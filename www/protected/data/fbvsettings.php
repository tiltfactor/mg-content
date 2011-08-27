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
      'thumbnail' => 
      array (
        'width' => 100,
        'height' => 60,
        'quality' => false,
        'sharpen' => false,
      ),
      'large' => 
      array (
        'width' => 750,
        'height' => 750,
        'quality' => 80,
        'sharpen' => 20,
      ),
    ),
  ),
  'throttle_interval' => 10,
  'admin-tools' => 
  array (
    'tool-subject-matter' => 
    array (
      'name' => 'Subject Matters',
      'description' => 'Some short description',
      'url' => '/admin/subjectmatter',
      'role' => 'editor',
    ),
    'tool-image-set' => 
    array (
      'name' => 'Image Sets',
      'description' => 'Some short description',
      'url' => '/admin/imageset',
      'role' => 'editor',
    ),
    'tool-licence' => 
    array (
      'name' => 'Licences',
      'description' => 'Some short description',
      'url' => '/admin/licence',
      'role' => 'editor',
    ),
    'tool-import' => 
    array (
      'name' => 'Import',
      'description' => 'Tools to import images or tags (? xxx) into the system',
      'url' => '/admin/import',
      'role' => 'editor',
    ),
    'tool-user' => 
    array (
      'name' => 'User Manager',
      'description' => 'Some short description',
      'url' => '/admin/user',
      'role' => 'dbmanager',
    ),
    'tool-plugins' => 
    array (
      'name' => 'Plugins',
      'description' => 'Some short description',
      'url' => '/plugins',
      'role' => 'dbmanager',
    ),
    'tool-games' => 
    array (
      'name' => 'Games',
      'description' => 'Some short description',
      'url' => '/games',
      'role' => 'dbmanager',
    ),
  ),
  'games' => 
  array (
    'zenpond' => 
    array (
      'name' => 'Zen Pond',
      'description' => 'This is a short description of Zen PondThis is a short description of Zen PondThis is a short description of Zen Pond',
      'more_info_url' => '',
      'play_once_and_move_on' => '0',
      'turns' => '10',
      'score_new' => '22',
      'score_match' => '12',
      'score_expert' => '32',
    ),
    'ZenPond' => 
    array (
      'name' => 'Zen Pond',
      'description' => 'This is a short description of Zen Pond, This is a short description of Zen Pond,
This is a short description of Zen Pond',
      'more_info_url' => '',
      'play_once_and_move_on' => '0',
      'turns' => '5',
      'score_new' => '2',
      'score_match' => '1',
      'score_expert' => '3',
    ),
  ),
);
