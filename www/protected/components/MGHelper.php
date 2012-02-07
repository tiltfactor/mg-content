<?php
/**
 * This class holds serveral static helper methods facilitating tasks throughout MG
 * 
 * @author Vincent Van Uffelen <novazembla@gmail.com>
 * @link http://tiltfactor.org
 * @license AGPL 3.0
 * @package MG
 */


class MGHelper {
  
  /**
   * This methods stores and returns lists of strings in 
   * associates arrays used in drop down/select boxes and/or radio lists. 
   * 
   * @param string $type The name of the list to retrieve
   * @param string $code One value of the list identified by $type
   * @return mixed list of values in associative array or single value 
   */
  public static function itemAlias($type,$code=NULL) {
    $_items = array(
      'active' => array(
        0 => Yii::t('app', 'Not active'),
        1 => Yii::t('app', 'Active'),
      ),
      'locked' => array(
        0 => Yii::t('app', 'Item not locked'),
        1 => Yii::t('app', 'Item locked'),
      ),
      'yes-no' => array(
        0 => Yii::t('app', 'No'),
        1 => Yii::t('app', 'Yes'),
      ),
      'or-and' => array(
        'OR' => Yii::t('app', 'OR'),
        'AND' => Yii::t('app', 'AND'),
      ),
    );
    if (isset($code))
      return isset($_items[$type][$code]) ? $_items[$type][$code] : array();
    else
      return isset($_items[$type]) ? $_items[$type] : array();
  }
  
  /**
   * This method attempts to read the front end theme setting from fbvSettings and 
   * sets this as current theme. It is mainly used in controller showing arcade pages 
   */
  public static function setFrontendTheme() {
    $theme = Yii::app()->fbvStorage->get("frontend_theme", "");
    if ($theme != "")
      Yii::app()->setTheme($theme);
    
  }
  
  /**
   * This is the shortcut to Yii::app()->request->baseUrl
   * If the parameter is given, it will be returned and prefixed with the app baseUrl.
   *
   * @param $url if set the url to append
   * @return string The url
   */
  public static function bu($url=null) {
    static $baseUrl;
    if ($baseUrl===null)
        $baseUrl=Yii::app()->getRequest()->getBaseUrl();
    return $url===null ? $baseUrl : $baseUrl.'/'.ltrim($url,'/');
  }
  
  /**
   * Parses all HTTP_X_... request header values and stores it in an static array.
   * If an header is specified it's value or null will be returned
   * 
   * @param mixed $header The header which value should be retrieved. Omit the leading HTTP_X_ 
   * @return mixed array (all HTTP_X_ headers) or null 
   */
  public static function HTTPXHeader($header="") {
    static $headers = array();
    foreach($_SERVER as $key => $value) {
        if (substr($key, 0, 7) <> 'HTTP_X_') {
            continue;
        }
        $h = str_replace(' ', '-', substr($key, 7));
        $headers[$h] = $value;
    }
    $header = str_replace("HTTP_X_", "", $header);
    
    if (array_key_exists($header, $headers)) {
      return $headers[$header];
    } else {
      return null;
    }
    return $headers;
  } 
  
  /** 
   * Creates a scaled image of an image and saves it in the specified folder. You can set $new_name to "" empty string
   * the system will then generate the scaled image's file name automatically. It follows the follwing pattern
   * $name + '_' + $width + '_' + $height + '.' + extension.
   * 
   * @param string $name the name of the image that can be found in the images subfolder of settings.app_upload_path
   * @param string $new_name the name the image should be save with. Leave blank "" to let the system autogenerate a new file name
   * @param string $folder the subfolder of settings.app_upload_path the image should be saved in. If the folder does not exist the method will attempt to create it     
   * @param int $width the max width to which the image should be scale to
   * @param int $height the max height to which the image should be scale to
   * @param mixed $quality if int the system will save the image with this quality setting. If false a default quality will be applied
   * @param mixed $sharpen if int the system will sharpen the image with this setting. If false the scaled image will not be sharpened
   * @return mixed the file name of the newly generated image or false on error  
   */
  public static function createScaledImage($name, $new_name, $folder, $width, $height, $quality=FALSE, $sharpen=FALSE) {
    $path= realpath(Yii::app()->getBasePath() . Yii::app()->fbvStorage->get("settings.app_upload_path"));
    
    if(!is_dir($path)){
      throw new CHttpException(500, "{$path} does not exists.");
    }else if(!is_writable($path)){
      throw new CHttpException(500, "{$path} is not writable.");
    }
    
    if(!is_dir($path . "/" . $folder)){
      mkdir($path . "/" . $folder);
    }
    
    $file_info = pathinfo($name);
    
    if ($new_name == "") {
      $new_name = $file_info["filename"] . ".mg-scaled." . $width . "_" . $height . "." . $file_info["extension"];
    }
    if (!file_exists($path. '/' . $folder . '/' . $new_name)) {
      $imgCPNT = Yii::app()->image->load($path . "/images/" . $name);
      if ($imgCPNT) {
        $imgCPNT->resize($width, $height, KImage::AUTO);
        if ($quality && (int)$quality != 0)  
          $imgCPNT->quality((int)$quality);
        if ($sharpen && (int)$sharpen != 0)  
          $imgCPNT->sharpen((int)$sharpen);
        
        $imgCPNT->save($path. '/' . $folder . '/' . $new_name);
        return $new_name;
      }
      return false;
    } else {
      return $new_name;
    }
  }
  
  /**
   * Creates a shared secret used in the MG_API and a new entry in the session table.
   * 
   * @param int $user_id The id of the user in the user table
   * @param sting $user_name The name of the user 
   * @param boolean $refresh If true a the system makes sure that a new session will be created for the user. Defaults to false. 
   */
  public static function createSharedSecretAndSession($user_id, $user_name, $refresh=false) {
    $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
    if (!isset(Yii::app()->session[$api_id .'_SHARED_SECRET'])) {
      Yii::app()->session[$api_id .'_SHARED_SECRET'] = uniqid($api_id) . substr(Yii::app()->session->sessionID, 0, 5);
    }
    if (!isset(Yii::app()->session[$api_id .'_SESSION_ID']) || $refresh) {
      $session = new Session;
      $session->username = $user_name;
      $session->ip_address = ip2long(self::getUserHostAddress());
      
      // Some local dev machines aren't returning a proper IP address
      // here (e.g. Sukie running mg under MAMP), so as a quick
      // workaround we'll just provide a placeholder to allow
      // development.
      //
      // TODO: Determine if there is a better fix.
      // TODO: Check if the new function self::getUserHostAddress() provides enough information and remove the next lines
      if(empty($session->ip_address)) {
        // The code expects the IP address to be stored as a 'long'
        // (not a set of dotted octets) in the session array (see
        // above).
        $session->ip_address = "123123123123";
      }
      
      $session->php_sid = Yii::app()->session->sessionID;
      $session->shared_secret = Yii::app()->session[$api_id .'_SHARED_SECRET'];
      if ($user_id) {
        $session->user_id = $user_id;
      }
      $session->created = date('Y-m-d H:i:s'); 
      $session->modified = date('Y-m-d H:i:s');   
      
      if ($session->validate()) {
        $session->save();  
      } else {
        throw new CHttpException(500, Yii::t('app', 'Internal Server Error.'));
      }
      
      Yii::app()->session[$api_id .'_SESSION_ID'] = $session->id;
    }
    return Yii::app()->session[$api_id .'_SHARED_SECRET'];
  }
  
  /**
   * Creates an entry in the log table
   * 
   * @param string $category The category of the action to be logged (create, update, delte, batch_*)
   * @param string $message The information to be logged
   * @param int $user_id The id of the user. If $user_id is null this method will try to set the current user's id
   */
  public static function log($category, $message, $user_id=null) {
    if (is_null($user_id)) 
      $user_id = Yii::app()->user->id;
    
    $sql=" INSERT INTO {{log}}
           (category, message, user_id, created) VALUES
           (:category, :message, :userID, :created)";
           
    $command=Yii::app()->db->createCommand($sql);
    $command->bindValue(':category' ,$category);
    $command->bindValue(':message', $message);
    $command->bindValue(':created', date('Y-m-d H:i:s'));
    $command->bindValue(':userID', $user_id);
    $command->execute();
  }
  
  /**
   * Get the users IP address.
   * 
   * Thanks: Gustavo @ http://www.yiiframework.com/forum/index.php?/topic/13331-improved-request-getuserhost-getuserhostaddress/
   * 
   * @return string the IP addess of the user
   */
  public static function getUserHostAddress() {
    switch(true){
      case isset($_SERVER["HTTP_X_FORWARDED_FOR"]):
        $ip=$_SERVER["HTTP_X_FORWARDED_FOR"];
        break;
      case isset($_SERVER["HTTP_CLIENT_IP"]):
        $ip=$_SERVER["HTTP_CLIENT_IP"];
        break;
      default:
        $ip=$_SERVER["REMOTE_ADDR"]?$_SERVER["REMOTE_ADDR"]:'127.0.0.1'; 
    }
    if (strpos($ip, ', ')>0) {
      $ips = explode(', ', $ip);
      $ip = $ips[0];
    }
    return $ip;
  }
  
  /**
   * removes a folder and its content recurseivly
   * 
   * thanks holger1 at NOSPAMzentralplan dot de http://www.php.net/manual/en/function.rmdir.php#98622
   * 
   * @param string $dir the folder that should be removed 
   */
  public static function rrmdir($dir) {
   if (is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if ($object != "." && $object != "..") {
         if (filetype($dir."/".$object) == "dir") self::rrmdir($dir."/".$object); else unlink($dir."/".$object);
       }
     }
     reset($objects);
     rmdir($dir);
   }
 } 
}
