<?php

class BlockedIpController extends GxController {

  public function filters() {
  	return array(
  	 'IPBlock',
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
			'model' => $this->loadModel($id, 'BlockedIp'),
		));
	}

	public function actionCreate() {
		$model = new BlockedIp;
		$model->created = date('Y-m-d H:i:s'); 
    $model->modified = date('Y-m-d H:i:s'); 
    
		$this->performAjaxValidation($model, 'blocked-ip-form');

		if (isset($_POST['BlockedIp'])) {
			$model->setAttributes($_POST['BlockedIp']);

			if ($model->save()) {
        MGHelper::log('create', 'Created BlockedIp with ID(' . $model->id . ')');
				Flash::add('success', Yii::t('app', "BlockedIp created"));
        if (Yii::app()->getRequest()->getIsAjaxRequest())
					Yii::app()->end();
				else 
				  $this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('create', array( 'model' => $model));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id, 'BlockedIp');
    $model->modified = date('Y-m-d H:i:s');
		$this->performAjaxValidation($model, 'blocked-ip-form');

		if (isset($_POST['BlockedIp'])) {
			$model->setAttributes($_POST['BlockedIp']);

			if ($model->save()) {
        MGHelper::log('update', 'Updated BlockedIp with ID(' . $id . ')');
        Flash::add('success', Yii::t('app', "BlockedIp updated"));
				$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('update', array(
				'model' => $model,
				));
	}

	public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$model = $this->loadModel($id, 'BlockedIp');
			if ($model->hasAttribute("locked") && $model->locked) {
			  throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
			} else {
			  $model->delete();
			  MGHelper::log('delete', 'Deleted BlockedIp with ID(' . $id . ')');
        
        Flash::add('success', Yii::t('app', "BlockedIp deleted"));

			  if (!Yii::app()->getRequest()->getIsAjaxRequest())
				  $this->redirect(array('admin'));
		  }
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$model = new BlockedIp('search');
    $model->unsetAttributes();

    if (isset($_GET['BlockedIp']))
      $model->setAttributes($_GET['BlockedIp']);

    $this->render('admin', array(
      'model' => $model,
    ));
	}

	public function actionAdmin() {
		$model = new BlockedIp('search');
		$model->unsetAttributes();

		if (isset($_GET['BlockedIp']))
			$model->setAttributes($_GET['BlockedIp']);

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
    if (isset($_POST['blocked-ip-ids'])) {
      $criteria=new CDbCriteria;
      $criteria->addInCondition("id", $_POST['blocked-ip-ids']);
      MGHelper::log('batch-delete', 'Batch deleted BlockedIp with IDs(' . implode(',', $_POST['blocked-ip-ids']) . ')');
        
      $model = new BlockedIp;
      $model->deleteAll($criteria);
        
    } 
  }
}