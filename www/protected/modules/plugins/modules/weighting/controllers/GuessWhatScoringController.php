<?php

class GuessWhatScoringController extends GxController
{
	public $defaultAction='view';  
    
	public function filters() {
    return array(
      'IPBlock',
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
    $model = $this->loadModel(array("unique_id" => "weighting-GuessWhatScoringPlugin"), 'GuessWhatScoring');  
    $model->fbvLoad();
    
    $this->render('view', array(
      'model' => $model,
    ));
  }
  
  public function actionUpdate() {
    $model = $this->loadModel(array("unique_id" => "weighting-GuessWhatScoringPlugin"), 'GuessWhatScoring');
    $model->fbvLoad();
    
    $this->performAjaxValidation($model, 'guesswhatscoring-form');
    if (isset($_POST['GuessWhatScoring'])) {
      $model->setAttributes($_POST['GuessWhatScoring']);
      
      if ($model->save()) {
        $model->fbvSave();
        MGHelper::log('update', 'Plugin ' . $model->getPluginID() . ' updated');
        Flash::add('success', $model->getPluginID() . ' ' . Yii::t('app', "Updated"));
        $this->redirect(array('view'));
      }
    }
    
    $this->render('update', array(
      'model' => $model,
      ));
  }
}