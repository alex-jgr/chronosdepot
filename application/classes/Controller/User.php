<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * The User controller
 *
 * @author Alexandru
 */
class Controller_User extends Controller_Base 
{
    public function before() 
    {
        parent::before();
       
        $action = $this->request->action();
        
        switch ($action) {
            case 'login':       break;
            case 'logout':      break;
            case 'register':    break;
            case 'reset':       break;
            case 'fb':          break;
            case 'register':    break;
            case 'acceptinvite': break;
            case 'reset_token':  break;
            case 'new_password': break;
            default: if (!$this->user) {
                $this->redirect('/user/login');
            } break;
        }
    }

    public function action_check()
    {
        $email = $this->request->post('email');
        $user = ORM::factory('User', array('email' => $email));
        if ($user->id) {
            $this->view = array(
                'error' => TRUE
            );
        } else {
            $this->view = array(
                'success' => TRUE
            );
        }
    }

    public function action_register()
    {
        $password = $this->request->post('password');
        $user = new Model_User();
        $user->email        = $this->request->post('email');
        $user->password     = $password;
        $user->username     = $this->request->post('email');
        $user->firstname    = $this->request->post('firstname');
        $user->lastname     = $this->request->post('lastname');
        $user->phone        = $this->request->post('phone');
//        $agreed = $this->request->post('agree');
//       
//        if ($agreed === 'on') {
            $user->save();
            // TODO: remove the next line when moving to live
            //die('While the system is under development your registration can only be activated by the admin. Send an email to jgr.alex@live.com for any questions or requests.');
            
            $role = new Model_Role(1);
            $user->add('roles', $role);
            Auth::instance()->login($user->username, $password, TRUE);
            $this->user = $user;
            $this->redirect('/user/firsttime');
//        } else {
//            $this->redirect('/user/login');
//        }
    }
    
    public function action_firsttime()
    {
        $experience = $this->request->post('experience_type');
        if ($experience !== NULL) {
            $this->user->simple     = $experience;
            $this->user->firsttime  = 0;
            $this->user->save();
            $this->redirect('/user/settings');
        }
        $this->view = $this->user->as_array();
    }

    public function action_home()
    {
        $this->view = new View_User();
        $this->view->owner_projects         = $this->user->get_owner_projects_array();//owner_projects->order_by('id', 'desc')->find_all()->as_array();
        $this->view->owned_projects         = $this->user->get_owned_projects_array();//owned_projects->order_by('id', 'desc')->find_all()->as_array();
        $this->view->supervised_projects    = $this->user->get_supervised_projects_array();
        $this->view->managed_teams          = $this->user->get_managed_teams_array();//managed_teams->order_by('id', 'desc')->find_all()->as_array();
        $this->view->notifications          = $this->main_view->notifications;
        $this->view->teams                  = $this->user->get_teams_array();//teams->order_by('id', 'desc')->find_all()->as_array();
        $this->view->simple                 = $this->user->simple;
        
        $this->main_view->home_panel = $this->request->param('id');
        if (!$this->main_view->home_panel) {
            $this->main_view->home_panel = 'owned-projects';
        }
        $this->main_view->this_is_home = TRUE;
        $this->add_js_file('jquery-ui.min.js');
        $this->add_js_file('jquery.ui.touch-punch.min.js');
        $this->add_js_file('spells/home.js');
    }

    public function action_profile()
    {
        $user_id    = $this->request->param('id');
        $user       = new Model_User($user_id);
        
        $this->view = $user->as_array();
        unset($this->view['password']);
        
        $this->view['description']  = nl2br($user->description);
        $this->view['image']        = $user->image->versions->where('name', '=', 'normal')->find()->as_array();
        $this->view['task_types']   = $user->task_types->find_all()->as_array();
        $this->view['user_teams']   = Model_Team::get_user_public_teams($user->id);
        $this->view['managed_teams']= Model_Team::get_managed_teams($this->user->id, $user->id);
        
        $this->add_js_file('spells/user-profile.js');
    }
    
    public function action_settings() 
    {
        if ($this->request->post()) {
            $image_id = $this->request->post('image_id');
            $image = NULL;
            if ($image_id && ($image_id != $this->user->image_id)) {
                if ($this->user->image_id) {
                    $image = new Model_Image($this->user->image_id);
                }
                $this->user->image_id = $image_id;
            }
            
            $this->user->username = $this->request->post('username');
            $this->user->email    = $this->request->post('email');
            
            $password = $this->request->post('password');
            if ($password) {
                if (!$this->user->fb_id) {
                    $old_password   = $this->request->post('old_password');
                    $auth           = Auth::instance();
                    $pass_hash      = $auth->hash($old_password);
                    $pass_check     = $auth->password($this->user->username);
                    if ($pass_hash === $pass_check) {
                        $this->user->password = $password;
                    }
                } else {
                    $this->user->password = $password;
                }
            }
            $background =  $this->request->post('background');
            $theme      =  $this->request->post('theme');
            $this->user->background  = str_replace('script', 'We do not allow scripts here. What were you thinking? Dumbass...', $background);
            $this->user->description = $this->request->post('description');
            $this->user->phone       = $this->request->post('phone');
            $this->user->location    = $this->request->post('location');
            $this->user->wage        = $this->request->post('wage');
            $this->user->currency_id = $this->request->post('currency_id');
            $this->user->headline    = $this->request->post('headline');
            $this->user->simple      = $this->request->post('simple');  // Simplified profile usage.
            $this->user->date_format = $this->request->post('date_format');
            $this->user->theme       = str_replace('script', 'We do not allow scripts here. What were you thinking?...', $theme);
            $this->user->save();
            
            if ($image) {
                $image->delete();
            }
        }
        
        $this->view = $this->user->as_array();
        unset($this->view['password']);
        $this->view['teams'] = Model_Team::get_user_public_teams($this->user->id);
        $this->view['image'] = $this->user->image->versions->where('name', '=', 'normal')->find()->as_array();
        
        $this->view['currencies'] = Model_Currency::get_all();
        
        $this->add_js_file('moment.min.js');
        $this->add_js_file('file-upload/file-upload.js');
        $this->add_js_file('validate/jquery.validate.js');
        $this->add_js_file('colorpicker/colorpicker-color.js');
        $this->add_js_file('colorpicker/colorpicker.js');
        $this->add_js_file('spells/user-settings.js');
        
        $this->main_view->colorpicker = TRUE;
    }
    
    
    public function action_save_picture()
    {
        $upload = Uploader::factory('user-image')->process_upload($_FILES['image']);
        
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
            $image->label   = 'user-image';
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
            $this->user->image_id = $image->id;
            $this->user->save();
            $upload['image_id'] = $image->id;
            
            $this->view = $upload;
        } catch(Exception $e) {
            $this->view = array(
                'error' => TRUE,
                'message' => 'Could not save images: ' . $e->getTraceAsString()
            );
        }
    }
    
    
    public function action_logout()
    {
        Auth::instance()->logout(TRUE, TRUE);
        $this->redirect('/user/login');
    }
    
    public function action_login() 
    {
        if ($this->request->post()) {            
            $username   = $this->request->post('email');
            $password   = $this->request->post('password');
            $logged_in = Auth::instance()->login($username, $password);
            if ($logged_in) {
                $this->user = Auth::instance()->get_user();
            }
        }
        
        if ($this->user) {
            if ($this->user->firsttime) {
                $this->redirect('/user/firsttime');
            } else {
                $this->redirect('/user/home');
            }
            exit();
        } else {
            $this->view     = new View_User();
            $this->view->facebook_login_url = URL::site('user/fb', TRUE); //$facebook->getLoginUrl(array('scope' => 'email', 'redirect_uri' => $redirect_url)); //, 'locale' => 'da_DK'
            $this->view->facebook_app_id    = Kohana::$config->load('facebook')->get('appId');
            $this->js_files = array(
                array('name' => 'validate/jquery.validate.min.js'),
                array('name' => 'mustache.js/mustache.js'),
                array('name' => 'spells/common.js'),
                array('name' => 'spells/login.js')
            );
            $this->set_meta(array(
                'title'         => 'Chronos Depot',
                'keywords'      => 'chronos, depot, welcome, page, time tracking, time sheet, timesheet, project, management, task, team, invoice, login',
                'description'   => 'Lightweight time tracking and project management tool. Log in page.'
            ));
            $this->set_facebook_meta(array(
                'title'         => 'Chronos Depot',
                'type'          => 'website',
                'url'           => URL::site('', TRUE),
                'image'         => '',
                'description'   => 'Time tracking and booking system'
            ));
        }
    }

    public function action_fb() 
    {
        $facebook = new FB();
        if (!$this->request->query('code')) {
            die(var_dump($facebook->getLoginUrl()));
            $this->redirect($facebook->getLoginUrl());
        } else {
            $fbuser = $facebook->getUser();            
        }
        /* @var $fbuser \Facebook\GraphUser */
        
        if ($fbuser) {
            try {
                // Proceed with the facebook user
                $user = ORM::factory('User')->where('fb_id', '=', $fbuser->getId())->find();
                if (!empty($user->id)) {
                    $_SESSION['fb_login'] = true;
                    Auth::instance()->force_login($user->email);
                    if ($user->firsttime) {
                        $this->redirect('/user/firsttime');
                    } else {
                        $this->redirect('/user/home');
                    }
                } else {
                    // Facebook user, who does not yet have a BattleGrid profile  
                    $new_user = TRUE;
                    $user = ORM::factory('User')->where('email', '=', $fbuser->getEmail())->find();
                    
                    if (!empty($user->id)) {
                        $new_user = FALSE;
                    } else {
                        $user->email = $fbuser->getEmail();
                        if (!$user->email) {
                            $user->email = UUID::v4() . '@chronosdepot.com';
                        }
                    }
                    
                    $user->username     = $fbuser->getName();
                    $user->email        = $fbuser->getEmail();
                    
                    $user->firstname    = $fbuser->getFirstName();
                    $user->lastname     = $fbuser->getLastName();
                    $user->password     = substr(md5(time() . $user->email), 0, 10);
                    $user->fb_id        = $fbuser->getId();
//                    die(var_dump($user->password));
//                    if (!empty($picture->picture->data->url)) {
//                        $user->profile_picture = $picture->picture->data->url;
//                    }
                     
                    $user->save();

                    // TODO: enable the following code when going live
                    //die('While the system is under development your registration can only be activated by the admin. Send an email to jgr.alex@live.com for any questions or requests.');
                    if ($new_user) {
                        $role = new Model_Role(1);
                        $user->add('roles', $role);
                    }

                    Auth::instance()->force_login($user->email);
                    if ($new_user) {
                        $this->redirect('/user/firsttime');
                    } else {
                        $this->redirect('/user/home');
                    }
                }
            } catch (FacebookApiException $e) {
                error_log($e);
                $fbuser = null;
                die(var_dump($e));
            }
        }

        if (isset($_GET['error_reason']) && $_GET['error_reason'] == 'user_denied') {
            $this->view->fb_error = true;
        }
    }
    public function action_get()
    {
        $user = Auth::instance()->get_user();
        $this->view = array(
            'id'    => $user->id,
            'name'  => $user->firstname . ' ' . $user->lastname,
            'email' => $user->email
        );
    }
    
    public function action_ajax_search()
    {
        $email = $this->request->post('email');
        $users = DB::select('id', DB::expr('username as value'), 'firstname', 'lastname')
                ->from('users')
                ->where('email', 'LIKE', $email . '%')
                ->limit(16)
                ->execute()->as_array();
        $this->view = $users;
    }
    
    public function action_notifications()
    {
        $page       = $this->request->post('page');
        $limit      = $this->request->post('elements');
        $start_date = $this->request->post('start_date');
        $end_date   = $this->request->post('end_date');
        
        $results_count = $this->user->notifications;
        
        if ($start_date) {
            $results_count->where('created', '>=', $start_date);
        }
        
        if ($end_date) {
            $end_date += 86400;
            $results_count->where('created', '<=', $end_date);
        }
        
        $last = $results_count->count_all();
        
        if (!$limit) {
            $limit = 15;
        }
        if (!$page) {
            $page   = 0;
            $offset = 0;
        } else {
            $offset = $limit * $page;
        }
        $notifications = Model_Notification::get_user_notifications($this->user->id, $limit, $offset, $start_date, $end_date);
        $pages = array(
            'prev'      => $page - 1,
            'current'   => $page,
            'next'      => $page + 1,
            'max'       => ceil($last/$limit) - 1
        );
        if ($this->request->is_ajax()) {
            $this->view = array(
                'success'       => TRUE,
                'notifications' => $notifications,
                'pages'         => $pages
            );
        } else {
            $this->add_template('user');
            $this->add_js_file('spells/notifications.js');
            $this->view->notifications = $notifications;
            $this->view->pages         = $pages;
        }
    }
    
    public function action_view_notification() {
        $id = $this->request->post('id');
        $notificaiton = new Model_Notification($id); 
        if ($notificaiton->user_id == $this->user->id) {
            $notificaiton->status = 'viewed';
            $notificaiton->save();
        }
        
        $this->view = array(
            'success' => TRUE,
            'status'  => $notificaiton->status,
            'remaining' => $this->user->notifications->where('status', '=', 'pending')->count_all()
        );
    }
    
    public function action_project()
    {
        $project_id = $this->request->param("id");
        $project = new Model_Project($project_id);
    }
    
    public function action_activate()
    {
        if ($this->user->active) {
            
        }
    }
    
    public function action_acceptinvite()
    {
        $hash = $this->request->param('id');
        $project_contact = ORM::factory('Project_Contact', array('hash' => $hash));
        if ($project_contact->id) {
            $password = substr('hash', 0, 8);
            $user = new Model_User();
            $user->email    = $project_contact->email;
            $user->password  = $password;
            $user->username = $project_contact->email;
            $user->save();
            $user->add('roles', ORM::factory('Role', array('name' => 'login')));
            $project_contact->activate($user->id);
            $project_contact->project->customer_id = $user->id;
            $project_contact->project->save();
            Auth::instance()->login($user->username, $password);
            $this->redirect('/user/settings');
        } else {
            
        }
    }
    
    public function action_reset()
    {
        $user = ORM::factory('User')->where('email', '=', $this->request->post('email'))->find();

        if ($user->id) {
            $now    = time();
            $token  = ORM::factory('ResetPassword')
                    ->where('user_id', '=', $user->id)
                    ->where('expires', '>', $now)
                    ->where('status', '=', 'pending')
                    ->find();   // find a previously unused token, update it and send it to the user. Otherwise create a new reset request. The factory will take care of that if none is found.
            $token->user_id = $user->id;
            $token->created = $now;
            
             // available for two weeks from now
            $token->expires = $now + 3600;
            $token->token   = UUID::v5(UUID::URL, $user->email.$now.  rand(0, 255));
            
            $url = URL::site('/user/reset_token', TRUE).'/'. $token->token;
            
            
            
            $email = Email::factory('Password reset');
            $email->to($user->email);
            $email->from('admin@chronosdepot.com', 'ChronosDepot.com');
            // HTML email
            $email->message(
                    'Hello ' . $user->email . '<br />'
                    .'You are receiving this email message because you requested a password reset for your account on ChronosDepot.com. If you still want to reset your password follow the link below: <br />'
                    . $url = '<a href="' . $url . '">' . $url . '</a><br />'
                    . 'Best regards, <br />'
                    . 'ChronosDepot.com' ,
            'text/html');
            
            
            // Plain text email
            $email->message(
                    'Hello ' . $user->email . "\r\n"
                    .'You are receiving this email message because you requested a password reset for your account on ChronosDepot.com. If you still want to reset your password follow the link below:' . "\r\n"
                    . $url
                    . 'Best regards,' . "\r\n"
                    . 'ChronosDepot.com' ,
            'text/plain');
            
//            if (mail($user->email, 'password reset', $message)) {
            if ($email->send()) {
                $token->save();
                $this->view = array(
                    'success' => TRUE,
                    'message' => 'An email has been sent to you. Follow the instructions in that email to reset your password.'
                );
            } else {
                $this->view = array(
                    'error'     => TRUE,
                    'message'   => 'Something went wrong while processing your request.'
                );
            }
        } else {
            $this->view = array(
                'error'     => TRUE,
                'message'   => 'We could not find your email in our database. Please make sure that you typed it correctly.'
            );
        }
    }
    
    public function action_reset_token()
    {
        $token = $this->request->param('id');
        if (UUID::valid($token)) {
            $tokenObject = ORM::factory('ResetPassword')->where('token', '=', $token)->find();
            $now = time();
            $this->template = 'user/reset';
            
            if ((!$tokenObject->user || ($now > $tokenObject->expires)) &&($tokenObject->status != 'pending')) {
                $this->view = array(
                    'expired' => TRUE
                );
            } else {
                $this->view = array('token' => $token);
            }
        }
    }
    
    public function action_new_password()
    {
        $token = $this->request->post('token');
        $error = array();
        if (UUID::valid($token)) {
            $tokenObject = ORM::factory('ResetPassword')->where('token', '=', $token)->find();
            $now = time();
            
            if ($tokenObject->user && ($now < $tokenObject->expires) && ($tokenObject->status == 'pending')) {
                $password   = $this->request->post('password');
                $confirm    = $this->request->post('confirm_password');
                if ($password == $confirm) {
                    $tokenObject->old_password = $tokenObject->user->password;
                    $tokenObject->used         = $now;
                    $tokenObject->expires      = $now + 1209600;  // Enable the user to recover his old password within two weeks
                    $tokenObject->save();
                    
                    $tokenObject->user->password = $password; 
                    $tokenObject->user->save();
                    
                    $this->view = array(
                        'success' => TRUE
                    );
                } else {
                    $error = array(
                        'token' => $tokenObject->token,
                        'message' => 'The confirmation password did not match the new password. Please try again!'
                    );
                }
            } else {
                $error = array(
                    'expired' => TRUE
                );
            }
        } else {
            $this->view = array('invalid' => 'Invalid token');
        }
        if ($error) {
            $this->template = 'user/reset';
            $this->view = $error;
        }
    }
}