<?php

class StopWordController extends GxController {

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
  				'actions'=>array('index','view', 'batch', 'create','update', 'admin', 'delete'),
  				'roles'=>array('editor', 'dbmanager', 'admin', 'xxx'), // ammend after creation
  				),
  			array('deny', 
  				'users'=>array('*'),
  				),
  			);
  }

	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'StopWord'),
		));
	}

	public function actionCreate() {
		$model = new StopWord;
		$model->created = date('Y-m-d H:i:s'); 
    $model->modified = date('Y-m-d H:i:s'); 
    
		$this->performAjaxValidation($model, 'stop-word-form');

		if (isset($_POST['StopWord'])) {
			$model->setAttributes($_POST['StopWord']);

			if ($model->save()) {
        MGHelper::log('create', 'Created StopWord with ID(' . $model->id . ')');
				Flash::add('success', Yii::t('app', "StopWord created"));
        if (Yii::app()->getRequest()->getIsAjaxRequest())
					Yii::app()->end();
				else 
				  $this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('create', array( 'model' => $model));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id, 'StopWord');
    $model->modified = date('Y-m-d H:i:s');
		$this->performAjaxValidation($model, 'stop-word-form');

		if (isset($_POST['StopWord'])) {
			$model->setAttributes($_POST['StopWord']);

			if ($model->save()) {
        MGHelper::log('update', 'Updated StopWord with ID(' . $id . ')');
        Flash::add('success', Yii::t('app', "StopWord updated"));
				$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('update', array(
				'model' => $model,
				));
	}

	public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$model = $this->loadModel($id, 'StopWord');
			if ($model->hasAttribute("locked") && $model->locked) {
			  throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
			} else {
			  $model->delete();
			  MGHelper::log('delete', 'Deleted StopWord with ID(' . $id . ')');
        
        Flash::add('success', Yii::t('app', "StopWord deleted"));

			  if (!Yii::app()->getRequest()->getIsAjaxRequest())
				  $this->redirect(array('admin'));
		  }
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$model = new StopWord('search');
    $model->unsetAttributes();

    if (isset($_GET['StopWord']))
      $model->setAttributes($_GET['StopWord']);

    $this->render('admin', array(
      'model' => $model,
    ));
	}

	public function actionAdmin() {
		$model = new StopWord('search');
		$model->unsetAttributes();

		if (isset($_GET['StopWord']))
			$model->setAttributes($_GET['StopWord']);

		$this->render('admin', array(
			'model' => $model,
		));
	}
  
  
  public function actionBatch($op) {
    if (Yii::app()->getRequest()->getIsPostRequest()) {
      switch ($op) {
        case "delete":
          $this->_batchDelete();
          break;
      }
      if (!Yii::app()->getRequest()->getIsAjaxRequest())
        $this->redirect(array('admin'));
    } else
      throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));  
    
  }

  private function _batchDelete() {
    if (isset($_POST['stop-word-ids'])) {
      $criteria=new CDbCriteria;
      $criteria->addInCondition("id", $_POST['stop-word-ids']);
      MGHelper::log('batch-delete', 'Batch deleted StopWord with IDs(' . implode(',', $_POST['stop-word-ids']) . ')');
        
      $model = new StopWord;
      $model->deleteAll($criteria);
        
    } 
  }
}