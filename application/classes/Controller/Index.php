<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Index extends Controller_Base {

	public function action_index()
	{
          
	}
        
        public function action_selected_panel() 
        {
            $home_panel = $this->request->post('home_panel');
            Session::instance()->set('home_panel', $home_panel);
            $this->view = array('success' => 'The panel is set: ' . $home_panel);
        }

}