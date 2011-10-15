<?php

class TagUseController extends GxController {
  public $defaultAction = 'admin';
  
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
  				'actions'=>array('view', 'batch', 'update', 'admin'),
  				'roles'=>array('editor', 'dbmanager', 'admin', 'xxx'), // ammend after creation
  				),
  			array('deny', 
  				'users'=>array('*'),
  				),
  			);
  }

	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'TagUse'),
		));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id, 'TagUse');
    		$this->performAjaxValidation($model, 'tag-use-form');

		if (isset($_POST['TagUse'])) {
			$model->setAttributes($_POST['TagUse']);

			if ($model->save()) {
        MGHelper::log('update', 'Updated TagUse with ID(' . $id . ')');
        Flash::add('success', Yii::t('app', "TagUse updated"));
				$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('update', array(
				'model' => $model,
				));
	}

	public function actionAdmin() {
		$model = new TagUse('search');
		$model->unsetAttributes();

		if (isset($_GET['TagUse']))
			$model->setAttributes($_GET['TagUse']);

		$this->render('admin', array(
			'model' => $model,
		));
	}
  
  
  public function actionBatch($op) {
    if (Yii::app()->getRequest()->getIsPostRequest()) {
      switch ($op) {
        case "xxx":
          break;
      }
      if (!Yii::app()->getRequest()->getIsAjaxRequest())
        $this->redirect(array('admin'));
    } else
      throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));  
    
  }
  
  /** 
   * remove if not needed
   */
  private function _batchDelete() {
    if (isset($_POST['tag-use-ids'])) {
      $criteria=new CDbCriteria;
      $criteria->addInCondition("id", $_POST['tag-use-ids']);
            
      MGHelper::log('batch-delete', 'Batch deleted TagUse with IDs(' . implode(',', $_POST['tag-use-ids']) . ')');
        
      $model = new TagUse;
      $model->deleteAll($criteria);
        
    } 
  }
}