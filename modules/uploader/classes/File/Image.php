<?php
defined('SYSPATH') or die('No direct script access.');

class File_Image implements File_Adapter
{
    protected $_config;
    protected $_type;
        
    public function __construct($config)
    {
        $this->_config = $config;
    }
    
    /**
     * 
     * @param array $file compulsory keys: 'name' and 'path'
     * @param array $version defined in config
     * @param string $version_name given by the version key in the config versions array
     */
    public function make_version($file, $version, $version_name)
    {
        $image = Image::factory($file['path']);
        
        if ($image->width > $version['width']) {
            $image->resize($version['width'], $version['height'], Image::INVERSE)->crop($version['width'], $version['height'], $version['crop_x'], $version['crop_y']);
        }
        
        $dir =  $this->_config['path'] . DIRECTORY_SEPARATOR . $version_name;
        
        if (!is_dir($dir)) {
            mkdir($dir, 0755, TRUE);
        }
        
        $new_path = $dir . DIRECTORY_SEPARATOR . $file['name'];
        $image->save($new_path, 100);
        
        return array(
            'name'          => $file['name'],
            'path'          => $new_path,
            'public_path'   => $this->_config['public-path'] . $version_name .DIRECTORY_SEPARATOR . $file['name'],
            'width'         => $image->width,
            'height'        => $image->height,
            'created'       => time(),
            'quality'       => 100
        );
    }
    
    public function save_original($file)
    {
        if (!is_dir($this->_config['path'])) {
            mkdir($this->_config['path'], 0755, TRUE);
        }
        
        $upload = Upload::save($file, NULL, $this->_config['path']);

        if ($upload) {
            $filename   = strtolower(Text::random('alnum', 10)) . time() . Auth::instance()->get_user() . '.' . $this->_type; //making sure the file will have a unique name
            $image      = Image::factory($upload);
            $path       = $this->_config['path'] . DIRECTORY_SEPARATOR .$filename;
            
            $image->save($path, 100);
            
            unlink($upload);
            
            return array(
                'name'          => $filename,
                'path'          => $path,
                'public_path'   => $this->_config['public-path'] . DIRECTORY_SEPARATOR . $filename,
                'width'         => $image->width,
                'height'        => $image->height,
                'created'       => time(),
                'quality'       => 100
            );
        }
    }
    
    public function process_upload($file)
    {
        $this->_type = pathinfo($file['name'], PATHINFO_EXTENSION);
        
        $image = array('original' => $this->save_original($file));
        
        if (!empty($image['original'])) {
            
            foreach ($this->_config['versions'] as $name => $version) {
                $image['versions'][$name] = $this->make_version($image['original'], $version, $name);
            }
            
            if (empty($this->_config['keep-original'])) {
                unlink($image['original']['path']);
                $image['original'] = NULL;
            }
            
            return $image;
        }
    }
}