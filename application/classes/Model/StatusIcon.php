<?php defined('SYSPATH') OR die('No direct access allowed.');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of StatusIcon
 *
 * @author Alexandru
 */
class Model_StatusIcon extends ORM
{
    protected $_table_name      = 'status_icons';
    protected $_primary_key     = 'status';
    protected $_table_columns   = array(
        'status'    => array('type' => 'string'),
        'icon'      => array('type' => 'string')
    );
}
