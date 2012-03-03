<?php

/**
 * This is the base implementation of a import plug-in 
 */

class MGImportPlugin extends MGPlugin {  
  function init() {
    parent::init();
  }
  
  /**
   * Provides the ability to add additional fields to the form. Before it will be shown 
   * to the user. 
   * 
   * @param GxActiveForm Widget object to be manipulated
   */
  function form(&$form) {}
  
  /**
   * Callback handler for validation of the form fields added by the form method.
   * 
   * @param Image $image Image model
   * @param Array $errors Array holding information of errors on each form field in the form
   */
  function validate($image, &$errors) {}
  
  /**
   * Callback handler that allows the plugin to process/manipulate the imported images
   * 
   * @param Array $images List of models of the type Image
   */
  function process($images) {}
}
