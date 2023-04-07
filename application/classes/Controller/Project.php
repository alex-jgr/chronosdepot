<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Description of Project
 *
 * @author Alexandru
 */
class Controller_Project extends Controller_Base
{
    public function before()
    {
        parent::before();
        if (!Auth::instance()->get_user()) {
            $this->redirect('user/login');
        }
        $this->view = new View_Data();
    }
    
    public function action_index()
    {
        $project_id = $this->request->param('id');
        if ($project_id) {
            $this->view();
        } else {
            $this->list_all();
        }
    }
    
    public function view()
    {
        $this->view = new View_Data();
    }
    
    public function action_edit($project_id = NULL)
    {
        if (!$project_id) {
            $project_id = $this->request->param('id');
        }
        $project = new Model_Project($project_id);
        if ($project->id && $project->user_id != $this->user->id) {
            $this->redirect('user/home');
            exit();
        }
        $this->view = $project->as_array();
        $this->view['project_id']   = $this->view['id'];
        $this->view['teams']        = Model_Team::get_teams_for_project($this->user->id, $project->id);
        $this->view['currencies']   = Model_Currency::get_all();
        $this->view['users']        = Model_Project::get_all_users($project->id);
        $this->view['contacts']     = Model_Project_Contact::active_contacts($project->id);
        $this->view['invites']      = Model_Project_Contact::pending_invites($project->id);
        $this->view['customer']     = $project->get_customer();
        $this->view['templates']    = Model_Invoice_Template::get_user_templates($this->user->id, TRUE);
        $this->add_template('project-edit');
        $this->add_js_file('spells/project-edit.js');
        $this->main_view->home_panel = 'owned-projects';
    }
    
    public function action_invite_contact()
    {
        $email      = $this->request->post('email');
        $project_id = $this->request->param('id');
        
        $project    = new Model_Project($project_id);
        
        if (!($this->user->id == $project->user_id || $this->user->id == $project->customer_id)) {
            $this->redirect('/user/home');
            exit();
        }
        $user       = ORM::factory('User', array('email' => $email));
        if (!$user->id) {
            $contact    = Model_Project_Contact::invite($email, $project->id, 'spectator');
        } else {
            $contact    = Model_Project_Contact::set_spectator($user->id, $project->id);
        }
        
        $this->view = array(
            'success'   => TRUE,
            'invites'   => Model_Project_Contact::pending_invites($project->id)
        );
    }
    
    public function action_cancel_contact()
    {
        $contact_id = $this->request->param('id');
        $contact    = new Model_Project_Contact($contact_id);
        $type = $contact->active ? 'contact' : 'invite';
        if (!($this->user->id == $contact->project->user_id || $this->user->id == $contact->project->customer_id)) {
            $this->redirect('/user/home');
            exit();
        }
        
        $contact->delete();
        
        $this->view = array(
            'success'   => TRUE,
            'type'      => $type
        );
    }

    public function action_save()
    {
        $project_id = $this->request->param('id');
        $project    = new Model_Project($project_id);
        if (!empty($project->user_id) && $project->user_id !== $this->user->id) {
            $this->redirect('user/home');
        } else {
            $email      = $this->request->post('main_contact');
            $customer   = NULL;
            if ($email) {
                $customer = ORM::factory('User', array('email' => $email));
                if (!$customer->id) {
                    Model_Project_Contact::invite($email, $project->id, 'customer');
                } else {
                    $project->customer_id = $customer->id;
                    Model_Project_Contact::set_spectator($customer->id, $project->id);
                }
            }
            $project->user_id       = Auth::instance()->get_user();
            $project->name          = $this->request->post('name');
            $project->description   = $this->request->post('description');
            $project->budget        = $this->request->post('budget');
            $project->wage          = $this->request->post('project-wage');
            $project->currency_id   = $this->request->post('currency_id');
            $project->template_id   = $this->request->post('template_id');
            $project->save();
            $recalculate = $this->request->post('recalculate');
            if ($recalculate) {
                $project->recalculateWage();
            }
            //if there is no project, then this is a new project and we have to add the admin as a person assigned to the project too
            if (is_null($project_id)) {
                $project->created = time();
                $project->add('users', $this->user);
            }
            
            $teams = $this->request->post('teams');
           
            $project->release_all_teams();
            if ($teams) {
                $project->assign_teams($teams);
            }
            $this->redirect('project/edit/' . $project->id);
        }
    }
    
    public function action_set_wages()
    {
        $project = new Model_Project($this->request->post('project_id'));
        $wages   = $this->request->post('wages');
        
        foreach ($wages as $wage) {
            $member_data = new Model_Project_Wage($wage['wage_id']);
            $member_data->wage          = $wage['wage'];
            $member_data->user_id       = $wage['user_id'];
            $member_data->project_id    = $project->id;
            $member_data->save();
        }
        $this->view = array(
            'success'   => TRUE,
            'message'   => 'The user wages were updated successfuly.'
        );
    }

    public function action_delete()
    {
        $project_id = $this->request->param('id');
        $project    = new Model_Project($project_id);
        if (!empty($project->user_id) && $project->user_id !== $this->user->id) {
             $this->redirect('user/home');
        } else {
            $project->delete();
            $this->redirect('user/home/owned-projects');
        }        
    }

    public function action_totals()
    {
        $project_id = $this->request->param('id');
        $project    = new Model_Project($project_id);
        if ($project->has('users', $this->user) || $project->user_id = $this->user->id) {
            $this->view->totals = $project->get_totals();
        } else {
            $this->view = array('error' => true, 'message' => 'Unauthorized action prevented.');
        }
    }


    public function action_operate()
    {
        $project_id = $this->request->param('id');
        $project    = new Model_Project($project_id);
        $this->view->project    = $project->as_array();
        $this->view->project_id = $project->id;
        $this->view->totals     = $project->get_totals();
        $this->view->currency   = $project->currency->code;
        $this->view->simple     = $this->user->simple;
        
        if ($project->has('users', $this->user)) {
            $this->view->tasks      = Model_Task::get_sub_tasks($project->id);
            $this->view->teams      = Model_Team::get_project_teams($project->id);
            $this->view->users      = Model_User::get_managed_users($this->user->id, $project->id);
            $this->view->task_types = Model_TaskType::get_task_types($this->user->id);
            
            foreach ($this->view->users as &$user) {
                $user['teams'] = Model_Team::get_user_membership($user['id'], $this->user->id);
            }
            //$this->view->tasks = $project->tasks->find_all()->as_array();
        }
        $this->main_view->home_panel = 'owned-projects';
        $this->add_js_file('spells/operate.js');
        
        $this->add_template('tasks');
    }


    public function action_operate_users()
    {
        $project_id = $this->request->param('id');
        $project    = new Model_Project($project_id);
        $this->view->project    = $project->as_array();
        
        if ($project->has('users', $this->user)) {
            $this->view->tasks = Model_Task::get_sub_tasks($project->id);
            $this->view->teams = Model_Team::get_teams($this->user->id);
            $this->view->users = Model_User::get_managed_users($this->user->id);

            foreach ($this->view->users as &$user) {
                $user['teams'] = Model_Team::get_user_membership($user['id'], $this->user->id);
            }
        }
        $this->add_js_file('spells/operate.js');
        
        $this->add_template('tasks');
        $this->template = 'project/operate';
    }
    
    public function action_task()
    {
        $task_id    = $this->request->post('task_id');
        $project_id = $this->request->post('project_id');
        
        $task       = new Model_Task($task_id);
        $project    = new Model_Project($project_id);
       
        if ($project->has('users', $this->user)) {
        
            $hours          = $this->request->post('estimate_hours');
            $minutes        = $this->request->post('estimate_minutes');
            $budget         = $this->request->post('budget');
            $task_type_id   = $this->request->post('task_type_id');
            
            $total_estimate = $hours * 3600 + $minutes * 60;
            
                        
            $task->parent_id            = $this->request->post('parent_id');
            $task->project_id           = $project_id;
            $task->goal                 = $this->request->post('goal');
            
            $task->description          = $this->request->post('description');
            $task->status               = $this->request->post('status');
            
            if ($task->parent_id) {
                if (!empty($task->estimate)) {
                    $task->updateRecurringEstimate($total_estimate - $task->estimate);
                } else {
                    $task->updateRecurringEstimate($total_estimate);
                }
                
                if (!empty($task->budget)) {
                    $task->updateRecurringBudget($budget - $task->budget);
                } else {
                    $task->updateRecurringBudget($budget);
                }                
            } else {
                $task->estimate = $total_estimate;
                $task->budget   = $budget;
            }

            if (!$task->user_id) {
                $task->user_id = $this->user->id; 
            }
            if (!$task->budget) {
                $task->budget = 0;
            }

            if (!$task->created) {
                $task->created = time();
            }
            
            if (!$task->work) {
                $task->work = 0;
            }
            
            if ($task->parent_id) {
                $parent = new Model_Task($task->parent_id);
                $task->level = $parent->level + 1;
            } else {
                $task->parent_id = NULL;
            }
            
            if ($task->level > 9) {
                $this->view = array(
                    'error'   => TRUE,
                    'message'   => 'Maximum subtask level allowed is 9'                    
                );
            } else {
                $task->save();
                if (!$task_id) {
                    // If this was not an edit add the default users.
                    $task->set_default_users();
                    $task->progress(0);
                }
                
                if (!is_numeric($task_type_id)) {
                    $task->task_type(NULL);
                } else {
                    $task->task_type($task_type_id);
                }
                
                $this->view = array(
                    'success'   => TRUE,
                    'message'   => 'Task added successfully',
                    'tasks'     => $task->as_array(),
                    'totals'    => $project->get_totals()
                );
                $this->view['tasks']['icon']         = $task->status_icon->icon;
                $this->view['tasks']['has_children'] = $task->subtasks->count_all();
                $this->view['tasks']['duration']     = gmdate('H:i', $task->work);
                $this->view['tasks']['work_seconds'] = $this->view['tasks']['work'];
            }
        } else {
            $this->view = array(
                'error'   => TRUE,
                'message'   => 'You have no permissions on this project'
            );
        }
    }
    
    public function action_task_data()
    {
        $task_id = intval($this->request->param('id'));
        $this->view = Model_Task::get_task_data($task_id);
    }


    public function action_subtasks()
    {
        $task_id = $this->request->post('id');
        $project_id = $this->request->post('project_id');
        $this->view->tasks = Model_Task::get_sub_tasks($project_id, $task_id);
    }
    
    public function action_delete_task()
    {
        $task_id    = $this->request->post("id");
        $task       = new Model_Task($task_id);
        $project    = $task->project;
        if ($task->project->has('users', $this->user)) {
            if ($task->worklogs->count_all() || $task->subtasks->count_all()) {
                $task->disable(TRUE);
                $this->view = array(
                    'warning'   => TRUE,
                    'message'   => 'The task and all its subtasks have been disabled. The task could not be deleted because there already is work associated with it or it contains subtasks.'
                );
            } else {
                $estimate   = -1 * $task->estimate;
                $task->updateRecurringEstimate($estimate);
                $budget     = -1 * $task->budget;
                $task->updateRecurringBudget($budget);
                $this->view = array(
                    'task' => $task->as_array()                    
                );
                $parent = $task->parent;
                $this->view['task']['icon']         = $task->status_icon->icon;
                $this->view['task']['has_children'] = $task->subtasks->count_all();
                $this->view['task']['duration']     = gmdate('H:i', $task->work);
                $this->view['task']['estimate']     = -1 * $task->estimate;
                $this->view['task']['work_seconds'] = $this->view['task']['work'];
                
                $task->delete();
                
                $this->view['success'] = TRUE;
                $this->view['message'] = 'The task was successfully deleted. No worklogs were affected.';                
                if ($parent) {
                    $progress = $parent->averageProgress();
                    $parent->progress($progress);
                    $this->view['parent'] = $parent->as_array();
                    $this->view['parent']['progress'] = round($progress);
                }
                $this->view['totals'] = $project->get_totals();
            }
        } else {
            $this->view = array(
                'error'     => TRUE,
                'message'   => 'The task could not be deleted. You have not been assigned to this project'
            );
        }
    }
    
    public function action_change_task_status()
    {
        $task_id = $this->request->post("task_id");
        
        try {
            $task   = new Model_Task($task_id);
            $status = $this->request->post("task_status");
            
            switch ($status) {
                case 'ongoing'  : {
                    $task->enable();
                    $task->enableParents();
                } break;
                case 'disabled' : {
                    $task->disable();
                } break;
                case 'finished' : {
                    $task->finish();
//                    $task->finishParents();
                } break;
            }
            
            $this->view = array(
                'success'   => TRUE,
                'message'   => 'Task status sucessfully updated to ' . $task->status,
                'icon'      => $task->status_icon->icon,
                'totals'    => Model_Project::get_project_totals($task->project_id),
                'task'      => $task->as_array()
            );
        } catch (Exception $e) {
            $this->view = array(
                'error' => TRUE,
                'message' => $this->exceptionToAjax($e)
            );
        }
    }
    
    public function action_task_users()
    {
        $task_id    = $this->request->param('id');
        $task       = new Model_Task($task_id);
        if ($task->user_id == $this->user->id || ($task->project->has('contacts', $this->user))) {
            $this->view = array(
                'users'     => Model_Task::get_users($task->id),
                'success'   => TRUE
            );
        }
    }
    
    public function action_task_users_assign()
    {
        $task_id = $this->request->post('task_id');
        $users   = $this->request->post('users');
        
        $task = new Model_Task($task_id);
        
        if ($task->user_id == $this->user->id) {
            $task->update_users($users);
           
            $this->view = array(
                'success' => TRUE,
                'message' => 'Users successfuly updated for this task'
            );
        } else {
            $this->view = array(
                'error' => TRUE,
                'message'   => $users,
                'task_id' => $task_id
            );
        }
    }
    
    
    public function project_work($project_id, $parent_id = NULL)
    {
        $this->show_header_active_tasks(FALSE);
        $this->view->tasks      = Model_Task::get_user_tasks($this->user->id, $project_id, $parent_id);
        $this->view->parent_id  = $parent_id;
        $this->view->project_id = $project_id;
        $project = new Model_Project($project_id);
        $this->view->project = $project->as_array();
        foreach($this->view->tasks as &$task) {
            $task['description'] = nl2br($task['description']);
        }
        if ($parent_id) {
            $parents = array();
            Model_Task::get_parents($parent_id, $parents);
            $this->view->parents = array_reverse($parents);
            if (!count($this->view->tasks)) {
                array_pop($this->view->parents);
            }
            $this->view->parent_progress = Model_Task::get_progress($parent_id);
            $parent = new Model_Task($parent_id);
            $this->view->parent_description = nl2br($parent->description);
        } else {
            $this->view->parent_progress = $project->progress();
        }
        $this->main_view->home_panel = 'contracted-projects';
    }
    
    public function projects()
    {
        $this->redirect('/user/home/contracted-projects');
    }

    public function action_work()
    {
        $project_id = $this->request->param('project_id');
        $parent_id  = $this->request->param('parent_id');
        
        if ($project_id) {
            $this->project_work($project_id, $parent_id);
        } else {
            $this->projects();
        }
    }


    public function action_task_assign_user()
    {
        
    }
    
    public function action_search()
    {
        
    }
    
    public function action_start_worklog()
    {
        $task_id    = $this->request->param('id');
        $task       = new Model_Task($task_id);
        $new        = FALSE;
        
        if ($task->has('users', $this->user)) {
            $worklog    = $task->worklogs
                    ->where('task_id', '=', $task->id)
                    ->where('active', '=', 1)
                    ->where('user_id', '=', $this->user->id)
                    ->find();
            if (!$worklog->id) {
                $new = TRUE;
                $worklog->start_time = time();
                $worklog->user_id    = $this->user->id;
                $worklog->task_id    = $task->id;
                $worklog->active     = 1;
                $worklog->save();
            }

            $this->view = array(
                'worklog'       => $worklog->as_array(),
                'previous_time' => !$new ? TRUE : FALSE,
                'success'       => TRUE
            );
        } else {
            $this->view = array(
                'error' => TRUE,
                'message' => 'This task is not among your assignments.'
            );
        }
    }
    
    public function action_stop_worklog()
    {
        $task_id    = $this->request->param('id');
        $task       = new Model_Task($task_id);
        
        if ($task->has('users', $this->user)) {
            $worklog    = $task->worklogs
                    ->where('task_id', '=', $task->id)
                    ->where('active', '=', 1)
                    ->where('user_id', '=', $this->user->id)
                    ->find();
            if ($worklog->id) {
                
                $wage = Model_Project_Wage::get_wage_value($worklog->user_id, $task->project_id);
                
                $worklog->stop_time         = time();
                $worklog->duration          = $worklog->stop_time - $worklog->start_time;
                $worklog->active            = 0;
                $worklog->note              = $this->request->post('message');
                $worklog->budget_spent      = $wage * ($worklog->duration/3600);
                $worklog->original_duration = $worklog->duration;
                $worklog->modified          = NULL;
                $worklog->save();
                
                $task->updateRecurringWork($worklog->duration);
                
                $this->view = array(
                    'success'   => TRUE,
                    'worklog'   => $worklog->as_array(),
                    'task'      => $task->as_array()
                );
            } else {
                $this->view = array(
                    'error'     => TRUE,
                    'message'   => 'You can not stop a worklog that does not exist.'
                );
            }
        } else {
            $this->view = array(
                'error' => TRUE,
                'message' => 'This task is not among your assignments.'
            );
        }
    }
    
    public function action_edit_worklog()
    {
        $worklog_id = $this->request->param('id');
        $worklog    = new Model_Worklog($worklog_id);
        if ($worklog->user_id == $this->user->id) {
            $worklog->note  = $this->request->post('note');
            $duration       = $this->request->post('duration');
            $progress       = $this->request->post('progress');
            
            $work           = $this->request->post('work');
            
            $wage = Model_Project_Wage::get_wage_value($worklog->user_id, $worklog->task->project_id);
            
            $timeDifference         = $duration - $worklog->task->work;
            
            if (is_numeric($work) && ($work != $worklog->duration)) {
                $worklog->modified = time();
                $worklog->duration = $work;
                $worklog->budget_spent  = ($work/3600) * $wage;
            } else {
                $worklog->budget_spent  = ($duration/3600) * $wage;
            }
            
            $worklog->task->updateRecurringWork($timeDifference);
            if ($progress == 100) {
                $worklog->task->status = 'finished';
            } else {
                $worklog->task->status = $this->request->post('status');
                if ($worklog->task->status == 'finished') {
                    $progress = 100;
                }
            }
            
            $worklog->task->progress($progress);
            $worklog->task->save();
            $worklog->task->finishParents();
            $worklog->save();
            
            $parent_progress = 0;
            if ($worklog->task->parent_id) {
                $parent_progress = Model_Task::get_progress($worklog->task->parent_id);
            } else {
                $parent_progress = $worklog->task->project->progress();
            }
            
            $this->view = array(
                    'success'           => TRUE,
                    'worklog'           => $worklog->as_array(),
                    'task'              => $worklog->task->as_array(),
                    'parent_progress'   => $parent_progress
            );
            $this->view['task']['has_children'] = $worklog->task->subtasks->count_all();
        } else {
            $this->view = array(
                    'error'     => TRUE,
                    'message'   => 'This is not your worklog or it does not exist.'
            );
        }
    }

    public function action_task_enable()
    {
        $task_id    = $this->request->post('id');
        $task       = new Model_Task($task_id);
        
        $parent_progress = 0;
        if ($task->parent_id) {
            $parent_progress = Model_Task::get_progress($task->parent_id);
        } else {
            $parent_progress = $task->project->progress();
        }
        
        if ($task->has('users', $this->user)) {
            $task->status = 'ongoing';
            $task->save();
            $this->view = array(
                'success'           => TRUE,
                'task'              => $task->as_array(),
                'parent_progress'   => $parent_progress
            );
            $this->view['task']['has_children'] = $task->subtasks->count_all();
        } else {
            $this->view = array(
                'error' => TRUE,
                'message' => 'This task is not among your assignments.'
            );
        }
    }

    public function action_teams()
    {
        try {
            $teams = Model_Team::get_teams($this->user->id, TRUE);
            $this->view = array(
                'success'   => TRUE,
                'teams'     => $teams
            );
        } catch (Kohana_Exception $e) {
            $this->view = array(
                'error' => TRUE,
                'message' => $e->getTraceAsString()
            );
        }
    }
    
    public function action_default_assignments()
    {
        $project_id = $this->request->post('project_id');
        $project    = new Model_Project($project_id);
        
        if ($this->user->id == $project->user_id) {
            $tasks = $project->tasks->find_all()->as_array();
            foreach ($tasks as $task) {
                $task->set_default_users();
            }
        }
        $this->view = array(
            'success'   => TRUE
        );
    }
    
    
    public function action_supervise()
    {
        $project_id = $this->request->param('id');
        $project    = new Model_Project($project_id);
        if ($project->has('contacts', $this->user)) {
            $this->view = array(
                'tasks'     => Model_Task::get_sub_tasks($project->id),
                'project'   => $project->as_array(),
                'totals'    => $project->get_totals(),
                'currency'  => $project->currency->code,
                'project_id'=> $project->id
            );
        }
        $this->add_js_file('spells/operate.js');
        $this->add_js_file('spells/supervise.js');
        $this->add_template('supervise');
    }
}
