<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'MetaData Games', // no need to change as this is just the default the app is using a value retrieved from fbvStorage

	// preloading 'log' component
	'preload'=>array('log', 'fbvStorage'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
    'ext.giix-components.*',
    'ext.components.*',
    'ext.yii-flash.*',
    'application.helpers.*',
    'application.modules.games.*',
    'application.modules.plugins.*',
    'application.modules.plugins.components.*',
    'ext.yii-mail.YiiMailMessage',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		/*
    'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>false,
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
  	  'generatorPaths' => array(
        'ext.giix-core', // giix generators
      ),
		),
    */
		'admin',
		'user',
		'plugins',
		'api',
		'games',
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
      'class'=>'CDbAuthManager',
    ),
		// uncomment the following to enable URLs in path-format
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
			  'user/login/restore-password' => 'user/recovery/recovery',
        'user/login/restore-password/<activekey:.+>/<email:.+>' => 'user/recovery/recovery/<activekey>/<email>',
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
    
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=mg', //xxx set via installer
			'emulatePrepare' => true,
			'username' => 'mg', //xxx set via installer
			'password' => 'mg789', //xxx set via installer
			'charset' => 'utf8',
			'tablePrefix'=>'', //xxx set via installer
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
					'levels'=>'error',
				),
				// uncomment the following to show log messages on web pages
				// xxx comment web log out. xxx configure logging
				/*
				array(
					'class'=>'CWebLogRoute',
				),
        */
			),
		),
		
    'fbvStorage'=>array(
      'class'=>'application.components.FBVStorage'
    ),
    
    'xUploadWidget' => array(
      'class' => 'ext.xupload.XUploadWidget',
    ),
    
    'clientScript' => array(
      'class' => 'ext.components.NLSClientScript',
      'hashMode' => 'PATH', //PATH|CONTENT
      'bInlineJs' => false
    ),
    
    'image'=>array(
      'class'=>'ext.image.CImageComponent',
      'driver'=>'ImageMagick',
    ),
    
    'mail' => array(
      'class' => 'ext.yii-mail.YiiMail',
      'transportType' => 'php',
      'viewPath' => 'application.views.mail',
      'logging' => true,
      'dryRun' => false
    ),
    'zip'=>array(
      'class'=>'application.extensions.zip.EZip', 
    ),
	),
  
  'behaviors'=>array(
    'onbeginRequest'=>array('class'=>'ext.components.NLSClientScriptBehaviour'),
  ),
  
	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(),
);