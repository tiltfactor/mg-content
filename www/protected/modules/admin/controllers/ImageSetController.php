<?php

class ImageSetController extends GxController {

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
			'model' => $this->loadModel($id, 'ImageSet'),
		));
	}

	public function actionCreate() {
		$model = new ImageSet;
		$model->created = date('Y-m-d H:i:s'); 
    $model->modified = date('Y-m-d H:i:s'); 
    
		$this->performAjaxValidation($model, 'image-set-form');

		if (isset($_POST['ImageSet'])) {
			$model->setAttributes($_POST['ImageSet']);
			$relatedData = array(
				'games' => $_POST['ImageSet']['games'] === '' ? null : $_POST['ImageSet']['games'],
				'images' => $_POST['ImageSet']['images'] === '' ? null : $_POST['ImageSet']['images'],
				'subjectMatters' => $_POST['ImageSet']['subjectMatters'] === '' ? null : $_POST['ImageSet']['subjectMatters'],
				);

			if ($model->saveWithRelated($relatedData)) {
				Flash::add('success', Yii::t('app', "ImageSet created"));
        if (Yii::app()->getRequest()->getIsAjaxRequest())
					Yii::app()->end();
				else 
				  $this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('create', array( 'model' => $model));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id, 'ImageSet');
    $model->modified = date('Y-m-d H:i:s');
		$this->performAjaxValidation($model, 'image-set-form');

		if (isset($_POST['ImageSet'])) {
			$model->setAttributes($_POST['ImageSet']);
			$relatedData = array(
				'games' => $_POST['ImageSet']['games'] === '' ? null : $_POST['ImageSet']['games'],
				'images' => $_POST['ImageSet']['images'] === '' ? null : $_POST['ImageSet']['images'],
				'subjectMatters' => $_POST['ImageSet']['subjectMatters'] === '' ? null : $_POST['ImageSet']['subjectMatters'],
				);

			if ($model->saveWithRelated($relatedData)) {
        Flash::add('success', Yii::t('app', "ImageSet updated"));
				$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('update', array(
				'model' => $model,
				));
	}

	public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$model = $this->loadModel($id, 'ImageSet');
			if ($model->hasAttribute("locked") && $model->locked) {
			  throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
			} else {
			 $model->delete();
        Flash::add('success', Yii::t('app', "ImageSet deleted"));

			  if (!Yii::app()->getRequest()->getIsAjaxRequest())
				  $this->redirect(array('admin'));
		  }
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$model = new ImageSet('search');
    $model->unsetAttributes();

    if (isset($_GET['ImageSet']))
      $model->setAttributes($_GET['ImageSet']);

    $this->render('admin', array(
      'model' => $model,
    ));
	}

	public function actionAdmin() {
		$model = new ImageSet('search');
		$model->unsetAttributes();

		if (isset($_GET['ImageSet']))
			$model->setAttributes($_GET['ImageSet']);

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
    if (isset($_POST['image-set-ids'])) {
      $criteria=new CDbCriteria;
      $criteria->addInCondition("id", $_POST['image-set-ids']);
      $criteria->addInCondition("locked", array(0));      
      $model = new ImageSet;
      $model->deleteAll($criteria);  
    } 
  }
}