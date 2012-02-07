<?php
/**
 * This is the base implementation of a mg plugin. Each plugin type has to extend this class. 
 * It holds some small methods used by several plugin types. 
 * 
 * @author Vincent Van Uffelen <novazembla@gmail.com>
 * @link http://tiltfactor.org
 * @license AGPL 3.0
 * @package MG
 */

class MGPlugin extends CComponent {
  /**
   * Set true to allow the plugin system to auto activate the plugin on registration
   */  
  public $enableOnInstall = FALSE;
  
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
  
  /**
   * This function adds a value to the score of a tag. If the key "score" is not set it will set it
   * 
   * @param array $tag the tag to be scored passed by reference
   * @param int $score the score to be added to the tag
   */
  function addScore(&$tag, $score) {
    if (is_array($tag)) {
      if (array_key_exists("score", $tag)) {
        $tag["score"] += $score;
      } else {
        $tag["score"] = $score;
      }
    } 
  }
  
  /**
   * This function adds or substracts a value from the weight of a tag.
   * If If the key "weight" is not set it will set it.
   * 
   * If the weight is 0 it will remain 0 if you don't set $overRideZero
   * 
   * @param array $tag the tag to be scored passed by reference
   * @param float $weight the weight to be added or substracted (pass negative values to substract)
   * @param boolean $overRideZero set to true to set a value that might be zero
   */
  function adjustWeight(&$tag, $weight, $overRideZero=false) {
    if (is_array($tag)) {
      if (array_key_exists("weight", $tag)) {
        if ($tag["weight"] != 0 || $overRideZero) {
          $tag["weight"] = ($weight == 0)? 0 : (float)$tag["weight"] + $weight;
        }
      } else {
        $tag["weight"] = $weight;
      }
    } 
  }
}
