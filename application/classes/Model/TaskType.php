<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Description of TaskType
 *
 * @author Alexandru
 */
class Model_TaskType extends ORM
{
    protected $_table_name      = 'task_types';

    protected $_table_columns   = array(
        'id'          => array('type' => 'int'),
        'user_id'     => array('type' => 'int'),
        'name'        => array('type' => 'string'),
        'description' => array('type' => 'string',  'null' => TRUE),
        'created'     => array('type' => 'int'),
    );
    
    protected $_has_many = array(
        'positions' => array('model' => 'Position', 'through' => 'positons_task_types', 'foreign_key' => 'task_type_id', 'far_key' => 'positon_id'),
        'experts'   => array('model' => 'User',     'through' => 'users_task_typess',   'foreign_key' => 'task_type_id', 'far_key' => 'user_id')
    );
    
    protected $_belongs_to = array(
        'user' => array('model' => 'User', 'foreign_key' => 'user_id')
    );
    
    public static function get_task_types($user_id, $associative_column = NULL)
    {
        return DB::select('id', 'user_id', 'name', 'description', 'created')
                ->from('task_types')
                ->where('user_id', '=', $user_id)
                ->order_by('created', 'ASC')
                ->execute()->as_array($associative_column);
    }
    
    public static function get_task_type_positions($task_type_id)
    {
        return DB::select('id', 'user_id', 'name', 'description', 'created')
                ->from('positions_task_types')
                ->join('positions')->on('positions.id', '=', 'positions_task_types.position_id')
                ->where('task_type_id', '=', $task_type_id)
                ->execute()->as_array();
    }
}
