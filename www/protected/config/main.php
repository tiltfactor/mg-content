<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
  'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
  'name'=>'Meta Data Games', // no need to change as this is just the default the app is using a value retrieved from fbvStorage
  
  // preloading 'log' component
  'preload'=>array('fbvStorage'),

  // autoloading model and component classes
  'import'=>array(
    'application.models.*',
    'application.components.*',
  ),

  'modules'=>array(),

  // application components
  'components'=>array(
    'fbvStorage'=>array(
      'class'=>'application.components.FBVStorage',
      'checkFile' => false
    ),
  ),
  
  // application-level parameters that can be accessed
  // using Yii::app()->params['paramName']
  'params'=>array(),
);