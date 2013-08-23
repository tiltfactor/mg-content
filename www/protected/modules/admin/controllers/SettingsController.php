<?php

class SettingsController extends GxController
{
	public $defaultAction = 'view';
	public $layout = '//layouts/column2';
	private $_model;

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return CMap::mergeArray(parent::filters(),array(
		  /*'IPBlock',*/
			'accessControl', // perform access control for CRUD operations
		));
	}
  
	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow', 
				'actions'=>array('update','view'),
				'roles'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	/**
	 * Displays a particular model.
	 */
	public function actionView()
	{
		$model = new SettingsForm;
    $model->fbvLoad();
		$this->render('view',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionUpdate()
	{
		$model = new SettingsForm;
    $model->fbvLoad();
		
    $this->performAjaxValidation($model, 'settings-form');
    
		if (isset($_POST['SettingsForm'])) {
      $model->setAttributes($_POST['SettingsForm']);

      if ($model->fbvSave()) {
        MGHelper::log('update', 'Updated Global Settings');
        Flash::add('success', Yii::t('app', "Global Settings updated"));
        $this->redirect(array('view'));
      }
    }

    $this->render('update', array(
        'model' => $model,
        ));
	}
}