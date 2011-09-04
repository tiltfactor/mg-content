<?php

class LicenceController extends GxController {

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
			'model' => $this->loadModel($id, 'Licence'),
		));
	}

	public function actionCreate() {
		$model = new Licence;
		$model->created = date('Y-m-d H:i:s'); 
    $model->modified = date('Y-m-d H:i:s'); 
    
		$this->performAjaxValidation($model, 'licence-form');

		if (isset($_POST['Licence'])) {
			$model->setAttributes($_POST['Licence']);

			if ($model->save()) {
        MGHelper::log('create', 'Created Licence with ID(' . $model->id . ')');
				Flash::add('success', Yii::t('app', "Licence created"));
        if (Yii::app()->getRequest()->getIsAjaxRequest())
					Yii::app()->end();
				else 
				  $this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('create', array( 'model' => $model));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id, 'Licence');
    $model->modified = date('Y-m-d H:i:s');
		$this->performAjaxValidation($model, 'licence-form');

		if (isset($_POST['Licence'])) {
			$model->setAttributes($_POST['Licence']);

			if ($model->save()) {
        MGHelper::log('update', 'Updated Licence with ID(' . $id . ')');
        Flash::add('success', Yii::t('app', "Licence updated"));
				$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('update', array(
				'model' => $model,
				));
	}

	public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$model = $this->loadModel($id, 'Licence');
			if ($model->hasAttribute("locked") && $model->locked) {
			  throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
			} else {
			  $model->delete();
			  MGHelper::log('delete', 'Deleted Licence with ID(' . $id . ')');
        
        Flash::add('success', Yii::t('app', "Licence deleted"));

			  if (!Yii::app()->getRequest()->getIsAjaxRequest())
				  $this->redirect(array('admin'));
		  }
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$model = new Licence('search');
    $model->unsetAttributes();

    if (isset($_GET['Licence']))
      $model->setAttributes($_GET['Licence']);

    $this->render('admin', array(
      'model' => $model,
    ));
	}

	public function actionAdmin() {
		$model = new Licence('search');
		$model->unsetAttributes();

		if (isset($_GET['Licence']))
			$model->setAttributes($_GET['Licence']);

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
    if (isset($_POST['licence-ids'])) {
      $criteria=new CDbCriteria;
      $criteria->addInCondition("id", $_POST['licence-ids']);
            MGHelper::log('batch-delete', 'Batch deleted Licence with IDs(' . implode(',', $_POST['licence-ids']) . ')');
        
      $model = new Licence;
      $model->deleteAll($criteria);
        
    } 
  }
}