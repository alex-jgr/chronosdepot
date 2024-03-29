<?php
defined('SYSPATH') or die('No direct script access.');
/**
 * define keys for image types and purposes.
 */
return array(
    'team-image' => array(
        'path'  => DOCROOT . 'public/images/teams',
        'public-path' => '/public/images/teams/',
        'adapter'  => 'File_Image',
        'extensions' =>  array('jpg', 'jpeg', 'gif', 'png'),
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
        'keep-original' => FALSE
    ),
    'user-image' => array(
        'path'  => DOCROOT . 'public/images/users',
        'public-path' => '/public/images/users/',
        'adapter'  =>  'File_Image',
        'extensions' => array('jpg', 'jpeg', 'gif', 'png'),
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