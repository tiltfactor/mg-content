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
   * The filter method for 'ajaxOnly' filter.
   * If the current request is not an ajax request the user will be shown the API's index page
   * 
   * @param CFilterChain $filterChain the filter chain that the filter is on.
   * 
   */
  public function filterThrottle($filterChain)
  {
    $filterChain->run();
    // xxx implement 
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
      header('Cache-Control: no-cache, must-revalidate'); // xxx really no caching?
      header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // xxx really no caching?
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