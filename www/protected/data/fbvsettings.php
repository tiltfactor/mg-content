<?php
return array (
  'api_id' => 'MG_API',
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
    'tool-image' => 
    array (
      'name' => 'Images',
      'description' => 'Tools to administer images the system',
      'url' => '/admin/image',
      'role' => 'editor',
      'group' => 'Images & Tags',
    ),
    'tool-tag' => 
    array (
      'name' => 'Tags',
      'description' => 'Some short description',
      'url' => '/admin/tag',
      'role' => 'editor',
      'group' => 'Images & Tags',
    ),
    'tool-import' => 
    array (
      'name' => 'Import',
      'description' => 'Tools to import images or tags (? xxx) into the system',
      'url' => '/admin/import',
      'role' => 'editor',
      'group' => 'Images & Tags',
    ),
    'tool-image-set' => 
    array (
      'name' => 'Image Sets',
      'description' => 'Some short description',
      'url' => '/admin/imageSet',
      'role' => 'editor',
      'group' => 'Images & Tags',
    ),
    'tool-licence' => 
    array (
      'name' => 'Licences',
      'description' => 'Some short description',
      'url' => '/admin/licence',
      'role' => 'editor',
      'group' => 'Images & Tags',
    ),
    'tool-user' => 
    array (
      'name' => 'Players Manager',
      'description' => 'Some short description',
      'url' => '/admin/user',
      'role' => 'dbmanager',
      'group' => 'Players',
    ),
    'tool-subject-matter' => 
    array (
      'name' => 'Subject Matters',
      'description' => 'Some short description',
      'url' => '/admin/subjectMatter',
      'role' => 'editor',
      'group' => 'Players',
    ),
    'tool-plugins' => 
    array (
      'name' => 'Plugins',
      'description' => 'Some short description',
      'url' => '/plugins',
      'role' => 'dbmanager',
      'group' => 'Games & Plugins',
    ),
    'tool-games' => 
    array (
      'name' => 'Games',
      'description' => 'Some short description',
      'url' => '/games',
      'role' => 'dbmanager',
      'group' => 'Games & Plugins',
    ),
    'tool-bages' => 
    array (
      'name' => 'Badges',
      'description' => 'Some short description',
      'url' => '/admin/badge',
      'role' => 'editor',
      'group' => 'Games & Plugins',
    ),
    'tool-ip' => 
    array (
      'name' => 'IP Blacklist',
      'description' => 'Some short description',
      'url' => '/admin/blockedIp',
      'role' => 'editor',
      'group' => 'Other',
    ),
    'tool-settings' => 
    array (
      'name' => 'Global Settings',
      'description' => 'Some short description',
      'url' => '/admin/settings',
      'role' => 'dbmanager',
      'group' => 'Other',
    ),
    'tool-logs' => 
    array (
      'name' => 'Admin Tool Log',
      'description' => 'Some short description',
      'url' => '/admin/log',
      'role' => 'dbmanager',
      'group' => 'Other',
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
      'play_once_and_move_on_url' => 'http://www.metadatagames.com',
      'turns' => '4',
      'score_new' => '2',
      'score_match' => '1',
      'score_expert' => '3',
      'image_width' => '450',
      'image_height' => '450',
    ),
  ),
  'settings' => 
  array (
    'app_name' => 'Meta Data Games Test',
    'throttle_interval' => '2500',
    'app_email' => 'sukie@tiltfactor.org',
    'pagination_size' => '25',
    'app_upload_path' => '/../uploads',
    'app_upload_url' => '/uploads',
  ),
);
