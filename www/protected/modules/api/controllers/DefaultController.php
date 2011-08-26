<?php

class DefaultController extends ApiController {

  /**
   * Defines the access rules for this controller
   */  
  public function accessRules() {
    return array(
      array('allow',
        'actions'=>array('index'),
        'users'=>array('*'),
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
  
}