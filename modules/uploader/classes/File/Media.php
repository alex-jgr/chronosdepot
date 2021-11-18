<?php
defined('SYSPATH') or die('No direct script access.');

class File_Media implements File_Adapter
{
    protected $_config;
        
    public function __construct($config)
    {
        $this->_config = $config;
    }
    
    public function save_original($file)
    {
        
    }
   
    public function process_upload($file)
    {
        
    }
}