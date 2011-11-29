<?php

class MGRequirementsHelper {
  
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
  
  static function checkFolderPermissions($listFolder) {
    $flag = true;
    $list = "<p>Path to application root: " . Yii::getPathOfAlias('webroot') . "<br/><br/><b>The following Folder and files in application root have to be writable:</b><br/>";
    $ds = DIRECTORY_SEPARATOR;
    
    /* check installer folders */
    $folder_files = array (
      Yii::getPathOfAlias('webroot.assets'),
      Yii::getPathOfAlias('webroot.uploads'),
      Yii::getPathOfAlias('webroot.uploads.badges'),
      Yii::getPathOfAlias('webroot.uploads.badges') . $ds . '1_a.png',
      Yii::getPathOfAlias('webroot.uploads.badges') . $ds . '1_d.png',
      Yii::getPathOfAlias('webroot.uploads.badges') . $ds . '2_a.png',
      Yii::getPathOfAlias('webroot.uploads.badges') . $ds . '2_d.png',
      Yii::getPathOfAlias('webroot.uploads.badges') . $ds . '3_a.png',
      Yii::getPathOfAlias('webroot.uploads.badges') . $ds . '3_d.png',
      Yii::getPathOfAlias('webroot.uploads.badges') . $ds . '4_a.png',
      Yii::getPathOfAlias('webroot.uploads.badges') . $ds . '4_d.png',
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
    
    /*
    if(realpath($_SERVER["SCRIPT_FILENAME"]) !== realpath(__FILE__))
      return Yii::t('yii','$_SERVER["SCRIPT_FILENAME"] must be the same as the entry script file path.');
    */
    if(!isset($_SERVER["REQUEST_URI"]) && isset($_SERVER["QUERY_STRING"]))
      return Yii::t('yii','Either $_SERVER["REQUEST_URI"] or $_SERVER["QUERY_STRING"] must exist.');
  
    if(!isset($_SERVER["PATH_INFO"]) && strpos($_SERVER["PHP_SELF"],$_SERVER["SCRIPT_NAME"]) !== 0)
      return Yii::t('yii','Unable to determine URL path info. Please make sure $_SERVER["PATH_INFO"] (or $_SERVER["PHP_SELF"] and $_SERVER["SCRIPT_NAME"]) contains proper value.');
  
    return '';
  }
  
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

  static function checkImageMagick() {
    return @is_file($path = @exec('which convert'));  
  }

}
