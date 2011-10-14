<?php

class ImageSetController extends GxController {

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
				'subjectMatters' => $_POST['ImageSet']['subjectMatters'] === '' ? null : $_POST['ImageSet']['subjectMatters'],
				);

			if ($model->saveWithRelated($relatedData)) {
        MGHelper::log('create', 'Created ImageSet with ID(' . $model->id . ')');
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
				'subjectMatters' => $_POST['ImageSet']['subjectMatters'] === '' ? null : $_POST['ImageSet']['subjectMatters'],
				);
      
      if (isset($_POST['ImageSet']['games']))
        $relatedData['games'] = $_POST['ImageSet']['games'] === '' ? null : $_POST['ImageSet']['games'];
        
			if ($model->saveWithRelated($relatedData)) {
        MGHelper::log('update', 'Updated ImageSet with ID(' . $id . ')');
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
			  MGHelper::log('delete', 'Deleted ImageSet with ID(' . $id . ')');
        
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
      MGHelper::log('batch-delete', 'Batch deleted ImageSet with IDs(' . implode(',', $_POST['image-set-ids']) . ')');
        
      $model = new ImageSet;
      $model->deleteAll($criteria);
        
    } 
  }
}