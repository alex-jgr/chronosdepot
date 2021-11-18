<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Description of User
 *
 * @author Alexandru
 */
class Model_User extends  Model_Auth_User 
{
    protected $_table_name      = 'users';

    protected $_table_columns   = array(
        'id'                    => array('type' => 'int'),
        'email'                 => array('type' => 'string'),
        'username'              => array('type' => 'string'),
        'headline'              => array('type' => 'string', 'null' => TRUE),
        'password'              => array('type' => 'string'),
        'logins'                => array('type' => 'int'),
        'last_login'            => array('type' => 'int',    'null' => TRUE),
        'fb_id'                 => array('type' => 'string', 'null' => TRUE),
        'firstname'             => array('type' => 'string'),
        'lastname'              => array('type' => 'string'),
        'description'           => array('type' => 'string', 'null' => TRUE),
        'phone'                 => array('type' => 'string'),
        'location'              => array('type' => 'string', 'null' => TRUE),
        'created'               => array('type' => 'int'),
        'image_id'              => array('type' => 'int',    'null' => TRUE),
        'background'            => array('type' => 'string', 'null' => TRUE),
        'theme'                 => array('type' => 'string', 'null' => TRUE),
        'wage'                  => array('type' => 'float',  'null' => TRUE),
        'currency_id'           => array('type' => 'int',    'null' => TRUE),
        'simple'                => array('type' => 'int'),
        'date_format'           => array('type' => 'string'),
        'firsttime'             => array('type' => 'int')
    );

    protected $_has_many = array (
        'roles'                 => array('model' => 'Role',     'through' => 'roles_users',         'foreign_key' => 'user_id', 'far_key' => 'role_id'),
        'owner_projects'        => array('model' => 'Project',  'through' => 'projects_users',      'foreign_key' => 'user_id', 'far_key' => 'project_id'),
        'owner_project_tasks'   => array('model' => 'Task',     'through' => 'users_tasks',         'foreign_key' => 'user_id', 'far_key' => 'task_id'),
        'teams'                 => array('model' => 'Team',     'through' => 'teams_users',         'foreign_key' => 'user_id', 'far_key' => 'team_id'),
        'task_types'            => array('model' => 'TaskType', 'through' => 'users_task_types',    'foreign_key' => 'user_id', 'far_key' => 'task_type_id'),
        
        'owned_projects'        => array('model' => 'Project',      'foreign_key' => 'user_id'),
        'owned_project_tasks'   => array('model' => 'Task',         'foreign_key' => 'user_id'),
        'worklogs'              => array('model' => 'Worklog',      'foreign_key' => 'user_id'),
        'managed_teams'         => array('model' => 'Team',         'foreign_key' => 'user_id'),
        'notifications'         => array('model' => 'Notification', 'foreign_key' => 'user_id'),
    );
    
    protected $_belongs_to = array(
        'image'     => array('model' => 'Image',    'foreign_key' => 'image_id'),
        'currency'  => array('model' => 'Currency', 'foreign_key' => 'currency_id')
    );
     
    public function get_notifications()
    {
        return DB::select('*')->from('user_notifications')
                ->where('user_id', '=', $this->id)
                ->where('status', '=', 'new')
                ->execute()->as_array();
    }

    public static function find_by_name($name)
    {
        return DB::select('username', 'picture_url', 'id')->from('users')
                ->where('username', 'like', "%{$name}%")
                ->execute()->as_array();
    }

    public function set_email($email, $reemail)
    {
        if (!empty($email) &&($email === $reemail)) {
            $this->email    = $email;
            $this->username = $email;
            return TRUE;
        } else {
             return FALSE;
        }
    }

    public function set_password($password, $repassword) 
    {
        if (!empty($password) && ($password === $repassword)) {
            $this->password = $password;
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public static function get_task_types($user_id, $owner_user_id)
    {
        if (is_numeric($user_id)) {
            return DB::select('id', 'name', 'description')
                    ->from('users_task_types')
                    ->join('task_types')->on('users_task_types.task_type_id', '=', 'task_types.id')
                    ->where('users_task_types.user_id', '=', $user_id)
                    ->where('task_types.user_id', '=', $owner_user_id)
                    ->execute()->as_array();
        } else {
            return NULL;
        }
    }
    
    public static function get_managed_users($user_id, $project_id = NULL)
    {
        $sql = DB::select(DB::expr('teams.*, users.* '))
                ->from('teams_users')
                ->join('users')->on('teams_users.user_id', '=', 'users.id')
                ->join('teams')->on('teams_users.team_id', '=', 'teams.id')
                ->where('teams.user_id','=',$user_id)
                ->group_by('users.id');
                
        
        if ($project_id) {
            $sql->join('projects_teams')->on('projects_teams.team_id', '=', 'teams.id')->where('projects_teams.project_id', '=', DB::expr($project_id));
        }
        
        return $sql->execute()->as_array();
    }
    
    public function get_owned_projects_array()
    {
        return DB::select(DB::expr('projects.*'))
                ->from('projects')
                ->where('projects.user_id', '=', $this->id)
                ->order_by('projects.id', 'desc')
                ->execute()->as_array();
    }

    public function get_owner_projects_array()
    {
        return DB::select(DB::expr('projects.*'))
                ->from('projects')
                ->join('projects_users')->on('projects_users.project_id', '=', 'projects.id')
                ->where('projects_users.user_id', '=', $this->id)
                ->group_by('projects.id')
                ->order_by('projects.id', 'desc')
                ->execute()->as_array();
    }
    
    public function get_supervised_projects_array()
    {
        return DB::select(DB::expr('projects.*'))
                ->from('projects')
                ->join('project_contacts')->on('project_contacts.project_id', '=', 'projects.id')
                ->where('project_contacts.user_id', '=', $this->id)
                ->group_by('projects.id')
                ->order_by('project_contacts.id', 'desc')
                ->execute()->as_array();
    }
    
    public function get_teams_array()
    {
        return DB::select(DB::expr('teams.*, image_versions.public_path AS image, image_versions.width, image_versions.height'))
                ->from('teams')
                ->join('teams_users')->on('teams_users.team_id', '=', 'teams.id')
                ->join('image_versions', 'left')->on('teams.image_id', '=', 'image_versions.image_id')->on('image_versions.name', '=', DB::expr('\'thumb\''))
                ->where('teams_users.user_id', '=', $this->id)
                ->order_by('teams.id', 'desc')
                ->execute()->as_array();
    }

    public function get_managed_teams_array()
    {
        return DB::select(DB::expr('teams.*, image_versions.public_path AS image, image_versions.width, image_versions.height'))
                ->from('teams')
                ->join('image_versions', 'left')->on('teams.image_id', '=', 'image_versions.image_id')->on('image_versions.name', '=', DB::expr('\'thumb\''))
                ->where('teams.user_id', '=', $this->id)
                ->order_by('teams.id', 'desc')
                ->execute()->as_array();
    }
    
    
    public function get_simple_team()
    {
        $team = $this->managed_teams->where('simple', '=', 1)->find();
        
        if (!$team->id) {
            $team->user_id      = $this->id;
            $team->name         = $this->username . '\'s team';
            $team->description  = 'This team was generated by Chronos Depot when the simple experience was selected by its owner in the account settings.';
            $team->created      = time();
            $team->public       = 0;
            $team->simple       = 1;
            $team->save();
            $team->add('members', $this);
        }
        return $team;
    }
}
