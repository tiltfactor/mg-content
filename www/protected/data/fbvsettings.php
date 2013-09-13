<?php
return array (
  'api_id' => 'MG_API',
  'installed' => false,
  'frontend_theme' => 'metadatagames',
  'mg-api-url' => 'http://localhost/mgg/index.php/ws/content/wsdl/',
  'token' => '',
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
      'role' => EDITOR,
      'group' => 'Media & Tags',
    ),
    'tool-import' => 
    array (
      'name' => 'Import',
      'description' => 'Import and process media into the system.',
      'url' => '/admin/import',
      'role' => EDITOR,
      'group' => 'Media & Tags',
    ),
    'tool-collection' => 
    array (
      'name' => 'Collections',
      'description' => 'Group media and apply applicable licences.',
      'url' => '/admin/collection',
      'role' => EDITOR,
      'group' => 'Media & Tags',
    ),
    'tool-licence' => 
    array (
      'name' => 'Licences',
      'description' => 'Create licences under which media can be published in the system.',
      'url' => '/admin/licence',
      'role' => EDITOR,
      'group' => 'Media & Tags',
    ),
    'tool-settings' => 
    array (
      'name' => 'Global Settings',
      'description' => 'Configure settings that are used globally in the system.',
      'url' => '/admin/settings',
      'role' => ADMIN,
      'group' => 'Other',
    ),
    'tool-logs' => 
    array (
      'name' => 'Admin Log',
      'description' => 'Access records of changes made using admin tools.',
      'url' => '/admin/log',
      'role' => ADMIN,
      'group' => 'Other',
    ),
    'update-code' => 
    array (
      'name' => 'Update DB',
      'description' => 'Please visit this tool after an update of the code base to make sure the database structure is up-to date.',
      'url' => '/admin/update',
      'role' => ADMIN,
      'group' => 'Other',
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
  ),
);
