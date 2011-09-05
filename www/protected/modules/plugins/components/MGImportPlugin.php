<?php

/**
 * This is the base implementation of a import plug-in 
 */

class MGImportPlugin extends MGPlugin {  
  function init() {
    parent::init();
  }
  
  function rules() {}
  function form(&$form) {}
  function process($images) {}
}
