<?php defined('SYSPATH') OR die('No direct access allowed.');

return array(
    'guest' => array(
            '/' => array(
                'class' => 'clearfix',
                'id'    => 'menu-first',
                'icon'  => 'glyphicon glyphicon-time',
                'name'  => 'Chronos Depot',
                'route' => '/'
            ),
            '/welcome/contact' => array(
                'class' => '',
                'id'    => 'menu-contact',
                'icon'  => 'glyphicon glyphicon-phone-alt',
                'name'  => 'Contact',
                'route' => '/welcome/contact'
            ),
            '/user/login' => array(
                'class' => '',
                'name'  => 'Log in',
                'icon'  => 'glyphicon glyphicon-log-in',
                'route' => '/user/login'
            ),
    ),
    
    'login' => array(
            '/' => array(
                'class' => 'clearfix',
                'id'    => 'menu-first',
                'icon'  => 'glyphicon glyphicon-time',
                'name'  => 'Chronos Depot',
                'route' => '/'
            ),
            '/welcome/contact' => array(
                'class' => '',
                'id'    => 'menu-contact',
                'icon'  => 'glyphicon glyphicon-phone-alt',
                'name'  => 'Contact',
                'route' => '/welcome/contact'
            ),
            '/user/notifications' => array(
                'class'   => '',
                'name'    => 'notifications',
                'route'   => '/user/notifications',
                'icon'    => 'glyphicon glyphicon-phone-send',
                'special' => NULL,
                'id'      => 'notifications-link'
            ),
            '/user/settings' => array(
                'class' => '',
                'name'  => '',
                'icon'  => 'glyphicon  glyphicon-cog',
                'route' => '/user/settings'
            ),
            '/user/logout' => array(
                'class' => '',
                'name'  => 'Log out',
                'icon'  => 'glyphicon glyphicon-log-out',
                'route' => '/user/logout'
            ),
    ),
    'admin' => array(
        '/' => array(
                'class' => 'clearfix',
                'id'    => 'menu-first',
                'icon'  => 'glyphicon glyphicon-time',
                'name'  => 'Chronos Depot',
                'route' => '/'
            ),
            '/welcome/contact' => array(
                'class' => '',
                'id'    => 'menu-contact',
                'icon'  => 'glyphicon glyphicon-phone-alt',
                'name'  => 'Contact',
                'route' => '/welcome/contact'
            ),
            '/user/notifications' => array(
                'class'   => '',
                'name'    => 'notifications',
                'route'   => '/user/notifications',
                'icon'    => 'glyphicon glyphicon-phone-send',
                'special' => NULL,
                'id'      => 'notifications-link'
            ),
            '/user/settings' => array(
                'class' => '',
                'name'  => '',
                'icon'  => 'glyphicon  glyphicon-cog',
                'route' => '/user/settings'
            ),
            '/admin/statistics' => array(
                'class' => '',
                'name'  => 'Statistics',
                'icon'  => 'glyphicon  glyphicon-cog',
                'route' => '/admin/statistics'
            ),
            '/user/logout' => array(
                'class' => '',
                'name'  => 'Log out',
                'icon'  => 'glyphicon glyphicon-log-out',
                'route' => '/user/logout'
            ),
    ),
);