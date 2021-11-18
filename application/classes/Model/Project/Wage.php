<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Description of WaGE
 *
 * @author admin
 */
class Model_Project_Wage extends ORM 
{
    protected $_table_name      = 'projects_wages';
    protected $_table_columns   = array(
        'id'            => array('type' => 'int'),
        'user_id'       => array('type' => 'int'),
        'project_id'    => array('type' => 'int'),
        'wage'          => array('type' => 'float')
    );
    
    protected $_belongs_to = array(
        'user'      => array('model' => 'User',     'foreign_key' => 'user_id'),
        'project'   => array('model' => 'Project',  'foreign_key' => 'project_id')
    );
    
    
    public static function get_wage_value($user_id, $project_id)
    {
        $result = DB::select('projects_wages.wage')
                ->from('projects_wages')
                ->where('user_id', '=', $user_id)
                ->where('project_id', '=', $project_id)
                ->execute()->as_array();
        if (isset($result[0])) {
            return $result[0]['wage'];
        } else {
            return 0;
        }
    }
    
    public static function get_wage($project_id, $user_id)
    {
        $result = DB::select('projects_wages.*')
                ->from('projects_wages')
                ->where('user_id', '=', $user_id)
                ->where('project_id', '=', $project_id)
                ->execute()->as_array();
        if (isset($result[0]['id'])) {
            return $result[0];
        } else {
            return false;
        }
    }
}
