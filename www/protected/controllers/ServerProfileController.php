<?php
class ServerProfileController extends GxController {


	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'ServerProfile'),
		));
	}

	public function actionCreate() {
		$model = new ServerProfile;


		if (isset($_POST['ServerProfile'])) {
			$model->setAttributes($_POST['ServerProfile']);

			if ($model->save()) {
				if (Yii::app()->getRequest()->getIsAjaxRequest()) {
                    Yii::app()->end();
                } else {
                    $this->redirect(Yii::app()->baseUrl . '/index.php/installer/Configuration');
                }
			}
		}

		$this->render('create', array( 'model' => $model));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id, 'ServerProfile');


		if (isset($_POST['ServerProfile'])) {
			$model->setAttributes($_POST['ServerProfile']);

			if ($model->save()) {
				$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('update', array(
				'model' => $model,
				));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('ServerProfile');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new ServerProfile('search');
		$model->unsetAttributes();

		if (isset($_GET['ServerProfile']))
			$model->setAttributes($_GET['ServerProfile']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}