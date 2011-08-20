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
          'actions'=>array('view'),
          'roles'=>array('*'),
          ),
        array('allow', 
          'actions'=>array('index','view', 'minicreate', 'create','update', 'admin','delete'),
          'roles'=>array('editor'),
          ),
        array('deny', 
          'users'=>array('*'),
          ),
        );
  }
    
	public function actionIndex()
	{
		$this->render('index');
	}
}