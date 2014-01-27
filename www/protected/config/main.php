<?php
/*
 * This is the initial config file that strips Yii & MG down to the bare essentials
 * ensuring that the system allows to redirect to install.php.
 *
 * The installer will copy main.install.php over this file
 */
// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
  'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
  'name'=>'Meta Data Games Content Build', // no need to change as this is just the default the app is using a value retrieved from fbvStorage

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
  'params'=>array(
    'version' => "0.1",
    'tags_csv_format' => "1.0",
    'embedded_metadata_format' => "1.0",
  ),
);