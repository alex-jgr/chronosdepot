<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Description of Project
 *
 * @author Alexandru
 */
class Model_Project extends ORM
{
    protected $_table_name      = 'projects';

    protected $_table_columns   = array(
        'id'          => array('type' => 'int'),
        'user_id'     => array('type' => 'int'),
        'customer_id' => array('type' => 'int'),
        'name'        => array('type' => 'string'),
        'budget'      => array('type' => 'int',     'null' => TRUE),
        'description' => array('type' => 'string',  'null' => TRUE),
        'created'     => array('type' => 'int'),
        'status'      => array('type' => 'string'),
        'picture'     => array('type' => 'string',  'null' => TRUE),
        'currency_id' => array('type' => 'int'),
        'wage'        => array('type' => 'float'),
        'template_id' => array('type' => 'int')
    );

    protected $_has_many = array (
        'users'       => array('model' => 'User',       'through'     => 'projects_users', 'far_key' => 'user_id', 'foreign_key' => 'project_id'),
        'tasks'       => array('model' => 'Task',       'foreign_key' => 'project_id'),
        'teams'       => array('model' => 'Team',       'through'     => 'projects_teams', 'far_key' => 'team_id', 'foreign_key' => 'project_id'),
        'positions'   => array('model' => 'Position',   'through'     => 'positions_projects', 'far_key' => 'position_id', 'foreign_key' => 'project_id'),
        'contacts'    => array('model' => 'User',       'through'     => 'project_contacts', 'far_key' => 'user_id', 'foreign_key' => 'project_id')
    );
    
    protected $_belongs_to = array(
        'owner'     => array('model' => 'User',     'foreign_key' => 'user_id'),
        'currency'  => array('model' => 'Currency', 'foreign_key' => 'currency_id'),
        'customer'  => array('model' => 'User',     'foreign_key' => 'customer_id'),
    );
    
    public static function get_user_tasks($project_id, $user_id)
    {
        return DB::select()
                ->from('tasks')
                ->join('users_tasks')->on('tasks.id', '=', 'users_tasks.task_id')
                ->where('project_id', '=', $project_id)
                ->where('users.id', '=', $user_id)
                ->execute()->as_array();
    }
    
    public function release_all_teams()
    {
        $this->release_all_users();
        return DB::delete('projects_teams')
            ->where('project_id', '=', $this->id)
            ->execute();
    }
    
    public function release_all_positions()
    {
        return DB::delete('positions_projects')
            ->where('project_id', '=', $this->id)
            ->execute();
    }
    
    public function release_all_users()
    {
        return DB::delete('projects_users')
            ->where('project_id', '=', $this->id)
            ->execute();
    }
    
    public function release_all_wages()
    {
        return DB::delete('projects_wages')
                ->where('project_id', '=', $this->id)
                ->execute();
    }


    public function assign_teams($team_ids = array(), $all_members = true)
    {
        foreach($team_ids as $team_id) {
            if (is_numeric($team_id)) {
                $team = new Model_Team($team_id);
                if (!$this->has('teams', $team)) {
                    $this->add('teams', $team);
                }
            }
            if ($all_members) {
                foreach($team->members->find_all()->as_array() as $member) {
                    if (!$this->has('users', $member)) {
                        $this->add('users', $member);
                        $this->project_wage($member->id);
                    }
                }
            }
        }
    }
    
    public function project_wage($user_id)
    {
        $wage = Model_Project_Wage::get_wage($this->id, $user_id);
        
        if (!$wage) {
            $wage = new Model_Project_Wage();
            $wage->project_id   = $this->id;
            $wage->user_id      = $user_id;
            $wage->save();
        }
    }

    public function delete()
    {
        $this->release_all_teams();
        $this->release_all_positions();
        $this->release_all_wages();
        $this->release_all_users();
        parent::delete();
    }
    
    public function get_users()
    {
        return Model_Project::get_all_users($this->id);
    }

    public static function get_all_users($project_id)
    {
        if (!$project_id) {
            return NULL;
        }
        //the complexity down there is to only get the latest wages because they may change in time and old worklogs should keep being related to 
        return DB::select(
                    'users.username',
                    'projects_wages.wage',
                    DB::expr('users.id AS user_id'),
                    DB::expr('image_versions.public_path AS image'),
                    DB::expr('projects_wages.id AS wage_id')
                )
                ->from('users')
                ->join('projects_users')
                        ->on('users.id', '=', 'projects_users.user_id')
                        ->on('projects_users.project_id', '=', DB::expr($project_id))
                ->join('projects_wages','left')
                        ->on('users.id', '=', 'projects_wages.user_id')
                        ->on('projects_wages.project_id', '=', DB::expr($project_id))                
                ->join('image_versions', 'left')
                        ->on('users.image_id', '=', 'image_versions.image_id')
                        ->on('image_versions.name', '=', DB::expr('\'thumb\''))
            ->execute()->as_array();
    }
    
    public function user_wage($user_id, $wage = NULL)
    {
        $wageObject = ORM::factory('Project_Wage')
                ->where('user_id', '=', $user_id)
                ->where('project_id', '=', $this->id)
                ->find();
        
        if ($wage !== NULL) {
            $wageObject->user_id    = $user_id;
            $wageObject->project_id = $this->id;
            $wageObject->wage       = $wage;
            $wageObject->save();
        }
        
        return $wageObject;
    }
    
    public static function get_project_totals($project_id)
    {
        $totals = DB::select(
                DB::expr('SUM(tasks.spent) AS total_spendings'), 
                DB::expr('SEC_TO_TIME(SUM(tasks.estimate)) AS total_estimate'),
                DB::expr('SEC_TO_TIME(SUM(tasks.work)) AS total_work'),
                DB::expr('ROUND(AVG(tasks.progress), 2) as project_progress'))
                ->from('tasks')
                ->where('tasks.level', '=', '0')
                ->where('tasks.project_id', '=', $project_id)
            ->execute()->as_array();
        $totals[0]['total_expenses'] = self::get_project_expenses($project_id);
//        echo '<pre>';
//        die(var_dump($totals));
        return $totals[0];
    }
    
    public static function get_project_expenses($project_id)
    {
        $wages = DB::select(DB::expr('SUM(worklogs.budget_spent) AS total_expenses'))
                ->from('worklogs')
                ->join('tasks')->on('worklogs.task_id', '=', 'tasks.id')->on('tasks.project_id', '=', DB::expr($project_id))
                ->execute()->as_array();
        return $wages[0]['total_expenses'];
    }

    public function get_totals()
    {
        return Model_Project::get_project_totals($this->id);
    }
    
    public function get_expenses()
    {
        return Model_Project::get_project_expenses($this->id);
    }
    
    public function progress()
    {
        $progress = DB::select(
                DB::expr('ROUND(AVG(tasks.progress), 2) as project_progress'))
                ->from('tasks')
                ->where('tasks.level', '=', '0')
                ->where('tasks.project_id', '=', $this->id)
            ->execute()->as_array();
        if (isset($progress[0]['project_progress'])) {
            return $progress[0]['project_progress'];
        } else {
            return 0;
        }
    }
    
     
    public function find_tasks($user_id, $keywords, $status)
    {
         $sql = Model_Task::common_query($this->id)
                ->select(DB::expr('worklogs.id AS worklog_id'))
                ->join('users_tasks')->on('users_tasks.task_id', '=', 'tasks.id')
                ->join('worklogs', 'LEFT')
                    ->on('worklogs.task_id', '=', 'tasks.id')
                    ->on('worklogs.user_id', '=', DB::expr($user_id))
                    ->on('worklogs.active', '=', DB::expr('1'))
                ->where('users_tasks.user_id', '=', $user_id);
        if ($keywords) {
            $sql->where('tasks.goal', 'LIKE', '%' . $keywords . '%');
        }
        if ($status) {
            $sql->where('tasks.status', '=', $status);
        }
        return $sql->select(DB::expr('IF(tasks.status LIKE \'ongoing\', \'disabled\', NULL) AS action_button'))
                ->where('tasks.status', 'NOT LIKE', 'disabled')
                ->order_by('action_button','desc')
                ->order_by('has_children','desc')
                ->execute()->as_array();
    }
    
    public function get_customer()
    {
        if (!$this->customer_id) {
	    if ($this->id) {
            	return DB::select(DB::expr('id as contact_id, email'))
                    ->from('project_contacts')
                    ->where('project_id', '=', $this->id)
                    ->where('type', '=', 'customer')
                    ->execute()->as_array();
	    } else {
		return NULL;
	    }
        } else {
            return $this->customer->as_array();
        }
    }
   
    public function get_filtered_worklogs($user_ids = NULL, $team_ids = NULL, $sum = NULL)
    {
        return Model_Project::get_project_filtered_worklogs($this->id, $user_ids, $team_ids, $sum);
    }
    
    public static function get_project_filtered_worklogs($project_id, $user_ids = NULL, $team_ids = NULL, $sum = NULL)
    {
        $query = Model_Worklog::common_query()
                ->select('teams_users.team_id')
                ->where('projects.id', '=', $project_id)
                ->join('teams_users')->on('users.id', '=', 'teams_users.user_id')
                ->join('projects_teams')->on('teams_users.team_id', '=', 'projects_teams.team_id')
                                        ->on('projects_teams.project_id', '=', 'projects.id');

        
        if (is_array($user_ids)) {
            $query->where('users.id', 'IN', Utilities::numeric_array_to_sql_in($user_ids));
        }
        
        if (is_array($team_ids)) {
                    $query->where('projects_teams.id', 'IN', Utilities::numeric_array_to_sql_in($team_ids))
                    ->group_by('worklogs.id');
        }
        
        return $query->execute();
    }
    
    public function recalculateWage()
    {
        $wage_pr_second = $this->wage / 3600;
        foreach($this->tasks->find_all()->as_array() as $task) {
            $task->spent = $wage_pr_second * $task->work;
            $task->save();
        }
    }
    
    public function last_invoice()
    {
        return ORM::factory('Invoice')->where('status', '!=', 'cancelled')->order_by('created', 'desc')->limit(1)->find();
    }
    
    public function task_sums($json = FALSE, $start_time = NULL, $stop_time = NULL)
    {
        $join = DB::select(
                'task_id', 
                'start_time', 
                'stop_time',
                DB::expr('SUM(`duration`) AS `task_duration`'),
                DB::expr('SUM(`budget_spent`) AS `spent`'))
                ->from('worklogs');
        if ($start_time) {
            $join->where('start_time', '>=', $start_time);
        }
        if ($stop_time) {
            $join->where('stop_time', '<=', $stop_time);
        }
        
        $join->group_by('task_id');
        
        $join_worklog_sums = DB::expr('(' . $join->compile() . ') AS activity');
        
        $sums = DB::select(
                    'tasks.id',
                    'goal',
                    'level',
                    'parent_id',
                    'activity.spent',
                    DB::expr('TIME_FORMAT(SEC_TO_TIME(`activity`.`task_duration`), \'%H:%i\') AS `readable_duration`')
                )
                ->from('tasks')
                ->join($join_worklog_sums)->on('activity.task_id', '=', 'tasks.id')
                ->where('tasks.project_id', '=', $this->id);                
        if ($json) {
            return json_encode(array('sums' => $sums->execute()->as_array()));
        } else {
            return $sums->execute()->as_array();
        }
    }
    
    public function task_type_sums()
    {
        
    }
}
