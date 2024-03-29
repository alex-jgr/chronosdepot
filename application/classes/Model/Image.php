<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Description of Project
 *
 * @author Alexandru
 */
class Model_Image extends ORM
{
    protected $_table_name      = 'images';

    protected $_table_columns   = array(
        'id'          => array('type' => 'int'),
        'name'        => array('type' => 'string'),
        'path'        => array('type' => 'string'),
        'public_path' => array('type' => 'string'),
        'label'       => array('type' => 'string',  'null' => TRUE),
        'width'       => array('type' => 'int'),
        'height'      => array('type' => 'int'),
        'created'     => array('type' => 'int'),
    );
    
    protected $_has_many = array('versions' => array('model' => 'Image_Version', 'foreign_key' => 'image_id'));
    
    public function delete()
    {
        foreach($this->versions->find_all() as $version) {
            unlink($version->path);
            $version->delete();
        }
        
        if (is_file($this->path)){
            unlink($this->path);
        }
        return parent::delete();
    }
}
