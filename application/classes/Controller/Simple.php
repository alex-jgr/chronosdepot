<?php defined('SYSPATH') OR die('No direct access allowed.');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Simple
 *
 * @author admin
 */
class Controller_Simple extends Controller_Base
{
    public function action_edit_project()
    {
        $project_id = $this->request->param('id');
        $project    = new Model_Project($project_id);
        
        if ($project->id && $project->user_id != $this->user->id) {
            $this->redirect('user/home');
            exit();
        }
        
        $this->view = $project->as_array();
        $this->view['project_id']   = $this->view['id'];
        $this->view['currencies']   = Model_Currency::get_all();
        
        // this is to make sure that we keep the old project members if this project was created with teams created before the switch to simple interface and continue with the same teams
        // it will also get the users for the simple version, because they will be set to the project after the first simple save.
        if ($project->id) { 
            $this->view['users'] =  Model_Project::get_all_users($project->id);
        }
        
        $team = $this->user->get_simple_team(); //after this there will be a team, regardles of whether there was one before or not
        
        if (empty($this->view['users'])) { // if no members were previously found check for what's happening inside the simple team. This will work for new projects created using the simple interface.
            $this->view['users']  = $team->get_all_members();
        }
        
        $this->view['team_id'] = $team->id;
        $this->view['join_requests']      = Model_Team_Request::get_requests($team->id, NULL, 'join',   TRUE);
        $this->view['invite_requests']    = Model_Team_Request::get_requests($team->id, NULL, 'invite', TRUE);
        
        $this->view['contacts']     = Model_Project_Contact::active_contacts($project->id);
        $this->view['invites']      = Model_Project_Contact::pending_invites($project->id);
        $this->view['customer']     = $project->get_customer();
        
        $this->add_js_file('spells/project-edit.js');
        $this->add_js_file('spells/simple-project.js');
        $this->add_template('team-management');
        $this->add_template('project-edit');
        $this->template = 'project/simple-edit';
        
        $this->main_view->home_panel = 'owned-projects';
    }

    public function action_save_project()
    {
        $project_id = $this->request->param('id');
        $project    = new Model_Project($project_id);
        if (!empty($project->user_id) && $project->user_id !== $this->user->id) {
            $this->redirect('user/home');
        } else {
            
            $project->user_id       = Auth::instance()->get_user();
            $project->name          = $this->request->post('name');
            $project->description   = $this->request->post('description');
            $project->budget        = $this->request->post('budget');
            $project->wage          = $this->request->post('project-wage');
            $project->save();
            
            //if there is no project, then this is a new project and we have to add the admin as a person assigned to the project too
            if (is_null($project_id)) {
                $project->created = time();
                $project->add('users', $this->user);
            }
            
            $team = $this->user->get_simple_team();
            
            if (!$project_id) {
                $project->assign_teams(array($team->id));
            }
            
            $wages = $this->request->post('wage');
            
            foreach ($wages as $user_id => $wage) {
                $user = new Model_User($user_id);
                if (!$project->has('users', $user)) {
                    $project->add('users', $user);
                }
                $project->user_wage($user->id, $wage);
            }
            
            $this->redirect('project/simple/edit/' . $project->id);
        }
    }
    
    public function action_mobile()
    {
        $mobile = Request::user_agent('mobile');
        die(var_dump($mobile));
    }


    public function action_manage_project()
    {
        
    }
}
