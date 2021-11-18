<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Description of Line
 *
 * @author admin
 */
class Model_Invoice_Line extends ORM
{
    protected $_table_name      = 'invoice_lines';
    
    protected $_table_columns   = array(
        'id'            => array('type' => 'int'),
        'invoice_id'    => array('type' => 'int'),
        'type'          => array('type' => 'string'),
        'task_id'       => array('type' => 'int'),
        'worklog_id'    => array('type' => 'int'),
        'task_type_id'  => array('type' => 'int'),
        'amount'        => array('type' => 'decimal'),
        'work_time'     => array('type' => 'int'),
        'message'       => array('type' => 'string')
    );
    
    protected $_belongs_to = array(
        'invoice'   => array('model' => 'Model_Invoice',    'foreign_key' => 'invoice_id'),
        'task'      => array('model' => 'Model_Task',       'foreign_key' => 'task_id'),
        'worklog'   => array('model' => 'Model_Worklog',    'foreign_key' => 'worklog_id'),
        'task_type' => array('model' => 'Model_TaskType',   'foreign_key' => 'task_type_id')
    );
}
