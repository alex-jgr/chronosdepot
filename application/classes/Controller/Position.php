<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Position extends Controller_Base 
{
    public function before()
    {
        parent::before();
        if (!Auth::instance()->get_user()) {
            $this->redirect('user/login');
        }
        $this->view = new View_Data();
    }
    
    public function action_task_type()
    {
        $task_type_id = $this->request->post('id');
        $task_type    = new Model_TaskType($task_type_id);
        $task_type->name = $this->request->post('name');
        $task_type->description = $this->request->post('description');
        
        if (empty($task_type->created)) {
            $task_type->created = time();
            $task_type->user_id = $this->user->id;
        }
        
        $task_type->save();
        
        $this->view = array(
            'task_type' => $task_type->as_array()
        );
    }

    public function action_position()
    {
        $position_id = $this->request->post('id');
        $position    = new Model_Position($position_id);
        $position->name = $this->request->post('name');
        $position->description = $this->request->post('description');
        
        if (empty($position->id)) {
            $position->created = time();
            $position->user_id = $this->user->id;
        }
        
        $position->save();
        
        $position = $position->as_array();
        $position['task_types'] = Model_Position::get_task_types($position['id']);
        
        $this->view = array('position' => $position);
    }
    
    public function action_get_tasks_and_positions()
    {
        $positions = Model_Position::get_positions($this->user->id, 'id');
        foreach ($positions as &$position) {
            $position['task_types'] = Model_Position::get_task_types($position['id']);
        }
        
        $this->view = array(
            'task_types' => Model_TaskType::get_task_types($this->user->id, 'id'),
            'positions'  => $positions
        );
    }

    public function action_manage()
    {
        $this->view->task_types = Model_TaskType::get_task_types($this->user->id);
        $this->view->positions  = Model_Position::get_positions($this->user->id);
        $this->main_view->home_panel = 'owned-teams';
        $this->js_files = array(
            array('name' => 'jquery-ui.min.js'),
            array('name' => 'jquery.ui.touch-punch.min.js'),
            array('name' => 'spells/common.js'),
            array('name' => 'spells/positions.js'),
        );
    }
    
    public function action_save_changes()
    {
        $positions = $this->request->post('positions');
        
        foreach($positions as $position) {
            $position_object = new Model_Position($position['id']);
            $position_object->remove_task_types();
            if (isset($position['task_types'])) {
                foreach ($position['task_types'] as $task_type) {
                    if (is_array($task_type) && (!empty($task_type['id']))) {
                        $task_type_object = new Model_TaskType($task_type['id']);
                        if ($task_type_object->user_id = $this->user->id) {
                            $position_object->add('task_types', $task_type_object);
                        }
                    }
                }
            }
        }
    }
}