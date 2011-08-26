<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class ApiController extends Controller
{
  /**
   * This app id has to be present in all api call comming from the user
   * @const API_APP_ID
   */
  Const API_APP_ID = 'MG_API';
  
  /**
   * The data format the api is responding with. Currently only JSON is allowed.
   * @const 
   */
  Const API_DATA_FORMAT = 'json';
  
  /**
   * Defines the filter that are active for the API controller
   */
  public function filters() {
    return array( // add blocked IP filter here
        'throttle',
        'IPBlock',
        'APIAjaxOnly', // custom filter defined in this class accepts only requests with the header HTTP_X_REQUESTED_WITH === 'XMLHttpRequest'
        'accessControl',
        'sharedSecret - index sharedsecret', // the API is protected by a shared secret this filter ensures that it is regarded 
        );
  }
  
  public function accessRules() {
    return array(
      array('deny', 
        'users'=>array('*'),
        ),
      );
  }
  
  /**
   * This action displays the a default page in case someone tries to consume 
   * the page via the browser.
   */
  public function actionIndex()
  {
    MGHelper::setFrontendTheme();
    $this->layout = '//layouts/minimal';
    $this->render('/default/index');
  }
    
  /**
   * Passes the given data to the currently activated response handler
   * 
   * @param mixed The data that should be returned with the response handler
   */
  public function sendResponse($data = "", $status = NULL) {
    if (!is_null($status)) {
      switch ($status) {
        case 403:
          header('HTTP/1.1 403 Forbidden');
          break;
        
        case 404:
          header('HTTP/1.1 404 Not Found');
          break;
        
        case 420:
          header('HTTP/1.1 420 Enhance You Calm');
          break;
          
        case 500:
          header('HTTP/1.1 500 Internal Server Error');
          break;
      }
    }
    switch (self::API_DATA_FORMAT) {
      case "json":
        $this->jsonResponse($data);
        break;
    }
  }
  
  /**
   * The filter method for 'ajaxOnly' filter.
   * If the current request is not an ajax request the user will be shown the API's index page
   * 
   * @param CFilterChain $filterChain the filter chain that the filter is on.
   * 
   */
  public function filterAPIAjaxOnly($filterChain)
  {
    if(Yii::app()->getRequest()->getIsAjaxRequest())
      $filterChain->run();
    else
      $this->actionIndex();
  }
  
  /**
   * The filter method for 'sharedSecret' filter.
   * This filter checks for the presence of the shared secret in the HTTP_X_... header and compares it 
   * to the current sessions one. This is to protect the users data.
   * 
   * You have to sign every request with the shared secret that can be retrieved via /API/sharedsecret
   * Place it in the request header as HTTP_X_<self::API_APP_ID>_SHARED_SECRET and you are fine. 
   * 
   * @param CFilterChain $filterChain the filter chain that the filter is on.
   * @throws CHttpException if the current request is not an AJAX request.
   */
  public function filterSharedSecret($filterChain)
  {
    $ss = MGHelper::HTTPXHeader(self::API_APP_ID . "_SHARED_SECRET");
    if (!is_null($ss) && $ss === Yii::app()->session[self::API_APP_ID .'_SHARED_SECRET']) {
      $filterChain->run();
    } else  
      throw new CHttpException(400, Yii::t('app', 'Please Share Your True And Well Kept Secret.'));
  }
}