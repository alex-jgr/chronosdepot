<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Description of Currency
 *
 * @author admin
 */
class Model_Currency extends ORM
{
    protected $_table_name      = 'currencies';
    protected $_table_columns   = array(
        'id'    => array('type' => 'int'),
        'code'  => array('type' => 'string'),
    );
    
    public static function get_all()
    {
        return DB::select('currencies.*')
                ->from('currencies')
                ->execute()->as_array();
    }
}
