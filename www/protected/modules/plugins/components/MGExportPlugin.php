<?php

/**
 * This is the base implementation of a import plug-in 
 */

class MGExportPlugin extends MGPlugin {
  // Is this Export Plugin active by default in the GUI?
  //
  // (This value may be overidden by subclasses in the form()
  // function; don't use the init() function, as it's unclear when/if
  // that's called!)
  public $activeByDefault = false;

  function init() {
    parent::init();
  }

  /**
   *  Is this plugin active?
   */
  function is_active() {
    return (isset($_POST['ExportForm'][get_class($this)]['active']) ?
            $_POST['ExportForm'][get_class($this)]['active'] :
	    $this->activeByDefault);
  }
  
  /**
   * With help of this method you can add additional fields into the form. At a minimum you'll have to add 
   * a field that allows the user to activate the plugin for the current export.
   * 
   * It is adviseable to give to make all additional fields user intput fail proof (by enforcing default values)
   * as the additional fields are currently not automatically validated. You can however provide your own validation.
   * See ImageSetAtImportPlugin for an example.
   * 
   * @param object $form the GxActiveForm rendering the export form
   * @param object $model the ExportForm instance holding the forms values
   */
  function form(&$form, &$model) {}
  
  /**
   * This method will be called at the moment the temporary folder has been created.
   * You could prepare files and folders needed for the processing of the images.
   * 
   * @param object $model the ExportForm instance
   * @param object $command the CDbCommand instance holding all information needed to retrieve the images' data
   * @param string $tmp_folder the full path to the temporary folder
   */
  function preProcess(&$model, &$command, $tmp_folder) {}
  
  /**
   * This method will be called once for each image to be exported. You can make use of the
   * $command to retrieve the image's data and process it as you like
   * 
   * @param object $model the ExportForm instance
   * @param object $command the CDbCommand instance holding all information needed to retrieve the images' data
   * @param string $tmp_folder the full path to the temporary folder
   * @param int $image_id the id of the image that should be exported
   */
  function process(&$model, &$command, $tmp_folder, $image_id) {}
  
  /**
   * This method will be called after all images have been exported.
   * You can make use of this calls to wrap the export. E.g combines all information gathered for the images
   * into one file. 
   * 
   * @param object $model the ExportForm instance
   * @param object $command the CDbCommand instance holding all information needed to retrieve the images' data
   * @param string $tmp_folder the full path to the temporary folder
   */
  function postProcess(&$model, &$command, $tmp_folder) {}
}
