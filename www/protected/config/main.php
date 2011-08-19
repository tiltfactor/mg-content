<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Web Application',

	// preloading 'log' component
	'preload'=>array('log', 'fbvStorage'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.modules.user.models.*',
    'application.modules.user.components.*',
    'ext.giix-components.*',
    'ext.components.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'mg',
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
  	  'generatorPaths' => array(
        'ext.giix-core', // giix generators
      ),
		),
		'admin',
		'user',
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			//'class' => 'WebUser',
			'allowAutoLogin'=>true,
			'loginUrl' => array('/user/login'),
		),
		'authManager'=>array(
            'class'=>'CPhpAuthManager',
            // 'authFile' => 'path'                  // only if necessary
    ),
		// uncomment the following to enable URLs in path-format
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
			  'user/restore-password'=>'user/recovery/recovery',
			  'user/register'=>'user/registration',
        
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=mg',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
			'tablePrefix'=>'',
		),
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
		
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				// xxx comment web log out. xxx configure logging
				array(
					'class'=>'CWebLogRoute',
				),
			),
		),
		
    'fbvStorage'=>array(
      'class'=>'application.components.FBVStorage'
    ),
    
    'clientScript' => array(
      'class' => 'ext.components.NLSClientScript',
      'hashMode' => 'PATH', //PATH|CONTENT
      'bInlineJs' => false
    ),
	),
  
  'behaviors'=>array(
    'onbeginRequest'=>array('class'=>'ext.components.NLSClientScriptBehaviour'),
  ),
  
	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
		'pagination.pageSize'=> 25
	),
);