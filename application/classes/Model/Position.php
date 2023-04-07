<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Description of Position
 *
 * @author Alexandru
 */
class Model_Position extends ORM
{
    protected $_table_name      = 'positions';

    protected $_table_columns   = array(
        'id'          => array('type' => 'int'),
        'user_id'     => array('type' => 'int'),
        'name'        => array('type' => 'string'),
        'description' => array('type' => 'string',  'null' => TRUE),
        'created'     => array('type' => 'int'),
    );
    
    protected $_has_many = array(
        'task_types'    => array('model' => 'Position', 'through' => 'positions_task_types', 'foreign_key' => 'position_id', 'far_key' => 'task_type_id')
    );
    
    public static function get_positions($user_id, $associative_column = NULL)
    {
        if (is_numeric($user_id)) {
            return DB::select('id', 'user_id', 'name', 'description', 'created')
                    ->from('positions')
                    ->where('user_id', '=', $user_id)
                    ->order_by('created', 'ASC')
                    ->execute()->as_array($associative_column);
        } else {
            return array('error - user_id must be an integer');
        }
    }
    
    public static function get_task_types($position_id, $associative_column = NULL)
    {
        if (is_numeric($position_id)) {
            return DB::select('id', 'user_id', 'name', 'description', 'created')
                    ->from('task_types')
                    ->join('positions_task_types')->on('positions_task_types.task_type_id', '=', 'task_types.id')
                    ->where('position_id', '=', $position_id)
                    ->execute()->as_array($associative_column);
        } else {
            return array('error - position_id must be an integer');
        }
    }
    
    public function remove_task_types() {
        return DB::delete('positions_task_types')
                ->where('position_id', '=', $this->id)
                ->execute();
    }
}
