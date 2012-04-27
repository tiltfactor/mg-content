<?php
/**
 * MGRequirementsHelper class file.
 *
 * @author Vincent Van Uffelen <novazembla@gmail.com>
 * @link http://www.metadatagames.com/
 * @license http://www.metadatagames.com/license/
 * @package MG
 */

/**
 * Collection of helper functions for the installer
 *
 * @author Vincent Van Uffelen <novazembla@gmail.com>
 */

class MGRequirementsHelper {
  
  /**
   * Checks whether the gd library is 
   * 
   * @return boolean false 
   */
  static function checkGDVersion() {
    $flag = true;
    if(extension_loaded('gd')) {
      $gdinfo=gd_info();
      if (strpos($gdinfo['GD Version'], '2.') !== FALSE || strpos($gdinfo['GD Version'], '3.') !== FALSE) {
        $flag = false;
      }
    }
    return $flag;
  }
  
  /**
   * Checks the write permissions for subfolders of the MG install
   * 
   * @param boolean If true the string of folder info will be retruned
   * @return mixed boolean (false if not all needed folder are writeable) or string with check result for all the files
   */
  static function checkFolderPermissions($listFolder) {
    $flag = true;
    $list = "<p>Path to application root: " . Yii::getPathOfAlias('webroot') . "<br/><br/><b>The following Folder and files in application root have to be writable:</b><br/>";
    $ds = DIRECTORY_SEPARATOR;
    
    /* check installer folders */
    $folder_files = array (
      Yii::getPathOfAlias('webroot.assets'),
      Yii::getPathOfAlias('webroot.uploads'),
      Yii::getPathOfAlias('application.runtime'),
      Yii::getPathOfAlias('application.config') . $ds . 'main.php',
      Yii::getPathOfAlias('application.data') . $ds . 'fbvsettings.php',
    );
    
    foreach ($folder_files as $f) {
      try {
        @chmod($f, 0766);  
      } catch (Exception $e) {}
      $list .= str_replace(Yii::getPathOfAlias('webroot'), '..', $f) . ': ' . (is_writeable($f)? ' <span style="font-weight:bold;color:green;">ok</span>': ' <span style="font-weight:bold;color:red;">failed</span>') . '<br/>';
      $flag = is_writeable($f) && $flag;
    }
    
    if ($listFolder) {
      return $list;
    } else {
      return $flag;
    }
  }
  
  /**
   * Checks the availablity of several server vars. If they can't be read or are not set the installer cannot progress.
   * 
   * @param string $file the real path the $file
   * @return string emtpy if no error. Error messages if given
   */
  static function checkServerVar($file)
  {
    $vars=array('HTTP_HOST','SERVER_NAME','SERVER_PORT','SCRIPT_NAME','SCRIPT_FILENAME','PHP_SELF','HTTP_ACCEPT','HTTP_USER_AGENT');
    $missing=array();
    foreach($vars as $var)
    {
      if(!isset($_SERVER[$var]))
        $missing[]=$var;
    }
    if(!empty($missing))
      return Yii::t('yii','$_SERVER does not have {vars}.',array('{vars}'=>implode(', ',$missing)));
    
    if(!isset($_SERVER["REQUEST_URI"]) && isset($_SERVER["QUERY_STRING"]))
      return Yii::t('yii','Either $_SERVER["REQUEST_URI"] or $_SERVER["QUERY_STRING"] must exist.');
  
    if(!isset($_SERVER["PATH_INFO"]) && strpos($_SERVER["PHP_SELF"],$_SERVER["SCRIPT_NAME"]) !== 0)
      return Yii::t('yii','Unable to determine URL path info. Please make sure $_SERVER["PATH_INFO"] (or $_SERVER["PHP_SELF"] and $_SERVER["SCRIPT_NAME"]) contains proper value.');
  
    return '';
  }
  
  /**
   * Checks if the GD library is installed
   * 
   * @return string empty if not error
   */
  static function checkGD()
  {
    if(extension_loaded('gd'))
    {
      $gdinfo=gd_info();
      if($gdinfo['FreeType Support'])
        return '';
      return Yii::t('yii','GD installed<br />FreeType support not installed');
    }
    return Yii::t('yii','GD not installed');
  }
  
  /**
   * Checks the current Yii versions
   * 
   * @return string empty if not error
   */
  static function getYiiVersion()
  {
    $coreFile=dirname(__FILE__).'/../framework/YiiBase.php';
    if(is_file($coreFile))
    {
      $contents=file_get_contents($coreFile);
      $matches=array();
      if(preg_match('/public static function getVersion.*?return \'(.*?)\'/ms',$contents,$matches) > 0)
        return $matches[1];
    }
    return '';
  }
  
  /**
   * Check if Image Magick is installed
   * 
   * @return boolean true if the library is installed
   */
  static function checkImageMagick() {
    return @is_file($path = @exec('which convert'));  
  }

}
