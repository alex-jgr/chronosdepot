<?php
defined('SYSPATH') or die('No direct script access.');
/**
 * define keys for image types and purposes.
 */
return array(
    'some-image' => array(
        'path'  => DOCROOT . 'public/images/some-images',
        'adapter'  => 'File_Image',
        'extensions' =>  array('jpg', 'jpeg', 'gif'),
        'versions' => array(
            'thumb' => array(
                'width'     => 64, 
                'height'    => 64,
                'crop'      => TRUE,
                'quality'   => 100,
                'crop_x'    => NULL,
                'crop_y'    => NULL
            ),
            'normal' => array(
                'width'     => 640,
                'height'    => 480,
                'crop'      => TRUE,
                'quality'   => 100,
                'crop_x'    => NULL,
                'crop_y'    => NULL
            )
        ),
        'keep-original' => TRUE
    ),
    'user-image' => array(
        'path'  => DOCROOT . 'public/images/users',
        'adapter'  =>  'File_Image',
        'extensions' => array('jpg', 'jpeg', 'gif'),
        'versions' => array(
            'thumb' => array(
                'width'     => 64, 
                'height'    => 64,
                'crop'      => TRUE,
                'quality'   => 100,
                'crop_x'    => NULL,
                'crop_y'    => NULL            
            ),
            'normal' => array(
                'width'     => 640,
                'height'    => 480,
                'crop'      => TRUE,
                'quality'   => 100,
                'crop_x'    => NULL,
                'crop_y'    => NULL            
            ),
        ),
        'keep-original' => FALSE
    )
);