<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller_Base 
{
    public function action_index()
    {
        if ($this->user) {
            $this->view->user = $this->user->as_array();
        }
        $this->set_meta(array(
            'title'         => 'Chronos Depot',
            'keywords'      => 'chronos, depot, welcome, page, time tracking, time sheet, timesheet, project, management, task, team, invoice',
            'description'   => 'Lightweight time tracking and project management tool. Welcome page.'
        ));
        $this->set_facebook_meta(array(
            'title'         => 'Chronos Depot',
            'type'          => 'website',
            'url'           => URL::site('', TRUE),
            'image'         => URL::site('', TRUE) . 'public/img/chronoslogo.png',
            'description'   => 'Lightweight time tracking and project management tool.'
        ));
    }

    public function action_contact()
    {
        if ($this->user && $this->user->theme === 'hack') {
            $this->view->hack_map = true;
        }
        $this->set_meta(array(
            'title'         => 'Chronos Depot',
            'keywords'      => 'chronos, depot, welcome, page, time tracking, time sheet, timesheet, project, management, task, team, invoice',
            'description'   => 'Lightweight time tracking and project management tool.'
        ));
        $this->add_css_file('contact.css');
        $this->set_facebook_meta(array(
            'title'         => 'Chronos Depot',
            'type'          => 'website',
            'url'           => URL::site('', TRUE),
            'image'         => URL::site('', TRUE) . 'public/img/chronoslogo.png',
            'description'   => 'Lightweight time tracking and project management tool. Contact page.'
        ));
    }

    public function action_terms()
    {
    }

    public function action_privacy()
    {
    }

    public function action_selected_panel()
    {
        $home_panel = $this->request->post('home_panel');
        Session::instance()->set('home_panel', $home_panel);
        $this->view = array('success' => 'The panel is set: ' . $home_panel);
    }

}