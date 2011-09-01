<?php

class ImageController extends GxController {

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
			'model' => $this->loadModel($id, 'Image'),
		));
	}

	public function actionCreate() {
		$model = new Image;
		$model->created = date('Y-m-d H:i:s'); 
    $model->modified = date('Y-m-d H:i:s'); 
    
		$this->performAjaxValidation($model, 'image-form');

		if (isset($_POST['Image'])) {
			$model->setAttributes($_POST['Image']);
			$relatedData = array(
				'imageSets' => $_POST['Image']['imageSets'] === '' ? null : $_POST['Image']['imageSets'],
				);

			if ($model->saveWithRelated($relatedData)) {
				Flash::add('success', Yii::t('app', "Image created"));
        if (Yii::app()->getRequest()->getIsAjaxRequest())
					Yii::app()->end();
				else 
				  $this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('create', array( 'model' => $model));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id, 'Image');
    $model->modified = date('Y-m-d H:i:s');
		$this->performAjaxValidation($model, 'image-form');

		if (isset($_POST['Image'])) {
			$model->setAttributes($_POST['Image']);
			$relatedData = array(
				'imageSets' => $_POST['Image']['imageSets'] === '' ? null : $_POST['Image']['imageSets'],
				);

			if ($model->saveWithRelated($relatedData)) {
        Flash::add('success', Yii::t('app', "Image updated"));
				$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('update', array(
				'model' => $model,
				));
	}

	public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$model = $this->loadModel($id, 'Image');
			if ($model->hasAttribute("locked") && $model->locked) {
			  throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
			} else {
			 $model->delete();
        Flash::add('success', Yii::t('app', "Image deleted"));

			  if (!Yii::app()->getRequest()->getIsAjaxRequest())
				  $this->redirect(array('admin'));
		  }
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$model = new Image('search');
    $model->unsetAttributes();

    if (isset($_GET['Image']))
      $model->setAttributes($_GET['Image']);

    $this->render('admin', array(
      'model' => $model,
    ));
	}

	public function actionAdmin() {
		$model = new Image('search');
		$model->unsetAttributes();

		if (isset($_GET['Image']))
			$model->setAttributes($_GET['Image']);

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
    if (isset($_POST['image-ids'])) {
      $criteria=new CDbCriteria;
      $criteria->addInCondition("id", $_POST['image-ids']);
      $criteria->addInCondition("locked", array(0));      
      $model = new Image;
      $model->deleteAll($criteria);  
    } 
  }
}