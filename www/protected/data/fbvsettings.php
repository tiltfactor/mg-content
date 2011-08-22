<?php
return array (
  'arcade' => 
  array (
    'description' => 'This is a short description of the project',
  ),
  'image' => 
  array (
    'formats' => 
    array (
      "thumbnail" => array (
        "width" => 100,
        "height" => 60,
        "quality" => FALSE, // set to integer 0 ... 100 to activate quality rendering
        "sharpen" => FALSE, // set to integer 0 ... 100 to activate sharpen
      ),
      "large" => array (
        "width" => 750,
        "height" => 750,
        "quality" => 80, // set to integer 0 ... 100 to activate quality rendering quality and sharpen 
        "sharpen" => 20, // set to integer 0 ... 100 to activate sharpen
      ),
    ),
  ),
);
