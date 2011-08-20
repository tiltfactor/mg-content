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
    );
    if (isset($code))
      return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
    else
      return isset($_items[$type]) ? $_items[$type] : false;
  }
}
