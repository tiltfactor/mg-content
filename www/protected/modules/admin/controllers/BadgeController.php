<?php

class BadgeController extends GxController {

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
			'model' => $this->loadModel($id, 'Badge'),
		));
	}

	public function actionCreate() {
		$model = new Badge;
		 
     
    
		$this->performAjaxValidation($model, 'badge-form');

		if (isset($_POST['Badge'])) {
			$model->setAttributes($_POST['Badge']);

			if ($model->save()) {
        MGHelper::log('create', 'Created Badge with ID(' . $model->id . ')');
				Flash::add('success', Yii::t('app', "Badge created"));
        if (Yii::app()->getRequest()->getIsAjaxRequest())
					Yii::app()->end();
				else 
				  $this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('create', array( 'model' => $model));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id, 'Badge');
    		$this->performAjaxValidation($model, 'badge-form');

		if (isset($_POST['Badge'])) {
			$model->setAttributes($_POST['Badge']);

			if ($model->save()) {
        MGHelper::log('update', 'Updated Badge with ID(' . $id . ')');
        Flash::add('success', Yii::t('app', "Badge updated"));
				$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('update', array(
				'model' => $model,
				));
	}

	public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$model = $this->loadModel($id, 'Badge');
			if ($model->hasAttribute("locked") && $model->locked) {
			  throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
			} else {
			  $model->delete();
			  MGHelper::log('delete', 'Deleted Badge with ID(' . $id . ')');
        
        Flash::add('success', Yii::t('app', "Badge deleted"));

			  if (!Yii::app()->getRequest()->getIsAjaxRequest())
				  $this->redirect(array('admin'));
		  }
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$model = new Badge('search');
    $model->unsetAttributes();

    if (isset($_GET['Badge']))
      $model->setAttributes($_GET['Badge']);

    $this->render('admin', array(
      'model' => $model,
    ));
	}

	public function actionAdmin() {
		$model = new Badge('search');
		$model->unsetAttributes();

		if (isset($_GET['Badge']))
			$model->setAttributes($_GET['Badge']);

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
    if (isset($_POST['badge-ids'])) {
      $criteria=new CDbCriteria;
      $criteria->addInCondition("id", $_POST['badge-ids']);
      MGHelper::log('batch-delete', 'Batch deleted Badge with IDs(' . implode(',', $_POST['badge-ids']) . ')');
        
      $model = new Badge;
      $model->deleteAll($criteria);
        
    } 
  }
}