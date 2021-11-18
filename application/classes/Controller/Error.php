<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Description of Controller_Error
 *
 * @author Alexandru
 */
class Controller_Error extends Controller_Base 
{
    public function action_index()
    {
        $this->view->message = Session::instance()->get_once('error_message');
    }
}