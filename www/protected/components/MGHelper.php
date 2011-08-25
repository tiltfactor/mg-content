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
   */
  public static function bu($url=null) {
    static $baseUrl;
    if ($baseUrl===null)
        $baseUrl=Yii::app()->getRequest()->getBaseUrl();
    return $url===null ? $baseUrl : $baseUrl.'/'.ltrim($url,'/');
  }
}
