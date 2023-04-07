<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Description of Task
 *
 * @author Alexandru
 */
class Model_Task extends ORM
{
    protected $_table_name      = 'tasks';

    protected $_table_columns   = array(
        'id'            => array('type' => 'int'),
        'parent_id'     => array('type' => 'int',     'null' => TRUE),
        'project_id'    => array('type' => 'int'),
        'level'         => array('type' => 'int'),
        'task_type_id'  => array('type' => 'int',     'null' => TRUE),
        'user_id'       => array('type' => 'int'),
        'created'       => array('type' => 'int'),
        'goal'          => array('type' => 'string'),
        'estimate'      => array('type' => 'int',     'null' => TRUE),
        'work'          => array('type' => 'int'),
        'budget'        => array('type' => 'int',     'null' => TRUE),
        'spent'         => array('type' => 'int',     'null' => TRUE),
        'status'        => array('type' => 'string',  'null' => TRUE),
        'description'   => array('type' => 'string',  'null' => TRUE),
        'progress'      => array('type' => 'int',     'null' => TRUE),
    );

    protected $_has_many = array (
        'users'       => array('model' => 'User', 'through' => 'users_tasks', 'foreign_key' => 'task_id', 'far_key' => 'user_id'),
        'worklogs'    => array('model' => 'Worklog',    'foreign_key' => 'task_id'),
        'subtasks'    => array('model' => 'Task',       'foreign_key' => 'parent_id'),
    );
    
    protected $_belongs_to = array(
        'owner'         => array('model' => 'User',         'foreign_key'   => 'user_id'),
        'project'       => array('model' => 'Project',      'foreign_key'   => 'project_id'),
        'task_type'     => array('model' => 'TaskType',     'foreign_key'   => 'task_type_id'),
        'status_icon'   => array('model' => 'StatusIcon',   'foreign_key'   => 'status'),
        'parent'        => array('model' => 'Task',         'foreign_key'   => 'parent_id')
    );

    /**
     * 
     * @param type $project_id
     * @param type $user_id if it is set, then only retun the tasks to which the user is assigned
     * @return type
     */
    
    public static function common_query($project_id = NULL)
    {
        $sql = DB::select(
                    'tasks.id', 'tasks.parent_id','project_id', 'task_type_id', 'tasks.user_id', 'tasks.created', 'goal', 'estimate', 'tasks.budget', 'tasks.description', 'tasks.status', 'level', 'has_children', 'icon', 'spent', 'progress',
                    DB::expr('tasks.work AS work_seconds, projects.name AS project_name'),
                    DB::expr('TIME_FORMAT(SEC_TO_TIME(`tasks`.`estimate`), \'%H\') AS `estimate_hours`'),//FROM_UNIXTIME(`tasks`.`estimate`, \'%H\')
                    DB::expr('TIME_FORMAT(SEC_TO_TIME(`tasks`.`estimate`), \'%i\') AS `estimate_minutes`'), //FROM_UNIXTIME(`tasks`.`estimate`, \'%i\')
                    DB::expr('TIME_FORMAT(SEC_TO_TIME(`tasks`.`estimate`), \'%H:%i\') AS `estimate`'),
                    DB::expr('TIME_FORMAT(SEC_TO_TIME(`tasks`.`work`), \'%H:%i\') AS `duration`'),
                    DB::expr('`tasks`.`estimate` AS estimate_seconds')
                )
                ->from('tasks')
                ->join(DB::expr('(SELECT count(id) AS has_children, parent_id FROM tasks GROUP BY parent_id) as children_tasks_count'), 'LEFT')->on('children_tasks_count.parent_id', '=', 'tasks.id')
                ->join('projects')->on('tasks.project_id', '=', 'projects.id')
                ->join('status_icons')->on('tasks.status', '=', 'status_icons.status');
        
        if ($project_id) {
            $sql->where('project_id', '=', $project_id);
        }
        
        return $sql;
    }

    public static function get_task_data($task_id)
    {
        $query = self::common_query();
        $query->where('tasks.id', '=', $task_id);
        $tasks = $query->execute()->as_array();
        if (isset($tasks[0]['id'])) {
            return $tasks[0];
        }
    }

    public static function get_sub_tasks($project_id, $parent_id = NULL, $user_id = NULL)
    {
        $query = self::common_query($project_id);
         
        if ($user_id) {
            $query->join('task_users')->on('tasks_users.task_id', '=', 'tasks.id')
                    ->where('tasks_users.user_id', '=', $user_id);                    
        }

        if ($parent_id) {
            $query->where('tasks.parent_id', '=', $parent_id);
        } else {
            $query->where('tasks.parent_id', 'IS', DB::expr('NULL'));
        }
        
        return $query->execute()->as_array();
    }
    
    public function disable($recurring = FALSE)
    {
        $this->status = 'disabled';
        $this->save();
        
        if ($recurring) {
            // Now disable all the subtasks of this task
            $subtasks = $this->subtasks->find_all();
            foreach($subtasks as $subtask) {
                $subtask->disable($recurring);
            }
        }
    }
    
    /**
     * 
     * @param type $cdosm - common difference of successive members. For more details: http://en.wikipedia.org/wiki/Arithmetic_progression
     */
    public function updateRecurringEstimate($cdosm)
    {
        $this->estimate = $this->estimate + $cdosm;
        $this->save();
        if (!empty($this->parent_id)) {
            $this->parent->updateRecurringEstimate($cdosm);
        }
    }
    
    public function updateRecurringBudget($cdosm)
    {
        $this->budget = $this->budget + $cdosm;
        $this->save();
        if (!empty($this->parent_id)) {
            $this->parent->updateRecurringBudget($cdosm);
        }
    }
    
    public function updateRecurringSpent($cdosm)
    {
        $this->spent = $this->spent + $cdosm;
        $this->save();
        if (!empty($this->parent_id)) {
            $this->parent->updateRecurringSpent($cdosm);
        }
    }
    
    public function updateRecurringWork($cdosm)
    {
        $this->work = $this->work + $cdosm;
        $this->spent = $this->work/3600 * $this->project->wage;
        $this->save();
        if (!empty($this->parent_id)) {
            $this->parent->updateRecurringWork($cdosm);
        }
    }

    public function finishParents() {
       if ($this->parent->id && !$this->parent->subtasks->where('status', '=', 'ongoing')->count_all()) {
           $this->parent->status = 'finished';
           $this->parent->progress = 100;
           $this->parent->save();
           $this->parent->finishParents();
       }
        
    }
    
    public function enableParents() {
        if ($this->parent->id) {
           $this->parent->status = 'ongoing';
           $this->parent->enableParents();
        }
    }

    public function enable($recurring = FALSE)
    {
        $this->status = 'ongoing';
        $this->save();
        
        if ($recurring) {
            // Now enable all the subtasks of this task
            $subtasks = $this->subtasks->find_all();
            foreach($subtasks as $subtask) {
                $subtask->enable($recurring);
            }
        }
    }
    
    public function finish($recurring = FALSE) 
    {
        $this->status = 'finished';
        $this->progress(100);
        $this->save();
        
        if ($recurring) {
            // Now finish all the subtasks of this task
            $subtasks = $this->subtasks->find_all();
            foreach($subtasks as $subtask) {
                $subtask->finish($recurring);
            }
        }
    }
    
    public function task_type($task_type_id) 
    {
        $this->task_type_id = $task_type_id;
        $this->save();
        foreach ($this->subtasks->find_all()->as_array() as $subtask) {
            $subtask->task_type($task_type_id);
        }
    }
    
    public static function get_users($task_id)
    {
        return DB::select('users.id', 'users.username', 'users.firstname', 'users.lastname')
                ->from('users')
                ->join('users_tasks')->on('users.id', '=', 'users_tasks.user_id')
                ->where('users_tasks.task_id', '=', $task_id)
                ->execute()->as_array();
    }
    
    public static function get_user_tasks($user_id, $project_id = NULL, $parent_id = NULL)
    {
        $sql = self::common_query($project_id)
                ->select(DB::expr('worklogs.id AS worklog_id'))
                ->join('users_tasks')->on('users_tasks.task_id', '=', 'tasks.id')
                ->join('worklogs', 'LEFT')
                    ->on('worklogs.task_id', '=', 'tasks.id')
                    ->on('worklogs.user_id', '=', DB::expr($user_id))
                    ->on('worklogs.active', '=', DB::expr('1'))
                ->where('users_tasks.user_id', '=', $user_id);
        if ($parent_id) {
            $sql->where('tasks.parent_id', '=', $parent_id);
        } else {
            $sql->where('tasks.parent_id', 'IS', DB::expr('NULL'));
        }
        return $sql->select(DB::expr('IF(tasks.status LIKE \'ongoing\', \'disabled\', NULL) AS action_button'))
                ->where('tasks.status', 'NOT LIKE', 'disabled')
                ->order_by('action_button','desc')
                ->order_by('has_children','desc')
                ->execute()->as_array();
    }
    
    public static function get_progress($task_id)
    {
        if (!is_numeric($task_id)) {
            throw new Exception('The task id must be a number');
        }
        $progress = DB::select(DB::expr('ROUND(AVG(progress), 2) AS avgProgress'))
                ->from('tasks')
                ->where('parent_id', '=', $task_id)
                ->execute()->as_array();
        if (isset($progress[0]['avgProgress'])) {
            return $progress[0]['avgProgress'];
        } else {
            return 0;
        }
    }


    public static function get_user_active_tasks($user_id) {
         $sql = self::common_query()
                ->select(DB::expr('worklogs.id AS worklog_id'))
                ->join('users_tasks')->on('users_tasks.task_id', '=', 'tasks.id')
                ->join('worklogs')
                    ->on('worklogs.task_id', '=', 'tasks.id')
                    ->on('worklogs.user_id', '=', DB::expr($user_id))
                    ->on('worklogs.active', '=', DB::expr('1'))
                ->where('users_tasks.user_id', '=', $user_id);

        return $sql->select(DB::expr('IF(tasks.status LIKE \'ongoing\', \'disabled\', NULL) AS action_button'))
                ->execute()->as_array();
    }


    public static function get_parents($parent_id, &$parents = array())
    {
        $parent = DB::select('id', 'goal', 'project_id', 'parent_id')
                ->from('tasks')
                ->where('id', '=', $parent_id)
                ->execute()->as_array();
       
        if (!empty($parent[0])) {
            $parents[] = $parent[0];
            self::get_parents($parent[0]['parent_id'], $parents);
        } else {
            return $parents;
        }
    }
    
    public function averageProgress()
    {
        $progress = DB::select(DB::expr('AVG(progress) AS avgProgress'))
                ->from('tasks')
                ->where('parent_id', '=', $this->id)
                ->execute()->as_array();
        if (isset($progress[0]['avgProgress'])) {
            return $progress[0]['avgProgress'];
        } else {
            return 0;
        }
    }

    public function progress($percentage)
    {
        if (is_numeric($percentage) && ($percentage <= 100)) {
            $this->progress = round($percentage);
            $this->save();
            if ($this->parent_id) {
                $progress = $this->parent->averageProgress();
                $this->parent->progress($progress);
            }
        } else {
            return $this->progress;
        }
    }

    public function set_default_users()
    {
        //first remove all assignments for this task
        DB::delete('users_tasks')->where('task_id', '=', $this->id)->execute();
        
        if ($this->parent_id && (!$this->users->count_all())) {
            foreach ($this->parent->users->find_all()->as_array() as $user) {
                if (!$this->has('users', $user)) {
                    if ($this->task_type_id) {
                        if ($user->has('task_types', $this->task_type)) {
                            $this->add('users', $user);
                        }
                    } else {
                        $this->add('users', $user);
                    }
                }
            }
        } else {
            foreach ($this->project->teams->find_all() as $team) {
                foreach ($team->members->find_all() as $user) {
                    if (!$this->has('users', $user)) {
                        if ($this->task_type) {
                            if ($user->has('task_types', $this->task_type)) {
                                $this->add('users', $user);
                            }
                        } else {
                            $this->add('users', $user);
                        }
                    }
                }
            }
        }
        
        // Finally adding the creator of the task to the task users
        if (!$this->has('users', $this->owner)) {
            $this->add('users', $this->owner);
        }
    }
    
    public function update_users($user_ids)
    {
        if (is_array($user_ids)) {
            foreach ($user_ids as $id) {
                if (!is_numeric($id)) {
                    throw new Exception('User id must be an integer "' . $id . '" given.');
                }
            } 

            $assigned_user_ids = array_keys(
                    DB::select('user_id')
                    ->from('users_tasks')
                    ->where('task_id', '=', $this->id)
                    ->execute()->as_array('user_id', 'user_id')
            );

            //using array functions in order to execute as little queries as possible;

            $new_users  = array_diff($user_ids, $assigned_user_ids);
            $keep_users = array_intersect($user_ids, $assigned_user_ids);

            if (!empty($keep_users)) {
                $keep_users = '(' . implode(',', $keep_users) . ')';
                DB::delete('users_tasks')
                        ->where('task_id', '=', $this->id)
                        ->where('user_id', 'NOT IN', DB::expr($keep_users))
                        ->execute();
            }

            if (!empty($new_users)) {
                foreach ($new_users as $user_id) {
                    $this->add('users', ORM::factory('User', $user_id));
                }
            }
        } else {
            $this->release_users();
        }
    }


    public function release_users()
    {
        return DB::delete('users_tasks')->where('task_id', '=', $this->id)->execute();
    }
}
