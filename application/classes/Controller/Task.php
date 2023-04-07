<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Description of Task
 *
 * @author Alexandru
 */
class Controller_Task extends Controller_Base
{
    public function before()
    {
        parent::before();
        if (!Auth::instance()->get_user()) {
            $this->redirect('user/login');
        }
        $this->view = new View_Data();
    }
    
    public function action_users()
    {
        $task_id = $this->request->param('id');
    }
    
        
    public function action_change_status()
    {
        $task_id = $this->request->post("task_id");
        
        try {
            $task   = new Model_Task($task_id);
            if ($task->has('users', $this->user) || ($task->user_id == $this->user->id)) {
                $status = $this->request->post("task_status");

                switch ($status) {
                    case 'ongoing'  :  {
                        $task->enable();
                        $task->enableParents();
                    } break;
                    case 'disabled' : $task->disable(TRUE); break;
                    case 'finished' : $task->finish(TRUE);  break;
                }

                $this->view = array(
                    'success'   => TRUE,
                    'message'   => 'Task status sucessfully updated to ' . $task->status,
                    'icon'      => $task->status_icon->icon,
                    'totals'    => Model_Project::get_project_totals($task->project_id),
                    'task'      => $task->as_array()
                );
            }
        } catch (Exception $e) {
            $this->view = array(
                'error' => TRUE,
                'message' => $this->exceptionToAjax($e)
            );
        }
    }
    
    public function action_search()
    {
        $this->show_header_active_tasks = FALSE;
        $this->view->has_active_tasks   = TRUE;
        $project_id = $this->request->param('id');
        $project    = new Model_Project($project_id);
        if ($project->id && $project->has('users', $this->user)) {
            $keywords   = $this->request->post('keywords');
            $status     = $this->request->post('status');
            if ($status ==  'all') {
                $status = NULL;
            }
            $this->view->tasks           = $project->find_tasks($this->user->id, $keywords, $status);
            $this->view->parent_progress = $project->progress();
            $this->view->project_id      = $project->id;
            $this->view->project         = $project->as_array();
        }
        $this->template = 'project/work';
    }
    
    public function action_ammend()
    {
        
    }
}
