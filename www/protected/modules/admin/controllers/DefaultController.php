<?php

class DefaultController extends Controller
{
	public function filters() {
    return array(
        'accessControl', 
        );
  }
  
  public function accessRules() {
    return array(
        array('allow', 
          'actions'=>array('index'),
          'roles'=>array('editor', 'dbmanager', 'admin'),
          ),
        array('deny', 
          'users'=>array('*'),
          ),
        );
  }  
    
	public function actionIndex() {
    // renders the view file 'protected/views/admin/index.php'
    // using the default layout 'protected/views/layouts/main.php'
    
    $tools = array();
    $registered_tools = Yii::app()->fbvStorage->get("admin-tools");
    
    foreach ($registered_tools as $tool) {
      if (Yii::app()->user->checkAccess($tool['role'])) {
        $tool['url'] = $this->createUrl($tool['url']);
        $tools[] = (object)$tool;
      }
    }
                         
    $this->render('index',
      array (
        'tools' => $tools 
      )
    );  
	}
}