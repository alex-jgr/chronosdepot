<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Description of Base
 *
 * @author Alexandru
 */

class Controller_Base extends Controller
{
    /**
     * The main layout
     * @var View
     */
    protected $layout;

    /**
     * The main view object
     * @var type 
     */
    protected $main_view;

    /**
     * A template to use specified in the controller action
     * @var type 
     */

    protected $template;

    /**
     * The view to use together with the template in case this is a normal http request. 
     * An array in case this is an ajax request. The array will be converted to JSON before being sent out as response.
     * 
     * @var View|array
     */
    protected $view;

    /**
     * Extra content to add between the head tags
     * @var View 
     */
    protected $extra_header;

    /**
     * Estra content to add between the footer tags
     * @var type 
     */
    protected $extra_footer;

    /**
     *
     * @var type 
     */
    protected $js_templates;

    /**
     * Aditional javascript files to load in the bottom
     * @var type 
     */
    protected $js_files;

    protected $css_files;
    /**
     * 
     * @var Model_User
     */
    protected $user;
    
    protected $show_header_active_tasks = TRUE;
    
    protected $mobile;

    /**
     * Executed before the action
     * @return type
     */
    
    public function before() 
    {
        $this->user         = Auth::instance()->get_user();
        $this->layout       = Kohana::$config->load('site')->get('layout');
        $this->main_view    = new View_Layout();
        $this->view         = new View_Layout();        
        $this->js_templates = array('common');
        $this->mobile       = Request::user_agent('mobile');
        
        if (!(empty($this->user->id) || $this->request->is_ajax())) {
            $this->main_view->notifications = $this->user->notifications->limit(15)->order_by('id', 'DESC')->find_all()->as_array();
            $this->main_view->notif_count   = $this->user->notifications->where('status', '=', 'pending')->count_all();
            $this->main_view->user          = $this->user->as_array();
        }
        $this->js_files = array(
            array('name' => 'jquery-ui.min.js'),
            array('name' => 'mustache.js/mustache.js'),
            array('name' => 'spells/work.js'),
            array('name' => 'spells/common.js')
        );
       
        return parent::before();
    }
    
    /**
     * Executed after the action
     * @return type
     */
    public function after() 
    {
        Session::instance()->set('lang', 'ro');
        $language = Session::instance()->get('lang');
        I18n::lang($language);
        $menu;
        
        if ($this->request->is_ajax()) {
            $this->response->body(json_encode($this->view));
        } else {
            
            if ($this->extra_header) {
                $this->main_view->extra_header = $this->extra_header;
            }
            $renderer = Kostache::factory();
            if ($this->user) {
                $this->main_view->username    = $this->user->username;
                $this->main_view->background  = $this->user->background;
                $this->main_view->theme       = $this->user->theme;
                
                if ($this->show_header_active_tasks) {
                    $this->main_view->tasks = Model_Task::get_user_active_tasks($this->user->id);
                    $this->main_view->has_active_tasks = count($this->main_view->tasks);
                }
                
                $admin_role = ORM::factory('Role',array('name' => 'admin'));
                $is_admin   = $this->user->has('roles', $admin_role);

                if ($is_admin) {
                    $menu = Kohana::$config->load('menu')->get('admin');
                } else {
                    $menu = Kohana::$config->load('menu')->get('login');
                }
                
                $menu['/user/settings']['name'] = $this->user->firstname;
                $headerNotifications = $renderer->render(array(
                    'notifications' => $this->main_view->notifications, 
                    'notif_count'   => $this->main_view->notif_count), 
                'user/headernotifications');
                
                $menu['/user/notifications']['special'] = $headerNotifications;
            } else {
                $menu = Kohana::$config->load('menu')->get('guest');
            }            

            $uri    = $this->request->uri();
            $path   = ($uri === '/') ? $uri : ('/' . $uri);
            
            if (isset($menu[$path])) {
                $menu[$path]['class'] .= ' active';
                $this->main_view->page_title = $menu[$path]['name'];
                $this->main_view->page_icon  = $menu[$path]['icon'];
            }
            
            if ($this->js_templates) {
                $this->main_view->js_templates = $this->get_js_templates($this->js_templates);
            }
            
            
            if ($this->view && $this->template) {
                $this->main_view->content = $renderer->render($this->view, $this->template);
            } else {
                if (empty($this->view)) {
                    throw new Exception('Please assign a view to the field: $this->view. You can find the location of the views in: APPPATH/classes/view.');
                } else {
                    $controller = strtolower($this->request->controller());
                    $action     = $this->request->action();
                    //$uri = ($uri === '/') ? 'index/index' : $uri;
                    $this->main_view->content = $renderer->render($this->view, $controller . '/' . $action);
                }
            }
            
            $this->main_view->menu          = array_values($menu);
            $this->main_view->css_files     = $this->css_files;
            $this->main_view->js_files      = $this->js_files;
         
            $this->response->body($renderer->render($this->main_view,  $this->layout));
        }
        return parent::after();
    }

    protected function get_js_templates($js_templates) 
    {
        $js_templates_dir   = dirname(dirname(__DIR__)).'/templates/js/';
        $templates          = array();
        foreach ($js_templates as $template) {
            $templates[] = array('content' => file_get_contents($js_templates_dir.$template.'.mustache'));
        }
        return $templates;
    }
    
    protected function set_facebook_meta($facebook_meta)
    {
        $this->main_view->facebook_meta = $facebook_meta;
        $this->main_view->facebook_meta['app_id'] = Kohana::$config->load('facebook')->get('appId');
    }
    
    protected function set_meta($meta)
    {
        $this->main_view->meta_content = $meta;
    }
    
    protected function add_template($template)
    {
        $this->js_templates[] = $template;
    }
    
    protected function add_js_file($file) {
        $this->js_files[] = array('name' => $file);
    }
    
    protected function add_css_file($file) {
        $this->css_files[] = array('name' => $file);
    }

    protected function exceptionToAjax(Exception $e){
        return 'Exception on line'
                    . '<pre>' . $e->getLine()
                    . '</pre>in file:<pre>' . PHP_EOL . $e->getFile()
                    . '</pre>Message:<pre>' . $e->getMessage() 
                    . '</pre>Stack trace: <pre>' . PHP_EOL . $e->getTraceAsString() . '</pre>';
    }
    
    protected function show_header_active_tasks($booleanValue) {
        $this->show_header_active_tasks = $booleanValue;
    }
    
    protected function not_allowed()
    {
        Session::instance()->set('error_message', 'Access denied.');
        $this->redirect('error');
    }
}