<?php

/**
 * The extension of the plugin module as base for any mg plugin module class.
 */
class MGPluginModel extends Plugin {
  public $active = 0; //active will never be saved in the games FBVStorage settings it is just a handler for the Game database entry
}

/**
 * Unfortunately in PHP 5.2 it is not possible to retrieve the child class name 
 * in a parent class method usind __CLASS__ or get_class.
 * 
 * Hence we have to make sure certain plugin methods are by each plugin component by making use 
 * of this interface
 * @abstract
 */
interface MGPluginModelInterface
{
  /*
   * loads values from the settings file using the FBVStorage compontent
   */  
  public function fbvLoad();
  
  /*
   * saves values from to settings file using the FBVStorage compontent
   */  
  public function fbvSave();
  
  /*
   * generates the unique id of the plugin
   */
  public function getPluginID();
}