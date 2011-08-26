<?php
return array (
  'frontend_theme' => 'metadatagames',
  'arcade' => 
  array (
    'description' => 'This is a short description of the project',
  ),
  'image' => 
  array (
    'formats' => 
    array (
      'thumbnail' => array (
        'width' => 100,
        'height' => 60,
        'quality' => FALSE, // set to integer 0 ... 100 to activate quality rendering
        'sharpen' => FALSE, // set to integer 0 ... 100 to activate sharpen
      ),
      'large' => array (
        'width' => 750,
        'height' => 750,
        'quality' => 80, // set to integer 0 ... 100 to activate quality rendering quality and sharpen 
        'sharpen' => 20, // set to integer 0 ... 100 to activate sharpen
      ),
    ),
  ),
  'throttle_interval' => 10, 
  'admin-tools' =>
  array (
   'tool-subject-matter' => array(
      'name' => Yii::t('app', 'Subject Matters'),
      'description' => Yii::t('app', 'Some short description'),
      'url' => '/admin/subjectmatter',
      'role' => 'editor',
    ),
    'tool-image-set' => array(
      'name' => Yii::t('app', 'Image Sets'),
      'description' => Yii::t('app', 'Some short description'),
      'url' => '/admin/imageset',
      'role' => 'editor',
    ),
    'tool-licence' => array(
      'name' => Yii::t('app', 'Licences'),
      'description' => Yii::t('app', 'Some short description'),
      'url' => '/admin/licence',
      'role' => 'editor',
    ),   
    'tool-import' => array(
      'name' => Yii::t('app', 'Import'),
      'description' => Yii::t('app', 'Tools to import images or tags (? xxx) into the system'),
      'url' => '/admin/import',
      'role' => 'editor',
    ),
    'tool-user' => array(
      'name' => Yii::t('app', 'User Manager'),
      'description' => Yii::t('app', 'Some short description'),
      'url' => '/admin/user',
      'role' => 'dbmanager',
    ),
    'tool-plugins' => array(
      'name' => Yii::t('app', 'Plugins'),
      'description' => Yii::t('app', 'Some short description'),
      'url' => '/plugins',
      'role' => 'dbmanager',
    ),
  ),
);
