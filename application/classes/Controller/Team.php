<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Description of Team
 *
 * @author Alexandru
 */
class Controller_Team extends Controller_Base
{
    public function before()
    {
        parent::before();
        if (!Auth::instance()->get_user()) {
            $this->redirect('user/login');
        }
        $this->view = new View_Data();
    }
    
    public function action_edit($team_id = NULL)
    {
        if (!$team_id) {
            $team_id = $this->request->param('id');
        }
        $team = new Model_Team($team_id);
        if ($team->user_id === $this->user->id) {
            $this->template = 'team/edit';
            $this->view = $team->as_array();
            if ($team->image_id) {
                $this->view['image'] = $team->image->versions->where('name', '=', 'normal')->find()->as_array();
            }
        }
        $this->main_view->home_panel = 'owned-teams';
        $this->add_js_file('file-upload.js');
        $this->add_js_file('spells/team-edit.js');
    }
    
    public function action_save_picture()
    {
        $upload = Uploader::factory('team-image')->process_upload($_FILES['image']);
        
        $image  = new Model_Image();
        
        $now    = time();
        try {
            if ($upload['original']) {
                $image->name        = $upload['name'];
                $image->path        = $upload['path'];
                $image->public_path = $upload['public_path'];
                $image->width       = $upload['width'];
                $image->height      = $upload['height'];
            }
            $image->label   = 'team-image';
            $image->created = $now;
            $image->save();

            foreach ($upload['versions'] as $version_name => $values) {
                $version = new Model_Image_Version();
                $version->image_id      = $image->id;
                $version->name          = $version_name;
                $version->path          = $values['path'];
                $version->public_path   = $values['public_path'];
                $version->width         = $values['width'];
                $version->height        = $values['height'];
                $version->created       = $now;
                $version->save();
            }
            
            $upload['image_id'] = $image->id;
            
            $this->view = $upload;
        } catch(Exception $e) {
            $this->view = array(
                'error' => TRUE,
                'message' => 'Could not save images: ' . $e->getTraceAsString()
            );
        }
    }


    public function action_save()
    {
        $team_id = $this->request->param('id');
        $team    = new Model_Team($team_id);
        if (!empty($team->user_id) && $team->user_id !== $this->user->id) {
            $this->redirect('user/home');
        } else {
            $team->user_id      = Auth::instance()->get_user();
            $team->name         = $this->request->post('name');
            $team->description  = $this->request->post('description');
                        
            $image_id   = $this->request->post('image_id');
            if (empty($image_id)) {
                $image_id = NULL;
            }
            $image      = NULL;
            if ($team->image_id && $team->image_id != $image_id) {
                $image  = $team->image;
            }
            $team->image_id = $image_id;
            if (!$team_id) {
                $team->created = time();
            }
            
            $team->save();
            
            if ($image) {
                $image->delete();
            }
            
            //if there is no team, then this is a new team and we have to add the admin as a person assigned to the team too
            if (is_null($team_id)) {
                $team->created = time();
                $team->add('members', $this->user);
            }
            
            $this->redirect('team/edit/'.$team->id);
        }
    }
    
    public function action_positions()
    {
        
    }


    public function action_profile()
    {
        $team_id = $this->request->param('id');
        $team = new Model_Team($team_id);
        
        $this->view = $team->as_array();
        $this->view['description'] = nl2br($this->view['description']);
        
        $this->view['image'] = $team->image->versions->where('name', '=', 'normal')->find()->as_array();
        $members = $team->members->find_all()->as_array();
        
        $team_requests = Model_Team_Request::get_requests($team->id, $this->user->id);
        
        foreach($team_requests as $request) {
            if ($request['type'] == 'invite') {
                $this->view['invite_request'] = $request['id'];
            }
            if ($request['type'] == 'join') {
                $this->view['join_request'] = $request['id'];
            }
        }
        
        foreach ($members as $member) {
            $this->view['members'][] = array(
                'user'       => $member->as_array(),
                'task_types' => Model_User::get_task_types($member->id, $team->user->id)
            );
            if ($this->user->id == $member->id) {
                $this->view['can_leave'] = TRUE;
            }
        }
        
        if ((!count($team_requests) && empty($this->view['can_leave']))) {
            $this->view['can_request'] = TRUE;
        }
        $this->add_js_file('spells/teams.js');
    }
    
    public function action_accept_invitation()
    {
        $team_id = $this->request->param('id');
        $team   = new Model_Team($team_id);
        
        $request = ORM::factory('Team_Request')
                ->where('team_id', '=', $team->id)
                ->where('user_id', '=', $this->user->id)
                ->where('type', '=', 'invite')
                ->find();
        
        if ($request->id) {
            if (!$team->has('members', $this->user)) {
                $team->add('members', $this->user);
            }
            // Adding the user to all the team projects
            foreach ($team->projects->find_all()->as_array() as $project) {
                if (!$project->has('users', $this->user)) {
                    $project->add('users', $this->user);
                }
                if ($team->user->simple) {
                    foreach ($project->tasks->find_all()->as_array() as $task) {
                        if (!$task->has('users', $this->user)) {
                            $task->add('users', $this->user);
                        }
                    }
                }
            }
            Model_Notification::team_accept($request);
            $request->delete();
        }
        
        $this->redirect('team/profile/' . $team->id);
    }
    
    public function action_accept_join()
    {
        $request_id = $this->request->param('id');
        try {
            $request    = new Model_Team_Request($request_id);
            $user       = $request->user;
            $team = $request->team;
            if ($request->id && $request->team->user_id == $this->user->id) {
                if (!$team->has('members', $request->user)) {
                    $team->add('members', $request->user);
                }
                // Add the user to all the team projects
                foreach ($team->projects->find_all()->as_array() as $project) {
                    if (!$project->has('users', $request->user)) {
                        $project->add('users', $request->user);
                    }
                    if ($team->user->simple) {
                        foreach ($project->tasks->find_all()->as_array() as $task) {
                            if (!$task->has('users', $request->user)) {
                                $task->add('users', $request->user);
                            }
                        }
                    }
                }
                Model_Notification::team_accept($request);
                $request->delete();
                $this->view = array(
                    'success'   => TRUE,
                    'message'   => 'Member successfuly added to the team',
                    'request_id'=> $request_id,
                    'user'      => $user->as_array()
                );
            }
        } catch (Kohana_Exception $e) {
            $this->view = array(
                'errot'     => TRUE,
                'message'   => $e->getTraceAsString()
            );
        }
    }
    
    public function action_cancel_request() {
        $request_id = $this->request->param('id');
        try{
            $request    = new Model_Team_Request($request_id);
            if ($request->user_id == $this->user->id || $request->team->user_id == $this->user->id) {
                $this->view = array(
                    'success' => TRUE,
                    'message' => 'Request cancelled',
                    'request_id' => $request_id
                );
                Model_Notification::team_cancel_request($request);
                $request->delete();
            }
        } catch(Kohana_Exception $e) {
            $this->view = array(
                'error'     => TRUE,
                'message'   => $e->getTraceAsString()
            );
        }
    }

    /**
     * Remove a team if the logged in user owns it.
     */
    public function action_remove()
    {
        $team_id = $this->request->param('id');
        $team = new Model_Team($team_id);
        if ($this->user->id == $team->user_id) {
            $team->delete();
        }
        $this->redirect('user/home/owned-teams');
    }
    
    public function action_leave()
    {
        $team_id = $this->request->param('id');
        $team    = new Model_Team($team_id);
        
        if ($team->has('members', $this->user)) {
            $team->cleanup_after($this->user);
            $team->remove('members', $this->user);
            Model_Notification::team_leave($team, $this->user);
        }
        
        $this->redirect('user/home/owner-teams');
    }
    
    public function action_exclude()
    {
        $user_id = $this->request->post('user_id');
        $team_id = $this->request->post('team_id');
        
        $team = new Model_Team($team_id);
        
        if ($team->user_id == $this->user->id) {
            $user = new Model_User($user_id);
            $team->cleanup_after($user);
            $team->remove('members', $user);
            $this->view = array(
                'success' => TRUE,
                'message' => $user->username . ' has been excluded from ' . $team->name
            );
            Model_Notification::team_exclude($team, $user);
        } else {
            $this->view = array('error'   => TRUE,
                                'message' => 'This team does not belong to you. You can not alter its data.'
            );
        }
    }
   
    public function action_join_request()
    {
        $team_id = $this->request->post('team_id');
        $team = new Model_Team($team_id);
        
        if ($team->id && $this->user->id) {
            $team_request = new Model_Team_Request();
            $team_request->user_id  = $this->user->id;
            $team_request->team_id  = $team->id;
            $team_request->type     = 'join';
            $team_request->created  = time();
            $team_request->save();
            Model_Notification::team_join_request($team, $this->user);
            
            $this->view = array(
                'success' => TRUE,
                'message' => 'Your request has been registered. You will receive an answer as soon as the team leader reviews it.'
            );
        }
    }
    
    public function action_invite_request()
    {
        $user_id = $this->request->post('user_id');
        $team_id = $this->request->post('team_id');
        $email   = $this->request->post('email');
        $team    = new Model_Team($team_id);
        
        if ($team->user_id == $this->user->id) {
            $request = new Model_Team_Request();
            $user = new Model_User($user_id);
            
            if (empty($user->id)) {
                $user->email = $email;
                $user->password = md5(rand(100, 10000));
                $user->username = $email;
                $user->save();
            }
            
            $request->user_id = $user->id;
            $request->team_id = $team->id;
            $request->type    = 'invite';
            $request->created = time();
            $request->save();
            
            Model_Notification::team_invite_request($team, $user);
            $request_data = $user->as_array();
            
            unset($request_data['password']);
            $request_data['id']        = $request->id;
            $request_data['user_id']   = $user->id;
            
            $this->view = array(
                'success'   => TRUE,
                'message'   => $user->username . ' will receive a notification about your invitation to ' . $team->name,
                'request'   => $request_data
            );
        } else {
            $this->view = array('error' => 'You can not invite users to a team that you do not own.');
        }
    }
    
    public function action_accept_member()
    {
        $team_id = $this->request->post('team_id');
        $user_id = $this->request->post('user_id');
        
        $team_request = ORM::factory('Team_Request')->where('user_id', '=', $user_id)->and_where('team_id', '=', $team_id)->find();
        if (!empty($team_request->id)) {
            $team = new Model_Team($team_id);
            $team->add('members', new Model_User($user_id));
        }
        
        $this->view = array('success' => $user->username . 'has been successfully added to '. $team->name);
    }
    
    public function action_accept_team()
    {
        $team_id = $this->request->param('id');
        $team_request = ORM::factory('Team_Request')->where('user_id', '=', $this->user->id)->and_where('team_id', '=', $team_id)->find();
        if (!empty($team_request->id)) {
            $team = new Model_Team($team_id);
            $this->user->add('teams', $team);
        }
        $this->view = array('success' => 'You have been successfully added to the team.');
    }


    public function action_search()
    {
        $name   = $this->request->post('team_name');
        $this->view->teams = Model_Team::find_public_teams($name);
    }
    
    public function action_users_task_types()
    {
        $team_id = $this->request->post('team_id');
        
        $members = Model_Team::get_members($team_id);
        foreach($members as &$member) {
            $member['task_types'] = Model_User::get_task_types($member['id'], $this->user->id);
        }
        
        $task_types = Model_TaskType::get_task_types($this->user->id);
        foreach ($task_types as &$task_type) {
            $task_type['positions'] = Model_TaskType::get_task_type_positions($task_type['id']);
        }
        $this->view = array(
            'members'    => $members,
            'task_types' => $task_types
        );
    }
    
    public function action_save_user_task_types()
    {
        
        $user_task_types = $this->request->post("user_task_types");
        $user = new Model_User($user_task_types['id']);
        
        $task_types = $user->task_types->where('tasktype.user_id','=', $this->user->id)->find_all()->as_array();
        
        foreach ($task_types as $task_type) {
            $user->remove('task_types', $task_type);
        }
        
        if (is_array($user_task_types['task_types'])) {
            foreach ($user_task_types['task_types'] as $task_type_element) {
                $task_type = new Model_TaskType($task_type_element['id']);
                    if ($this->user->id == $task_type->user_id) {
                    $user->add('task_types', $task_type);
                }
            }
        }
        $this->view = array('success' => TRUE);
    }

    public function action_manage()
    {
        $team_id = $this->request->param('id');
        if (!$team_id) {
            $team_id = $this->request->post('team_id');
        }
        
        $team  = new Model_Team($team_id);
        $this->view->team       = $team->as_array();
        $this->view->members    = $team->members->find_all()->as_array();
        $this->view->positions  = Model_Position::get_positions($this->user->id);
        $this->view->join_requests      = Model_Team_Request::get_requests($team->id, NULL, 'join',   TRUE);
        $this->view->invite_requests    = Model_Team_Request::get_requests($team->id, NULL, 'invite', TRUE);
        $this->add_js_file('spells/teams.js');
        
        $this->add_template("team-management");
        $this->main_view->home_panel = 'owned-teams';
    }
}
