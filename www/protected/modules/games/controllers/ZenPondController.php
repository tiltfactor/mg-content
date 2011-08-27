<?php

class ZenPondController extends GxController
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
          'users'=>array('*'),
          ),
        array('allow', 
          'actions'=>array('view', 'update'),
          'roles'=>array('dbmanager', 'admin'),
          ),
        array('deny', 
          'users'=>array('*'),
          ),
        );
  }  
  public function actionIndex() {
    MGHelper::setFrontendTheme();
    
    $model = new ZenPondForm;  
    $model->load();
    
    $game = $this->loadModel(array("unique_id" => $model->getGameID()), 'Game');
    
    if ($game->active) {
      Yii::app()->clientScript->registerCssFile($this->module->getAssetsUrl() . '/zenpond/css/style.css');
      Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/mg.api.js', CClientScript::POS_END);
      Yii::app()->clientScript->registerScriptFile($this->module->getAssetsUrl() . '/zenpond/js/mg.game.zenpond.js', CClientScript::POS_END);
      
      if ($model->play_once_and_move_on == 1) {
        $this->layout = '//layouts/main_no_menu';
      } else {
        $this->layout = '//layouts/column1';
      }
      $this->render('index', array(
        'model' => $model,
        'game' => $game,
      ));  
    } else {
      throw new CHttpException(403, Yii::t('app', 'The game is not active.'));
    }
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