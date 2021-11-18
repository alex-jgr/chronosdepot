<?php defined('SYSPATH') OR die('No direct script access.');
return array(
    	// Enable the API browser.  TRUE or FALSE
	'api_browser'  => TRUE,
	// Enable these packages in the API browser.  TRUE for all packages, or a string of comma seperated packages, using 'None' for a class with no @package
	// Example: 'api_packages' => 'Kohana,Kohana/Database,Kohana/ORM,None',
	'api_packages' => TRUE,
	// Enables Disqus comments on the API and User Guide pages
	'show_comments' => Kohana::$environment === Kohana::PRODUCTION,
	
	// Leave this alone
	'modules' => array(            
                // This should be the path to this modules userguide pages, without the 'guide/'. Ex: '/guide/modulename/' would be 'modulename'
		'userguide' => array(
			// Whether this modules userguide pages should be shown
			'enabled' => TRUE,
			
			// The name that should show up on the userguide index page
			'name' => 'Userguide',
			// A short description of this module, shown on the index page
			'description' => 'Documentation viewer and api generation.',
			
			// Copyright message, shown in the footer for this module
			'copyright' => '&copy; 2008–2012 Kohana Team',
		),	
		// This should be the path to this modules userguide pages, without the 'guide/'. Ex: '/guide/modulename/' would be 'modulename'
		'auth' => array(
			// Whether this modules userguide pages should be shown
			'enabled' => TRUE,
			// The name that should show up on the userguide index page
			'name' => 'Auth',
			// A short description of this module, shown on the index page
			'description' => 'User authentication and authorization.',
			// Copyright message, shown in the footer for this module
			'copyright' => '&copy; 2008–2012 Kohana Team',
		),
            'database' => array(
			// Whether this modules userguide pages should be shown
			'enabled' => TRUE,
			
			// The name that should show up on the userguide index page
			'name' => 'Database',
			// A short description of this module, shown on the index page
			'description' => 'Database agnostic querying and result management.',
			
			// Copyright message, shown in the footer for this module
			'copyright' => '&copy; 2008–2012 Kohana Team',
		),
                // This should be the path to this modules userguide pages, without the 'guide/'. Ex: '/guide/modulename/' would be 'modulename'
		'image' => array(
			// Whether this modules userguide pages should be shown
			'enabled' => TRUE,
			
			// The name that should show up on the userguide index page
			'name' => 'Image',
			// A short description of this module, shown on the index page
			'description' => 'Image manipulation.',
			
			// Copyright message, shown in the footer for this module
			'copyright' => '&copy; 2008–2012 Kohana Team',
		),
                // This should be the path to this modules userguide pages, without the 'guide/'. Ex: '/guide/modulename/' would be 'modulename'
		'orm' => array(
			// Whether this modules userguide pages should be shown
			'enabled' => TRUE,
			
			// The name that should show up on the userguide index page
			'name' => 'ORM',
			// A short description of this module, shown on the index page
			'description' => 'Official ORM module, a modeling library for object relational mapping.',
			
			// Copyright message, shown in the footer for this module
			'copyright' => '&copy; 2008–2012 Kohana Team',
		)
	),
        // Set transparent class name segments
	'transparent_prefixes' => array(
		'Kohana' => TRUE,
	)
);