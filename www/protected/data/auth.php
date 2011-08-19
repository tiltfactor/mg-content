<?php
return array (
  'player' => 
  array (
    'type' => 2,
    'description' => 'Authenticated user. Can play games, change their profile and see their recorded game results',
    'bizRule' => '',
    'data' => '',
    'assignments' => 
    array (
      2 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
    ),
  ),
  'editor' => 
  array (
    'type' => 2,
    'description' => 'Access to the following admin tools: Image Administration, Tag Administration, Dictionary Administration?',
    'bizRule' => '',
    'data' => '',
    'children' => 
    array (
      0 => 'player',
    ),
  ),
  'dbmanager' => 
  array (
    'type' => 2,
    'description' => 'Access to nearly all tools.',
    'bizRule' => '',
    'data' => '',
    'children' => 
    array (
      0 => 'player',
      1 => 'editor',
    ),
  ),
  'admin' => 
  array (
    'type' => 2,
    'description' => 'Access/permission to do absolutely everything.',
    'bizRule' => '',
    'data' => '',
    'children' => 
    array (
      0 => 'player',
      1 => 'editor',
      2 => 'dbmanager',
    ),
    'assignments' => 
    array (
      1 => 
      array (
        'bizRule' => NULL,
        'data' => NULL,
      ),
    ),
  ),
);
