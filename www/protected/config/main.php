<?php
/*
 * This is the config file that will be configured by the installer (by replacing 
 * tokens such as root) and then used to replace main.php 
 */
// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
  'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
  'name'=>'Metadata Games', // no need to change as this is just the default the app is using a value retrieved from fbvStorage

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
    'ext.yii-mail.YiiMailMessage',
  ),

  'modules'=>array(
    'admin',
    'user',
    /*'plugins',*/
    'api',
    /*'games',*/
  ),

  // application components
  'components'=>array(
    'user'=>array(
      'allowAutoLogin'=>true,
      'loginUrl' => array('/user/login'),
    ),
    'authManager'=>array(
      'class'=>'CDbAuthManager',
    ),

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
      'connectionString' => 'mysql:host=localhost;dbname=mgc',
      'emulatePrepare' => true,
      'username' => 'root',
      'password' => '',
      'tablePrefix'=>'',
      'charset' => 'utf8',
    ),
    
    'errorHandler'=>array(
      'errorAction'=>'site/error',
    ),
    
    'log'=>array(
      'class'=>'CLogRouter',
      'routes'=>array(
        array(
          'class'=>'CFileLogRoute',
          'levels'=>'error',
        ),
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
    ),
    
    'image'=>array(
      'class'=>'ext.image.CImageComponent',
      'driver'=>'GD',
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
    'counter' => array(
      'class' => 'ext.usercounter.UserCounter',
    ),
  ),
  
  // application-level parameters that can be accessed
  // using Yii::app()->params['paramName']
  'params'=>array(
    'version' => "0.1",
    'tags_csv_format' => "1.0",
    'tags_by_game_csv_format' => "1.0",
    'embedded_metadata_format' => "1.0",
  ),
);