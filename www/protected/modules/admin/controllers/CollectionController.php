<?php

class CollectionController extends GxController {

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
  				'roles'=>array('editor', 'dbmanager', 'admin'), 
  				),
  			array('deny', 
  				'users'=>array('*'),
  				),
  			);
  }

	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'Collection'),
		));
	}

	public function actionCreate() {
		$model = new Collection;
		$model->created = date('Y-m-d H:i:s'); 
    $model->modified = date('Y-m-d H:i:s'); 
    
		$this->performAjaxValidation($model, 'collection-form');

		if (isset($_POST['Collection'])) {
			$model->setAttributes($_POST['Collection']);
			$relatedData = array(
				'games' => $_POST['Collection']['games'] === '' ? null : $_POST['Collection']['games'],
				'subjectMatters' => $_POST['Collection']['subjectMatters'] === '' ? null : $_POST['Collection']['subjectMatters'],
				);

			if ($model->saveWithRelated($relatedData)) {
        MGHelper::log('create', 'Created Collection with ID(' . $model->id . ')');
				Flash::add('success', Yii::t('app', "Collection created"));
        if (Yii::app()->getRequest()->getIsAjaxRequest())
					Yii::app()->end();
				else 
				  $this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('create', array( 'model' => $model));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id, 'Collection');
    $model->modified = date('Y-m-d H:i:s');
		$this->performAjaxValidation($model, 'collection-form');

		if (isset($_POST['Collection'])) {
			$model->setAttributes($_POST['Collection']);
			$relatedData = array(
				'subjectMatters' => $_POST['Collection']['subjectMatters'] === '' ? null : $_POST['Collection']['subjectMatters'],
				);
      
      if (isset($_POST['Collection']['games']))
        $relatedData['games'] = $_POST['Collection']['games'] === '' ? null : $_POST['Collection']['games'];
        
			if ($model->saveWithRelated($relatedData)) {
        MGHelper::log('update', 'Updated Collection with ID(' . $id . ')');
        Flash::add('success', Yii::t('app', "Collection updated"));
				$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('update', array(
				'model' => $model,
				));
	}

	public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$model = $this->loadModel($id, 'Collection');
			if ($model->hasAttribute("locked") && $model->locked) {
			  throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
			} else {
			  $model->delete();
			  MGHelper::log('delete', 'Deleted Collection with ID(' . $id . ')');
        
        Flash::add('success', Yii::t('app', "Collection deleted"));

			  if (!Yii::app()->getRequest()->getIsAjaxRequest())
				  $this->redirect(array('admin'));
		  }
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$model = new Collection('search');
    $model->unsetAttributes();

    if (isset($_GET['Collection']))
      $model->setAttributes($_GET['Collection']);

    $this->render('admin', array(
      'model' => $model,
    ));
	}

	public function actionAdmin() {
		$model = new Collection('search');
		$model->unsetAttributes();

		if (isset($_GET['Collection']))
			$model->setAttributes($_GET['Collection']);

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
    if (isset($_POST['collection-ids'])) {
      $criteria=new CDbCriteria;
      $criteria->addInCondition("id", $_POST['collection-ids']);
      $criteria->addInCondition("locked", array(0));      
      MGHelper::log('batch-delete', 'Batch deleted Collection with IDs(' . implode(',', $_POST['collection-ids']) . ')');
        
      $model = new Collection;
      $model->deleteAll($criteria);
        
    } 
  }
}