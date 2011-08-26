<?php

class DefaultController extends Controller
{
	public $defaultAction='view';
  
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
    
  public function actionView($id) {
    $this->render('view', array(
      'model' => $this->loadModel($id, 'BlockedIp'),
    ));
  }
  
  public function actionUpdate() {
    $model = new ZenPondForm;

    $this->performAjaxValidation($model, 'zen-pond-form');

    if (isset($_POST['ZenPond'])) {
      $model->setAttributes($_POST['ZenPond']);

      if ($model->save()) {
        Flash::add('success', Yii::t('app', "ZenPond updated"));
        $this->redirect(array('view', 'id' => $model->id));
      }
    }

    $this->render('update', array(
        'model' => $model,
        ));
  }
}