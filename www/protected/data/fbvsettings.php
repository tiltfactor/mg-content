<?php
return array (
  'api_id' => 'MG_API',
  'installed' => false,
  'frontend_theme' => 'metadatagames',
  'arcade' => 
  array (
    'description' => 'This is a short description of the project',
  ),
  'media' => 
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
  'admin-tools' => 
  array (
    'tool-media' => 
    array (
      'name' => 'Media',
      'description' => 'Manage media that have been imported and processed.',
      'url' => '/admin/media',
      'role' => 'editor',
      'group' => 'Media & Tags',
    ),
    /*'tool-tag' =>
    array (
      'name' => 'Tags',
      'description' => 'Manage tags that have been created by players.',
      'url' => '/admin/tag',
      'role' => 'editor',
      'group' => 'Media & Tags',
    ),*/
    'tool-import' => 
    array (
      'name' => 'Import',
      'description' => 'Import and process media into the system.',
      'url' => '/admin/import',
      'role' => 'editor',
      'group' => 'Media & Tags',
    ),
    'tool-collection' => 
    array (
      'name' => 'Collections',
      'description' => 'Group media and apply applicable licences.',
      'url' => '/admin/collection',
      'role' => 'editor',
      'group' => 'Media & Tags',
    ),
    'tool-licence' => 
    array (
      'name' => 'Licences',
      'description' => 'Create licences under which media can be published in the system.',
      'url' => '/admin/licence',
      'role' => 'editor',
      'group' => 'Media & Tags',
    ),
    /*'tool-export' =>
    array (
      'name' => 'Export',
      'description' => 'Export tags, tag uses, and tagged media.',
      'url' => '/admin/export',
      'role' => 'editor',
      'group' => 'Media & Tags',
    ),*/
    /*'tool-user' =>
    array (
      'name' => 'Players',
      'description' => 'Manage registered players and the tags they have created.',
      'url' => '/admin/user',
      'role' => 'dbmanager',
      'group' => 'Players',
    ),*/
    'tool-subject-matter' => 
    array (
      'name' => 'Subject Matters',
      'description' => 'Manage subject matter categories in which players can express interest. These values are used to influence media selection and tag weights.',
      'url' => '/admin/subjectMatter',
      'role' => 'editor',
      'group' => 'Players',
    ),
    /*'tool-plugins' =>
    array (
      'name' => 'Plugins',
      'description' => 'Plugins allow the flexible extension of functionality and can be managed here.',
      'url' => '/plugins',
      'role' => 'editor',
      'group' => 'Games & Plugins',
    ),*/
    /*'tool-games' =>
    array (
      'name' => 'Games',
      'description' => 'Manage games.',
      'url' => '/games',
      'role' => 'dbmanager',
      'group' => 'Games & Plugins',
    ),*/
    /*'tool-bages' =>
    array (
      'name' => 'Badges',
      'description' => 'Manage badges that can be achieved by players.',
      'url' => '/admin/badge',
      'role' => 'editor',
      'group' => 'Games & Plugins',
    ),*/
    /*'tool-ip' =>
    array (
      'name' => 'IP Blacklist',
      'description' => 'Restrict access to Metadata Games by whitelisting or blacklisting IP addresses.',
      'url' => '/admin/blockedIp',
      'role' => 'editor',
      'group' => 'Other',
    ),*/
    'tool-settings' => 
    array (
      'name' => 'Global Settings',
      'description' => 'Configure settings that are used globally in the system.',
      'url' => '/admin/settings',
      'role' => 'dbmanager',
      'group' => 'Other',
    ),
    'tool-logs' => 
    array (
      'name' => 'Admin Log',
      'description' => 'Access records of changes made using admin tools.',
      'url' => '/admin/log',
      'role' => 'dbmanager',
      'group' => 'Other',
    ),
    'update-code' => 
    array (
      'name' => 'Update DB',
      'description' => 'Please visit this tool after an update of the code base to make sure the database structure is up-to date.',
      'url' => '/admin/update',
      'role' => 'admin',
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
      'more_info_url' => 'http://www.google.co.uk',
      'play_once_and_move_on' => '0',
      'play_once_and_move_on_url' => '',
      'turns' => '4',
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
      'image_width' => '450',
      'image_height' => '450',
    ),
    'ZenPond' => 
    array (
      'name' => 'Zen Pond',
      'description' => 'Clear your mind with a partner and you will hear the voices of the serene taggers within you. Ohm.',
      'arcade_image' => 'zenpond_arcade.png',
      'more_info_url' => '',
      'turns' => '4',
      'image_width' => '450',
      'image_height' => '450',
      'partner_wait_threshold' => '30',
      'play_against_computer' => '1',
    ),
    'GuessWhat' => 
    array (
      'name' => 'Guess What!',
      'description' => 'Can you guess what the other player\'s media is?',
      'arcade_image' => 'guesswhat_arcade.png',
      'more_info_url' => '',
      'turns' => '4',
      'image_width' => '450',
      'image_height' => '450',
      'image_grid_width' => '150',
      'image_grid_height' => '150',
      'number_guesses' => '3',
      'number_hints' => '1',
      'partner_wait_threshold' => '30',
      'play_against_computer' => '1',
    ),
  ),
  'settings' => 
  array (
    'app_name' => 'Metadata Games',
    'throttle_interval' => '500',
    'message_queue_interval' => '450',
    'app_email' => 'admin@admin.com',
    'pagination_size' => '25',
    'app_upload_path' => '/../uploads',
    'app_upload_url' => '/uploads',
  ),
  'plugins' => 
  array (
    'dictionary' => 
    array (
      'WordsToAvoid' => 
      array (
        'words_to_avoid_threshold' => 10,
      ),
    ),
    'weighting' => 
    array (
      'ScoreBySubjectMatter' => 
      array (
        'score_new' => 2,
        'score_match' => 1,
        'score_new_expert' => 4,
        'score_new_trusted' => 4,
        'score_match_expert' => 3,
        'score_match_trusted' => 3,
      ),
      'ScoreNewMatch' => 
      array (
        'score_new' => 2,
        'score_match' => 1,
      ),
      'TwoPlayerBonus' => 
      array (
        'score_new' => '2',
        'score_match' => '1',
      ),
      'GuessWhatScoring' => 
      array (
        'score_new' => 2,
        'score_match' => 1,
        'score_first_guess' => 5,
        'score_second_guess' => 3,
        'score_third_guess' => 2,
        'additional_weight_first_guess' => 1,
      ),
    ),
  ),
);
