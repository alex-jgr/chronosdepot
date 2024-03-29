<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Description of Team
 *
 * @author Alexandru
 */
class Model_Team extends ORM
{
    protected $_table_name      = 'teams';

    protected $_table_columns   = array(
        'id'          => array('type' => 'int'),
        'name'        => array('type' => 'string'),
        'user_id'     => array('type' => 'int'),
        'description' => array('type' => 'string', 'null' => TRUE),
        'image_id'    => array('type' => 'string', 'null' => TRUE),
        'created'     => array('type' => 'int'),
        'public'      => array('type' => 'int'),
        'simple'      => array('type' => 'int')
    );
    
    protected $_has_many = array(
        'members'   => array('model' => 'User',     'through' => 'teams_users',     'foreign_key' => 'team_id', 'far_key' => 'user_id'),
        'projects'  => array('model' => 'Project',  'through' => 'projects_teams',  'foreign_key' => 'team_id', 'far_key' => 'project_id')
    );
    
    protected $_belongs_to = array(
        'user'      => array('model' => 'User', 'foreign_key' => 'user_id'),
        'image'     => array('model' => 'Image', 'foreign_key' => 'image_id'),
    );
    
    public static function get_members($team_id, $associative_column = NULL)
    {
        if (is_numeric($team_id)) {
            return DB::select('users.id', 'email', 'username', 'firstname', 'lastname')
                    ->from('teams_users')
                    ->join('users')->on('teams_users.user_id', '=', 'users.id')
                    ->where('teams_users.team_id', '=', $team_id)
                    ->execute()->as_array($associative_column);
        } else {
            return array();
        }
    }
    
    public static function get_user_teams($user_id)
    {
        return DB::select()
                ->from('teams')
                ->where('user_id', '=' ,$user_id)
                ->execute()->as_array();
    }
    
    public static function get_team_users($team_id)
    {
        return DB::select('username','firstname','lastname','email', 'users.id', DB::expr('teams_users.team_id as team_id'))
                ->from('users')
                ->join('teams_users')->on('teams_users.user_id', '=', 'users.id')
                ->where('teams_users.team_id', '=', $team_id)
                ->execute()->as_array();
    }
    
    public static function  get_user_membership($user_id, $manager_id)
    {
        return DB::select()
                ->from('teams_users')
                ->join('teams')->on('teams.id', '=', 'teams_users.team_id')
                ->where('teams.user_id', '=', $manager_id)
                ->where('teams_users.user_id', '=', $user_id)
                ->execute()->as_array();
    }

    public static function get_teams($user_id, $with_members = FALSE)
    {
        $teams = self::get_user_teams($user_id);
        if ($with_members) {
            foreach ($teams as &$team) {
                $team['users'] = self::get_team_users($team['id']);
            }
        }
        return $teams;
    }
    
    public static function get_user_public_teams($user_id)
    {
        return DB::select(DB::expr('teams.id, teams.name AS name, image_versions.public_path AS image'))
                ->from('teams')
                ->join('teams_users')->on('teams_users.team_id', '=','teams.id')
                ->join('image_versions', 'LEFT')->on('image_versions.image_id', '=', 'teams.image_id')->on('image_versions.name', '=', DB::expr('\'thumb\''))
                ->where('teams.public', '=', 1)
                ->where('teams_users.user_id', '=', $user_id)
                ->execute()->as_array();
    }
    
    public static function get_managed_teams($user_id, $member_user_id = NULL)
    {
        $sql = DB::select(DB::expr('teams.id, teams.name AS name, image_versions.public_path AS image'))
                ->from('teams')
                ->join('image_versions', 'LEFT')->on('image_versions.image_id', '=', 'teams.image_id')->on('image_versions.name', '=', DB::expr('\'thumb\''))
                ->where('teams.user_id', '=', $user_id);
        
        if ($member_user_id) {
            $sql->select(DB::expr('teams_users.user_id AS member'))
                    ->join('teams_users','left')->on('teams_users.team_id', '=', 'teams.id')->on('teams_users.user_id', '=', DB::expr($member_user_id));
        }
        
        return $sql->execute()->as_array();
    }
    
    public static function get_teams_for_project($user_id, $project_id)
    {
        return DB::select('teams.id', 'teams.name', DB::expr('image_versions.public_path AS image, projects_teams.project_id AS assigned'))
                ->from('teams')
                ->join('projects_teams', 'LEFT')->on('teams.id','=','projects_teams.team_id')->on('projects_teams.project_id', '=', DB::expr("'$project_id'"))
                ->join('image_versions', 'LEFT')->on('image_versions.image_id', '=', 'teams.image_id')->on('image_versions.name', '=', DB::expr('\'thumb\''))
                ->where('teams.user_id','=', $user_id)
                ->execute()->as_array();
    }
    
    public static function get_project_teams($project_id)
    {
        return DB::select('teams.id', 'teams.user_id', 'teams.name', 'teams.description', 'teams.public', 'teams.created')
         ->from('teams')
         ->join('projects_teams')->on('projects_teams.team_id', '=', 'teams.id')
         ->where('project_id', '=' ,$project_id)
         ->execute()->as_array();
    }
    
    public static function find_public_teams($name)
    {
        return DB::select('teams.id', 'teams.name', DB::expr('image_versions.public_path AS image'))
                ->from('teams')
                ->join('image_versions', 'LEFT')->on('image_versions.image_id', '=', 'teams.image_id')->on('image_versions.name', '=', DB::expr('\'thumb\''))
                ->where('teams.public', '=', '1')
                ->where('teams.name','LIKE', '%'.$name.'%')
                ->execute()->as_array();
    }
    
    public function get_all_members()
    {
        return DB::select(
                'teams_users.user_id', 
                'users.username',
                DB::expr('image_versions.public_path AS image')
            )
            ->from('users')
            ->join('teams_users')
                    ->on('users.id', '=', 'teams_users.user_id')
                    ->on('teams_users.team_id', '=', DB::expr($this->id))
            ->join('image_versions', 'left')
                    ->on('users.image_id', '=', 'image_versions.image_id')
                    ->on('image_versions.name', '=', DB::expr('\'thumb\''))
            ->execute()->as_array();
    }
    
    public function release_all_users()
    {
        return DB::delete('teams_users')
            ->where('team_id', '=', $this->id)
            ->execute();
    }
    
    public function release_all_requests()
    {
        return DB::delete('team_requests')
            ->where('team_id', '=', $this->id)
            ->execute();
    }
    
    public function cleanup_after($member)
    {
        foreach($this->projects->find_all()->as_array() as $project) {
            if ($project->has('users', $member)) {
                $project->remove('users', $member);
                foreach($project->tasks->find_all()->as_array() as $task) {
                    if ($task->has('users', $member)) {
                        $task->remove('users', $member);
                    }
                }
            }
        }
    }


    public function delete() {
        $this->release_all_users();
        $this->release_all_requests();
        parent::delete();
    }
}