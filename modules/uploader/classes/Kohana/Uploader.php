<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Description of Upload
 *
 * @author Alexandru
 */
class Kohana_Uploader
{
    /**
     * Load the configuration into this variable
     * @var array
     */
    protected $_config;
    protected $_file;
    protected $_file_object;
   
    /**
     * Create a new upload 
     * @param mixed $config string containing an array key to look for in the config file or an entire configuration array
     * @throws Kohana_Exception
     */
    public function __construct($config) 
    {
        if (is_string($config)) {
            $configuration  = Kohana::$config->load('uploader');
            $this->_config  = $configuration->get($config);
        } elseif (is_array($config)) {
            $this->_config = $config;
        } else {
            throw new Kohana_Exception('Expected string or array for argument: $config');
        }
       
        $this->_file_object = new $this->_config['adapter']($this->_config);
    }
    
    /*
     * returns a new object of Kohana_Upload
     */
    public static function factory($config)
    {
        return new Kohana_Uploader($config);
    }
    
    public function setFile($file)
    {
        $this->_file = $file;
    }
    
    
    public function validate() 
    {
        if ( !Upload::valid($this->_file) OR !Upload::not_empty($this->_file) OR !Upload::type($this->_file, $this->_config['extensions'])) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function process_upload($file = NULL) 
    {
        if ($file) {
            $this->setFile($file);
        }
        
        if ($this->validate()) {
            return $this->_file_object->process_upload($this->_file);
        } else {
            throw new Kohana_Exception('Ivalid file or bad file validation configuration');
        }
        
    }
    
}
