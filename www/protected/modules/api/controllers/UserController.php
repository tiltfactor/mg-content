<?php

class UserController extends ApiController {
  
  /**
   * Defines the access rules for this controller
   */  
  public function accessRules() {
    return array(
      array('allow',
        'actions'=>array('index', 'login', 'user', 'passwordrecovery', 'sharedsecret'),
        'users'=>array('*'),
        ),
      array('allow', 
        'actions'=>array('profile', 'passwordchange', 'logout'),
        'roles'=>array('player', 'editor', 'dbmanager', 'admin'),
        ),
      array('deny', 
        'users'=>array('*'),
        ),
      );
  }
  
  /**
   * This action displays the a default page in case someone tries to consume 
   * the page via the browser.
   */
  public function actionIndex() {
    parent::actionIndex();  
  }
  
  /**
   * Returns a shared secret for the user that will be saved in the session. Each further request has 
   * to be signed with this shared secret. This should happen by setting a custom header 
   * HTTP_X_<fbvStorage(api_id)>_SHARED_SECRET
   * 
   * It will return the following array
   * 
   * JSON: it will return either 
   * {shared_secret:'USERS SHARED SECRET'}
   * 
   */
  public function actionSharedSecret() {
    $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
    if (!isset(Yii::app()->session[$api_id .'_SHARED_SECRET'])) {
      Yii::app()->session[$api_id .'_SHARED_SECRET'] = uniqid($api_id) . substr(Yii::app()->session->sessionID, 0, 5);
    }
    if (!isset(Yii::app()->session[$api_id .'_SESSION_ID'])) {
      $session = new Session;
      $session->username = Yii::app()->user->name;
      $session->ip_address = ip2long(Yii::app()->request->userHostAddress);
      $session->php_sid = Yii::app()->session->sessionID;
      $session->shared_secret = Yii::app()->session[$api_id .'_SHARED_SECRET'];
      if (Yii::app()->user->id) {
        $session->user_id = Yii::app()->user->id;
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
    $data = array();
    $data['status'] = "ok";
    $data['shared_secret'] = Yii::app()->session[$api_id .'_SHARED_SECRET'];
    $this->sendResponse($data);
  }
  
  /**
   * This is the login action it expects to receive 
   * 
   * Needs POST request
   * needs fields login and password
   * 
   * JSON: it will return either 
   * {status:'ok'} or HTTP status 400 and {"errors":{"field":["Error Message"]}}
   * 
   * @throws CHttpException if the request is not a Post request or one of the needed fields is not set
   */
  public function actionLogin() {
    if (Yii::app()->getRequest()->getIsPostRequest()
      && isset($_POST['login']) && isset($_POST['password'])) {
      // collect user input data
      Yii::import("application.modules.user.components.UserIdentity");
      Yii::import("application.modules.user.models.UserLogin");
      
      $model = new UserLogin;
      $model->username = $_POST['login'];
      $model->password = $_POST['password'];
      $model->rememberMe = false;
      
      $data = array();
      // validate user input and redirect to previous page if valid
      if($model->validate()) { // validate mean the user's credentials where correct
        $model->setLastVisit();
        $data = array();
        $data['status'] = "ok";
        $this->sendResponse($data);
      } else {
        $data = array();
        $data['status'] = "error";
        $data['errors'] = $model->getErrors();
        $this->sendResponse($data, 403);
      }
    } else {
      throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }  
  }
  
  /**
   * This is the logout action.
   * It has to be called via a GET request. 
   * 
   * The currently logged in user will be logged out and the session destroyed
   * 
   * JSON: it will return 
   * {status:'ok'} or throw an exception
   * 
   * @throws CHttpException if the request is not a GET request
   */
  public function actionLogout() {
    if (Yii::app()->getRequest()->getIsGetRequest()) {
      Yii::app()->session->clear(); //remove all of the session variables. 
      Yii::app()->user->logout();  
      $data = array();
      $data['status'] = "ok";
      $this->sendResponse($data);
    } else {
      throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }  
  }
  
  /**
   * This is the password recovery action action.
   * It has to be called via a POST request.
   * 
   * If receives a user name or email address in a field called "login_or_email". If either
   * name or email are found an password reset email will be generated and send to the user.  
   * 
   * JSON: it will return either 
   * {status:'ok'} or HTTP status 400 and {"errors":{"field":["Error Message"]}}
   * 
   * @throws CHttpException if the request is not a POST request
   */
  public function actionPasswordRecovery() {
    if (Yii::app()->getRequest()->getIsPostRequest()) {
      Yii::import("application.modules.user.components.UFrontendActionHelper");
      Yii::import("application.modules.user.models.UserRecoveryForm");
      $frontendArctions = new UFrontendActionHelper;
      $frontendArctions->passwordRecovery($this);
    } else {
      throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }  
  }
}