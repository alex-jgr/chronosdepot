<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Description of ResetToken
 *
 * @author jgral
 */
class Model_ResetPassword extends ORM
{
    protected $_table_name = 'reset_passwords';
    protected $_belongs_to = array(
        'user' => array('model' => 'User', 'foreign_key' => 'user_id')
    );
    
    protected $_table_columns = array(
        'id'            => array('type' => 'int'),
        'user_id'       => array('type' => 'int'),
        'token'         => array('type' => 'string',    'null' => FALSE),
        'created'       => array('type' => 'int',       'null' => FALSE),
        'used'          => array('type' => 'int'),
        'expires'       => array('type' => 'int',       'null' => FALSE),
        'old_password'  => array('type' => 'string'),
        'status'        => array('type' => 'string',    'null' => FALSE)
    );
}
