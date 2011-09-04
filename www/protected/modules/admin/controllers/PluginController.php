<?php

class PluginController extends GxController {

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
				'actions'=>array('index','view','update', 'admin','delete'),
				'roles'=>array('editor', 'dbmanager', 'admin', 'xxx'),
				),
			array('deny', 
				'users'=>array('*'),
				),
			);
}

	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'Plugin'),
		));
	}
	public function actionUpdate($id) {
		$model = $this->loadModel($id, 'Plugin');
    $model->modified = date('Y-m-d H:i:s');
		$this->performAjaxValidation($model, 'plugin-form');

		if (isset($_POST['Plugin'])) {
			$model->setAttributes($_POST['Plugin']);
      if ($model->save()) {
				MGHelper::log('update', 'Updated Plugin with ID(' . $id . ')');
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
			$this->loadModel($id, 'Plugin')->delete();
      MGHelper::log('delete', 'Deleted Plugin with ID(' . $id . ')');
      Flash::add('success', Yii::t('app', "Plugin deleted"));  
			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$model = new Plugin('search');
    $model->unsetAttributes();

    if (isset($_GET['Plugin']))
      $model->setAttributes($_GET['Plugin']);

    $this->render('admin', array(
      'model' => $model,
    ));
	}

	public function actionAdmin() {
		$model = new Plugin('search');
		$model->unsetAttributes();

		if (isset($_GET['Plugin']))
			$model->setAttributes($_GET['Plugin']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}