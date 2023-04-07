<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Description of Available
 *
 * @author admin
 */
class Calendar_Available extends ORM 
{
    protected $_table_name = 'availables';
    
    protected $_table_columns = array(
        'id'            => array('type' => 'int'),
        'user_id'       => array('type' => 'int'),
        'date'          => array('type' => 'string'),
        'start'         => array('type' => 'int'),
        'end'           => array('type' => 'int'),
    );
    
    protected $_belongs_to = array(
        'user'      => array('model' => 'User',         'foreign_key' => 'user_id')
    );
}
