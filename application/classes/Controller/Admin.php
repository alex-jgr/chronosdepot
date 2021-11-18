<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin extends Controller_Base
{
    public function before()
    {
        parent::before();
        if (!$this->user->has('roles', ORM::factory('Role', array('name' => 'admin')))) {
            $this->redirect('welcome/index');
        }
    }
    
    public function action_statistics()
    {
        $this->view->users      = ORM::factory('User')->find_all()->as_array();
        $this->view->projects   = ORM::factory('Project')->find_all()->as_array();
    }
    
    public function action_as() {
        $user = new Model_User(intval($this->request->param('id')));
        Auth::instance()->force_login($user->email);
    }
}