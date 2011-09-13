<?php

class ScoreBySubjectMatterController extends GxController
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
    $model = $this->loadModel(array("unique_id" => "weighting-ScoreBySubjectMatterPlugin"), 'ScoreBySubjectMatter');  
    $model->fbvLoad();
    
    $this->render('view', array(
      'model' => $model,
    ));
  }
  
  public function actionUpdate() {
    $model = $this->loadModel(array("unique_id" => "weighting-ScoreBySubjectMatterPlugin"), 'ScoreBySubjectMatter');
    $model->fbvLoad();
    
    $this->performAjaxValidation($model, 'scorebysubjectmatter-form');
    if (isset($_POST['ScoreBySubjectMatter'])) {
      $model->setAttributes($_POST['ScoreBySubjectMatter']);
      
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