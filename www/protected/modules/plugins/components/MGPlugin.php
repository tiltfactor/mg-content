<?php

/**
 * This is the base implementation of a mg plugin 
 */

class MGPlugin extends CComponent {
  /**
   * Set true if the plug-in has got an admin tool
   */  
  public $hasAdmin = FALSE;
  /**
   * If the admin tools path is not the default location please specify it here. 
   * User absolute or relative paths. 
   * 
   * Beware you might have to specify /index.php/ as part of your path if you use 
   * Yii routing 
   */  
  public $adminPath = "";
  
  /**
   * The minimum role needed in order to access the plug-ins admin tools
   */
  public $accessRole = "editor";
  
  function init() {
    parent::init();
  }
  
  function install() {
    return TRUE;
  }
  
  function uninstall() {
    return TRUE;
  }
}
