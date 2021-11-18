<?php

// -- Environment setup --------------------------------------------------------

// Load the core Kohana class
require SYSPATH.'classes/Kohana/Core'.EXT;

if (is_file(APPPATH.'classes/Kohana'.EXT))
{
	// Application extends the core
	require APPPATH.'classes/Kohana'.EXT;
}
else
{
	// Load empty core extension
	require SYSPATH.'classes/Kohana'.EXT;
}

/**
 * Set the default time zone.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/timezones
 */
date_default_timezone_set('Europe/Bucharest');

/**
 * Set the default locale.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/function.setlocale
 */
setlocale(LC_ALL, 'en_UK.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @link http://kohanaframework.org/guide/using.autoloading
 * @link http://www.php.net/manual/function.spl-autoload-register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Optionally, you can enable a compatibility auto-loader for use with
 * older modules that have not been updated for PSR-0.
 *
 * It is recommended to not enable this unless absolutely necessary.
 */
//spl_autoload_register(array('Kohana', 'auto_load_lowercase'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @link http://www.php.net/manual/function.spl-autoload-call
 * @link http://www.php.net/manual/var.configuration#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

/**
 * Enable composer autoload libraries
 */
// require DOCROOT . '/vendor/autoload.php';

/**
 * Set the mb_substitute_character to "none"
 *
 * @link http://www.php.net/manual/function.mb-substitute-character.php
 */
mb_substitute_character('none');

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('en-us');

if (isset($_SERVER['SERVER_PROTOCOL']))
{
	// Replace the default protocol.
	HTTP::$protocol = $_SERVER['SERVER_PROTOCOL'];
}

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
if (isset($_SERVER['KOHANA_ENV']))
{
	Kohana::$environment = constant('Kohana::'.strtoupper($_SERVER['KOHANA_ENV']));
}

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php", if set to FALSE uses clean URLS     index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - integer  cache_life  lifetime, in seconds, of items cached              60
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 * - boolean  expose      set the X-Powered-By header                        FALSE
 */
Kohana::init(array(
    'base_url' => '/',
    'errors' => TRUE,
    'caching' => FALSE,
    'index_file' => FALSE
));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
	// 'encrypt'    => MODPATH.'encrypt',    // Encryption supprt
	// 'cache'      => MODPATH.'cache',      // Caching with multiple backends
        'auth'          => MODPATH . 'auth',       // Basic authentication
        'database'      => MODPATH . 'database',   // Database access
	'image '        => MODPATH . 'image',      // Image manipulation
	'orm'           => MODPATH . 'orm',        // Object Relationship Mapping
        'Utilities'     => MODPATH . 'Utilities',
        'Kostache'      => MODPATH . 'KOstache',
        'facebook'      => MODPATH . 'facebook',
        'Uploader'      => MODPATH . 'uploader',
        'uuid'          => MODPATH . 'uuid',
        'PHPExcel'      => MODPATH . 'PHPExcel',
        'pdf'           => MODPATH . 'tcpdf',
        'email'         => MODPATH . 'email'
));

/**
 * Cookie Salt
 * @see  http://kohanaframework.org/3.3/guide/kohana/cookies
 * 
 * If you have not defined a cookie salt in your Cookie class then
 * uncomment the line below and define a preferrably long salt.
 */
Cookie::$salt = '';
Cookie::$domain='';

/**
 * Cookie HttpOnly directive
 * If set to true, disallows cookies to be accessed from JavaScript
 * @see https://en.wikipedia.org/wiki/Session_hijacking
 */
// Cookie::$httponly = TRUE;
/**
 * If website runs on secure protocol HTTPS, allows cookies only to be transmitted
 * via HTTPS.
 * Warning: HSTS must also be enabled in .htaccess, otherwise first request
 * to http://www.example.com will still reveal this cookie
 */
// Cookie::$secure = isset($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] == 'on' ? TRUE : FALSE;

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
Route::set('project/task/change-status','project/task/change-status')
        ->defaults(array(
            'controller'    => 'project',
            'action'        => 'change_task_status'
        ));

Route::set('project/work', 'project/work(/<project_id>(/<parent_id>))')
        ->defaults(array(
            'controller'    => 'project',
            'action'        => 'work'
        ));

Route::set('project/task/users/assign', 'project/task/users/assign')
        ->defaults(array(
            'controller'    => 'project',
            'action'        => 'task_users_assign'
        ));

Route::set('project/task/users','project/task/users/<id>')
        ->defaults(array(
            'controller'    => 'project',
            'action'        => 'task_users'
        ));

Route::set('project/save/simple','project/save/simple/<id>')
        ->defaults(array(
            'controller'    => 'simple',
            'action'        => 'save_project'
        ));

Route::set('project/simple/edit','project/simple/edit(/<id>)')
        ->defaults(array(
            'controller'    => 'simple',
            'action'        => 'edit_project'
        ));

Route::set('project/manage/simple','project/manage/simple/<id>')
        ->defaults(array(
            'controller'    => 'simple',
            'action'        => 'manage_project'
        ));

Route::set('terms', 'terms')
        ->defaults(array(
            'controller'    => 'welcome',
            'action'        => 'terms',
        ));

Route::set('default', '(<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'welcome',
		'action'     => 'index',
	));
