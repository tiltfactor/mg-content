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
          'roles'=>array('dbmanager', 'admin'),
          ),
        array('deny', 
          'users'=>array('*'),
          ),
        );
  }  
    
  public function actionIndex() {
    // renders the view file 'protected/views/admin/index.php'
    // using the default layout 'protected/views/layouts/main.php'
    
    $games = array(); // xxx loop through all available games
                    
    $this->render('index',
      array (
        'tools' => $games 
      )
    );  
  }
}