<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Description of Version
 *
 * @author Alexandru
 */
class Model_Image_Version extends ORM
{
    protected $_table_name = 'image_versions';
    
    protected $_belongs_to = array(
        'image' => array('model' => 'Image', 'foreign_key' => 'image_id')
    );
    
    protected $_table_columns   = array(
        'id'          => array('type' => 'int'),
        'name'        => array('type' => 'string'),
        'path'        => array('type' => 'string'),
        'public_path' => array('type' => 'string'),
        'image_id'    => array('type' => 'int'),
        'width'       => array('type' => 'int'),
        'height'      => array('type' => 'int'),
        'created'     => array('type' => 'int'),
    );
}
