<?php

/**
 * This is the base implementation of a dictionary plugin 
 */

class DictionaryPlugin extends CApplicationComponent {  
  public $hasAdmin = FALSE;
  public $adminPath = "";
  
  function init() {
    parent::init();
  }
  
  function lookup(array $tags, $user_id=NULL, $image_id=NULL) {}
  
  function wordsToAvoid() {}
  
  function cleanUp() {}
  
  function expand() {} 
  
  function install() {
    return TRUE;
  }
  
}
