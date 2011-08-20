<?php

/**
 * This is the base implementation of a dictionary plug-in 
 */

class MGDictionaryPlugin extends MGPlugin {  
  function init() {
    parent::init();
  }
  
  function lookup(array $tags, $user_id=NULL, $image_id=NULL) {}
  
  function wordsToAvoid() {}
  
  function cleanUp() {}
  
  function expand() {} 
  
}
