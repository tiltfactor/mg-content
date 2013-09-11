<?php

class LogController extends GxController {

  public function filters() {
  	return array(
      /*'IPBlock',*/
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
  				'actions'=>array('index','view', 'batch','update', 'admin', 'delete'),
  				'roles'=>array(EDITOR, ADMIN),
  				),
  			array('deny', 
  				'users'=>array('*'),
  				),
  			);
  }

	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'Log'),
		));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id, 'Log');
    		$this->performAjaxValidation($model, 'log-form');

		if (isset($_POST['Log'])) {
			$model->setAttributes($_POST['Log']);

			if ($model->save()) {
        MGHelper::log('update', 'Updated Log with ID(' . $id . ')');
        Flash::add('success', Yii::t('app', "Log updated"));
				$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('update', array(
				'model' => $model,
				));
	}

	public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$model = $this->loadModel($id, 'Log');
			if (!$model->canDelete()) {
			  throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
			} else {
			  $model->delete();
			  MGHelper::log('delete', 'Deleted Log with ID(' . $id . ')');
        Flash::add('success', Yii::t('app', "Log deleted"));

			  if (!Yii::app()->getRequest()->getIsAjaxRequest())
				  $this->redirect(array('admin'));
		  }
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$model = new Log('search');
    $model->unsetAttributes();

    if (isset($_GET['Log']))
      $model->setAttributes($_GET['Log']);

    $this->render('admin', array(
      'model' => $model,
    ));
	}

	public function actionAdmin() {
		$model = new Log('search');
		$model->unsetAttributes();

		if (isset($_GET['Log']))
			$model->setAttributes($_GET['Log']);

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
    if (isset($_POST['log-ids'])) {
      $criteria=new CDbCriteria;
      $criteria->addInCondition("id", $_POST['log-ids']);
      MGHelper::log('batch-delete', 'Batch deleted Log with IDs(' . implode(',', $_POST['log-ids']) . ')');
        
      $model = new Log;
      $model->deleteAll($criteria);
        
    } 
  }
}