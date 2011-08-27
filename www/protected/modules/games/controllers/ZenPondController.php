<?php

class ZenPondController extends GxController
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
          'actions'=>array('view', 'update'),
          'roles'=>array('dbmanager', 'admin'),
          ),
        array('deny', 
          'users'=>array('*'),
          ),
        );
  }  
    
  public function actionView() {
    $model = new ZenPondForm;  
    $model->load();
    
    $game = $this->loadModel(array("unique_id" => $model->getGameID()), 'Game');
    if ($game) {
      $model->active = $game->active;
    }
    
    $this->render('view', array(
      'model' => $model,
      'game' => $game,
    ));
  }
  
  public function actionUpdate() {
    $model = new ZenPondForm;
    $model->load();
    $game = $this->loadModel(array("unique_id" => $model->getGameID()), 'Game');
    
    $this->performAjaxValidation($model, 'zenpond-form');
    
    if (isset($_POST['ZenPondForm'])) {
      
      $model->setAttributes($_POST['ZenPondForm']);
      
      if ($model->validate()) {
        $game->active = $model->active;
        $game->save();
        $model->save();
        
        Flash::add('success', $model->name . ' ' . Yii::t('app', "Updated"));
        $this->redirect(array('view'));
      }
    }
    
    if ($game) {
      $model->active = $game->active;
    }
    
    $this->render('update', array(
      'model' => $model,
      ));
  }
}