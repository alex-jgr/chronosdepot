<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Description of Booking
 *
 * @author admin
 */
class Calendar_Booking extends ORM
{
    protected $_table_name = 'bookings';
    
    protected $_table_columns = array(
        'id'            => array('type' => 'int'),
        'user_id'       => array('type' => 'int'),
        'available_id'  => array('type' => 'int'),
        'date'          => array('type' => 'string'),
        'start'         => array('type' => 'int'),
        'end'           => array('type' => 'int'),
    );
    
    protected $_belongs_to = array(
        'user'      => array('model' => 'User',         'foreign_key' => 'user_id'),
        'available' => array('model' => 'Available',    'foreign_key' => 'available_id')
    );
}
