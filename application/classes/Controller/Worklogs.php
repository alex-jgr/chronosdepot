<?php
defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Description of Worklogs
 *
 * @author admin
 */
class Controller_Worklogs extends Controller_Base
{
    public function before()
    {
        parent::before();
        $this->add_js_file('moment.min.js');
        $this->add_js_file('spells/worklogs.js');
        $this->add_template('worklogs');
        $this->view->date_format = $this->user->date_format;
    }

    public function action_index()
    {
        $this->view->worklogs = Model_Worklog::get_personal_worklogs($this->user->id);
    }
    
    public function action_date()
    {
        die(date_default_timezone_get());
    }
    
    public function action_week()
    {
        
    }
    
    public function action_month()
    {
        
    }
    
    public function action_project()
    {
        $project_id = $this->request->param('id');
        $start      = $this->request->post('start_date');
        $end        = $this->request->post('end_date');
        $user_id    = $this->request->post('user_id');
        
        if ($start === 'all') {
            $start  = NULL;
            $end    = NULL;
        } else {
            if (!$start && !$end) {
                $start = strtotime(date('Y-m-01'));
                $end   = strtotime(date('Y-m-t'));
            }
        }
        
        // if the logged in user is the owner of the project he will be able to see the worklogs of all users
        if (is_numeric($project_id)) {
            $project = new Model_Project($project_id);
            if ($this->user->id == $project->user_id) {
                if (is_numeric($user_id)) {
                    $this->view->worklogs = json_encode(Model_Worklog::get_project_worklogs($project_id, $user_id, $start, $end));
                } else {
                    $this->view->worklogs = json_encode(Model_Worklog::get_project_worklogs($project_id, NULL, $start, $end));
                }
            } else {
                if ($project->has('users', $this->user)) {
                    $this->view->worklogs = json_encode(Model_Worklog::get_project_worklogs($project_id, $this->user->id, $start, $end));
                }
            }
            $this->view->users      = $project->get_users();
            $this->view->project    = $project->as_array();
            $this->view->teams      = Model_Team::get_project_teams($project->id);
            $this->view->start      = $start;
            $this->view->end        = $end;
            $this->view->date_format = $this->user->date_format;
            $this->main_view->home_panel = 'owned-projects';
            $this->view->template = 'owned';
            $this->view->currency = $project->currency->code;
        }
    }
    
    public function action_excel()
    {
        $project_id = intval($this->request->param('id'));
        $start      = $this->request->post('start_date');
        $end        = $this->request->post('end_date');
        
        $project = new Model_Project($project_id);
        
        if (($this->user->id == $project->user_id) || ($project->has('contacts', $this->user))) {
            if ($start === 'all') {
                $start  = NULL;
                $end    = NULL;
            } else {
                if (!$start && !$end) {
                    $start = strtotime(date('Y-m-01'));
                    $end   = strtotime(date('Y-m-t'));
                }
            }

            $spreadsheet = Model_Worklog::get_project_excel($project, $start, $end);
            $fileName = strtolower(preg_replace(array('/\s\s+/', '/\s/'), array(' ', '-'), $spreadsheet->getActiveSheet()->getTitle()));
            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $fileName . '.xlsx"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

            // If you're serving to IE over SSL, then the following may be needed
            header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0

            $objWriter = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        }
    }

    public function action_user()
    {
        $project_id = $this->request->param('id');
        $start      = $this->request->post('start_date');
        $end        = $this->request->post('end_date');
        
        if ($start === 'all') {
            $start  = NULL;
            $end    = NULL;
        } else {
            if (!$start && !$end) {
                $start = strtotime(date('Y-m-01'));
                $end   = strtotime(date('Y-m-t'));
            }
        }
        
        if (is_numeric($project_id)) {
            $project = new Model_Project($project_id);
            $this->view->worklogs = json_encode(Model_Worklog::get_project_worklogs($project->id, $this->user->id, $start, $end));
            $this->view->project  = $project->as_array();
        } else {
            $this->view->worklogs = json_encode(Model_Worklog::get_user_inactive_worklogs($this->user->id, $start, $end));
        }
        $this->main_view->home_panel = 'contracted-projects';
        $this->view->start    = $start;
        $this->view->end      = $end;
        $this->view->template = 'contracted';
    }
    
    public function action_supervise() 
    {
        $project_id = $this->request->param('id');
        $start      = $this->request->post('start_date');
        $end        = $this->request->post('end_date');
        
        if ($start === 'all') {
            $start  = NULL;
            $end    = NULL;
        } else {
            if (!$start && !$end) {
                $start = strtotime(date('Y-m-01'));
                $end   = strtotime(date('Y-m-t'));
            }
        }
        if (is_numeric($project_id)) {
            $project = new Model_Project($project_id);
            if ($project->has('contacts', $this->user)) {
                $this->view->worklogs = json_encode(Model_Worklog::get_project_worklogs($project_id, NULL, $start, $end));
                $this->main_view->home_panel = 'contracted-projects';
                $this->view->template = 'supervised';
                $this->view->start    = $start;
                $this->view->end      = $end;
                $this->view->project  = $project->as_array();
                $this->view->currency = $project->currency->code;
            }
        }
    }
    
    public function action_update()
    {
        $worklog_id = $this->request->param('id');
        $worklog = new Model_Worklog($worklog_id);
        
        if (($worklog->user_id) == $this->user->id || ($worklog->task->project->user_id == $this->user->id)) {
            $duration   = $this->request->post('duration');
            $dif        = $duration - $worklog->duration;
            $wage       = Model_Project_Wage::get_wage_value($worklog->user_id, $worklog->task->project_id);
//            $spendings  = ($duration/3600) * $wage;
//            $spent_dif  = $spendings - $worklog->budget_spent;
            $worklog->modified      = time();
            $worklog->budget_spent  = ($duration/3600) * $wage;
            $worklog->duration      = $duration;
            $worklog->stop_time     += $dif;
            $worklog->note          = $this->request->post('note');
            $worklog->save();
            $worklog->task->updateRecurringWork($dif);
//            $worklog->task->updateRecurringSpent($spent_dif);
            $this->view = array('success' => TRUE);
        }
    }
    
    public function action_delete()
    {
        $worklog_id = $this->request->param('id');
        $worklog = new Model_Worklog($worklog_id);
        
        if (($worklog->user_id) == $this->user->id || ($worklog->task->project->user_id == $this->user->id)) {
            $dif = -1 * $worklog->duration;
//            $spent = -1 * $worklog->budget_spent;
            $worklog->task->updateRecurringWork($dif);
//            $worklog->task->updateRecurringSpent($spent);
            
            $worklog->delete();
            
            $this->view = array('success' => TRUE);
        }
    }
}
