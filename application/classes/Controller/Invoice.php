<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Description of Test
 *
 * @author Alex
 */
class Controller_Invoice extends Controller_Base
{
    public function before()
    {
        parent::before();
        $this->add_js_file('moment.min.js');
        $this->view->date_format = $this->user->date_format;
    }
    public function action_templates()
    {
        
    }
    
    public function action_template()
    {
        $template   = new Model_Invoice_Template($this->request->param('id'));
        if ($template->id && $template->supplier_id !== $this->user->id) {
            die('Access denied.');
        }
        $this->add_js_file('spells/templates.js');
        $name       = $this->request->post('name');
        
        // For automatic filling in case the user wants to clone some info from other templates
        $this->view->templates = Model_Invoice_Template::get_user_templates($this->user->id, TRUE);
        $this->view->currencies= Model_Currency::get_all();
        
        if ($name) {
            $properties = $template->table_columns();
            
            unset ($properties['id']);
            
            foreach ($properties as $property => $attributes) {
                if ($property !== 'id') {
                    $template->$property = $this->request->post($property);
                }
            }
            $template->supplier_id = $this->user->id;
            $template->save();
            $this->redirect('invoice/template/'.$template->id);
        }
        
        if ($template->id) {
            $this->view->template = $template->as_array();
        }
    }
    
    public function action_delete_template()
    {
        
    }

    public function action_generate()
    {
        
    }
    
    public function action_lines()
    {
        $project = new Model_Project($this->request->param('id'));
        if ($project->user_id !== $this->user->id) {
            $this->not_allowed();
        }
        $this->view->project = $project->as_array();
        
        $start      = $this->request->post('start');
        $stop        = $this->request->post('stop');
        
        if (!$start) {
            $last_invoice = $project->last_invoice();
            if ($last_invoice->created) {
                $start = $last_invoice->created;
            }
        }
        
        if (!$stop) {
            $stop = time();
        }
        
        $this->view->sums = $project->task_sums(TRUE, $start, $stop);
        
        $this->view->start       = $start;
        $this->view->stop        = $stop;
        $this->view->date_format = $this->user->date_format;
        $this->main_view->home_panel = 'owned-projects';
        $this->view->template = 'tasks';
        $this->view->currency = $project->currency->code;
        
        $this->add_js_file('spells/invoices.js');
        $this->add_template('invoice');
    }

    public function action_download()
    {
        $project = new Model_Project($this->request->param('id'));
        $start  = $this->request->post('start');
        $stop   = $this->request->post('stop');
        $sums   = $project->task_sums(TRUE, $start, $stop);
    }
    
    public function action_cancel()
    {
       
    }

    public function action_delete()
    {
        
    }
    
    public function action_correct()
    { 
        $tasks = DB::select(DB::expr('id, goal, project_id, worklogs_count, task_duration'))
                ->from('tasks')
                ->join(DB::expr('(SELECT task_id, COUNT(worklogs.id) AS worklogs_count, SUM(duration) AS task_duration FROM worklogs WHERE active = 0 GROUP BY task_id) AS worklogs'))->on('worklogs.task_id', '=', 'tasks.id')
                ->execute()->as_array();
        
//        foreach ($tasks as $task) {
//            $worklogs = ORM::factory('Worklog')->where('task_id', '=', $task['id'])->find_all()->as_array();
//            foreach ($worklogs as $worklog) {
//                $wage = ORM::factory('Project_Wage')
//                        ->where('user_id', '=', $worklog->user_id)
//                        ->where('project_id', '=', $worklog->task->project_id)
//                        ->find();
//                $worklog->budget_spent = $worklog->duration * $wage->wage/3600;
//                $worklog->save();
//            }
//        }
        
//        foreach($tasks as $t) {
//            $task = new Model_Task($t['id']);
//            if ($task->id) {
//                $cdosm = $t['task_duration'] - $task->work;
//                echo '<br /> ' . $cdosm . 's || Project: ' . $t['project_id'] . ' Goal -> '. $t['goal'] . ' task: ' . $t['id'];
//                $task->updateRecurringWork($cdosm);
//                $spent = ($task->project->wage / 3600) * $t['task_duration'];
//                $cdosm = $spent - $task->spent;
//                
//                $task->updateRecurringSpent($cdosm);
//            }
//        }
        die();
    }
}
