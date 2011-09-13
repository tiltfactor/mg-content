<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();
  
  
  /**
   * The filter method for 'ajaxOnly' filter.
   * If the current request is not an ajax request the user will be shown the API's index page
   * 
   * @param CFilterChain $filterChain the filter chain that the filter is on.
   * 
   */
  public function filterIPBlock($filterChain)
  {
    $filterChain->run();
    // xxx implement 
  }
  
  /**
   * The filter method for 'Throttle' filter.
   * The current microtime of will be compared to the user's last access microtime (which is stored 
   * in the session). If the current time minus the stored time is smaller than the configured throttle interval 
   * (as configured in Global Settings) is the stored time will be actualized and a HTTP 420 'Enhance your calm' 
   * exception will be thrown.    
   * 
   * @param CFilterChain $filterChain the filter chain that the filter is on.
   * 
   */
  public function filterThrottle($filterChain)
  {
    $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
    if (!isset(Yii::app()->session[$api_id .'_LAST_ACCESS'])) {
      Yii::app()->session[$api_id .'_LAST_ACCESS'] = microtime(true);
      $filterChain->run();  
    } else {
      $time = microtime(true);
      $throttle_interval = (int)Yii::app()->fbvStorage->get("settings.throttle_interval", 1500);  
      if (($time - Yii::app()->session[$api_id .'_LAST_ACCESS']) * 1000 > $throttle_interval) {
        Yii::app()->session[$api_id .'_LAST_ACCESS'] = $time;
        $filterChain->run();
      } else {
        Yii::app()->session[$api_id .'_LAST_ACCESS'] = $time;
        throw new CHttpException(420, Yii::t('app', 'Enhance your calm.'));        
      }
    }
  }
  
  /**
   * Returns a JSON encoded response. Sets needed header and ends the Yii app gracefully
   * 
   * @param mixed $var the response that will be json_encoded before it will be returned 
   * @param boolean $noCache IF true header cache-control and expires will be set to ensure that the browser does not cache the response 
   */
  public function jsonResponse($var, $noCache=true) {
    $this->layout=false;
    if ($noCache) {
      header('Cache-Control: no-cache, must-revalidate');
      header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    }
    header('Content-type: application/json');
    echo json_encode($var);
    if(Yii::app()->hasEventHandler('onEndRequest')) {
      ob_start();
      Yii::app()->onEndRequest(new CEvent(Yii::app()));
      ob_end_clean();
    }
    exit();
  }
}