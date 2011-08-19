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
          'roles'=>array('admin'),
          ),
        array('deny', 
          'users'=>array('*'),
          ),
        );
  }

  public function actionView($id) {
    $this->render('view', array(
      'model' => $this->loadModel($id, 'Plugin'),
    ));
  }

  public function actionCreate() {
    $model = new Plugin;
    $model->created = date('Y-m-d H:i:s');
    $model->modified = date('Y-m-d H:i:s');
    
    $this->performAjaxValidation($model, 'plugin-form');

    if (isset($_POST['Plugin'])) {
      $model->setAttributes($_POST['Plugin']);

      if ($model->save()) {
        if (Yii::app()->getRequest()->getIsAjaxRequest())
          Yii::app()->end();
        else
          $this->redirect(array('view', 'id' => $model->id));
      }
    }

    $this->render('create', array( 'model' => $model));
  }

  public function actionUpdate($id) {
    $model = $this->loadModel($id, 'Plugin');
    $model->modified = date('Y-m-d H:i:s');
    $this->performAjaxValidation($model, 'plugin-form');

    if (isset($_POST['Plugin'])) {
      $model->setAttributes($_POST['Plugin']);

      if ($model->save()) {
        $this->redirect(array('view', 'id' => $model->id));
      }
    }

    $this->render('update', array(
        'model' => $model,
        ));
  }

  public function actionDelete($id) {
    if (Yii::app()->getRequest()->getIsPostRequest()) {
      $this->loadModel($id, 'Plugin')->delete();

      if (!Yii::app()->getRequest()->getIsAjaxRequest())
        $this->redirect(array('admin'));
    } else
      throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
  }

  public function actionIndex() {
    $model = new Plugin('search');
    $model->unsetAttributes();

    if (isset($_GET['Plugin']))
      $model->setAttributes($_GET['Plugin']);

    $this->render('admin', array(
      'model' => $model,
    ));
  }

  public function actionAdmin() {
    $model = new Plugin('search');
    $model->unsetAttributes();

    if (isset($_GET['Plugin']))
      $model->setAttributes($_GET['Plugin']);

    $this->render('admin', array(
      'model' => $model,
    ));
  }
}