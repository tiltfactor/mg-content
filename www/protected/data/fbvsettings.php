<?php
return array (
  'api_id' => 'MG_API',
  'frontend_theme' => 'metadatagames',
  'arcade' => 
  array (
    'description' => 'This is a short description of the project',
  ),
  'throttleInterval' => 5,
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
      'url' => '/admin/subjectMatter',
      'role' => 'editor',
    ),
    'tool-image-set' => 
    array (
      'name' => 'Image Sets',
      'description' => 'Some short description',
      'url' => '/admin/imageSet',
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
    'tool-image' => 
    array (
      'name' => 'Images',
      'description' => 'Tools to administer images the system',
      'url' => '/admin/image',
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
    'tool-tag' => 
    array (
      'name' => 'Tags',
      'description' => 'Some short description',
      'url' => '/admin/tag',
      'role' => 'editor',
    ),
    'tool-ip' => 
    array (
      'name' => 'IP Blacklist',
      'description' => 'Some short description',
      'url' => '/admin/blockedIp',
      'role' => 'editor',
    ),
    'tool-bages' => 
    array (
      'name' => 'Badges',
      'description' => 'Some short description',
      'url' => '/admin/badge',
      'role' => 'editor',
    ),
  ),
  'games' => 
  array (
    'ZenTag' => 
    array (
      'name' => 'Zen Tag',
      'description' => 'Clear your mind and you will hear the voice of the serene tagger within you. Ohm.',
      'arcade_image' => 'zentag_arcade.png',
      'more_info_url' => '',
      'play_once_and_move_on' => '0',
      'play_once_and_move_on_url' => '',
      'turns' => '4',
      'score_new' => '2',
      'score_match' => '1',
      'score_expert' => '3',
      'image_width' => '450',
      'image_height' => '450',
    ),
    'ZenTagPlayOnceMoveOn' => 
    array (
      'name' => 'Zen Tag (Play Once Move On)',
      'description' => 'Clear your mind and you will hear the voice of the serene tagger within you. Ohm.',
      'arcade_image' => 'zentag_arcade.png',
      'more_info_url' => '',
      'play_once_and_move_on' => '1',
      'play_once_and_move_on_url' => 'http://metadatagames.test/index.php/site/contact',
      'turns' => '4',
      'score_new' => '2',
      'score_match' => '1',
      'score_expert' => '3',
      'image_width' => '450',
      'image_height' => '450',
    ),
  ),
);
