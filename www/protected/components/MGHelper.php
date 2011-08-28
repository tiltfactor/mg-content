<?php
/**
 * This class is a collection of helper methods for various tasks
 * 
 */

class MGHelper {
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
    );
    if (isset($code))
      return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
    else
      return isset($_items[$type]) ? $_items[$type] : false;
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
   * xxx if new_name == "" it generates width_height_name file name and returns it
   * 
   */
  public static function createScaledImage($name, $new_name, $folder, $width, $height, $quality=FALSE, $sharpen=FALSE) {
    $path= realpath(Yii::app()->getBasePath() . Yii::app()->params['upload_path']);
    
    if(!is_dir($path)){
      throw new CHttpException(500, "{$path} does not exists.");
    }else if(!is_writable($path)){
      throw new CHttpException(500, "{$path} is not writable.");
    }
    
    if(!is_dir($path . "/" . $folder)){
      mkdir($path . "/" . $folder);
    }
    if ($new_name == "") {
      $new_name = $width . "_" . $height . "_" . $name;
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
}
