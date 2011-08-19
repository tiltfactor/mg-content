<?php

class UserController extends Controller
{
	public $defaultAction = 'admin';
	public $layout = '//layouts/column2';
	private $_model;

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return CMap::mergeArray(parent::filters(),array(
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
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete','create','update','view'),
				'roles'=>array('dbmanager'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model = new User('search');
    $model->unsetAttributes();

    if (isset($_GET['User']))
      $model->setAttributes($_GET['User']);

    $this->render('admin', array(
      'model' => $model,
    ));
	}


	/**
	 * Displays a particular model.
	 */
	public function actionView()
	{
		$model = $this->loadModel();
		$this->render('view',array(
			'model'=>$model,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new User;
		$profile=new Profile;
		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			$model->activkey=UserModule::encrypting(microtime().$model->password);
			$model->created = date('Y-m-d H:i:s');
      $model->modified = date('Y-m-d H:i:s');
			$model->lastvisit=date('Y-m-d H:i:s');
      $model->role = 'player';
      
      if (isset($_POST['Profile']))
			 $profile->attributes=$_POST['Profile'];
			
			$profile->user_id=0;
			
			if($model->validate() && $profile->validate()) {
				$model->password=UserModule::encrypting($model->password);
				
        $relatedData = array(
          'games' => $_POST['User']['games'] === '' ? null : $_POST['User']['games'],
          'subjectMatters' => $_POST['User']['subjectMatters'] === '' ? null : $_POST['User']['subjectMatters'],
          );
				
				if($model->saveWithRelated($relatedData)) {
					$profile->user_id=$model->id;
					$profile->save();
				}
				if (Yii::app()->getRequest()->getIsAjaxRequest())
          Yii::app()->end();
        else
          $this->redirect(array('view', 'id' => $model->id));
			} else $profile->validate();
		}
    
		$this->render('create',array(
			'model'=>$model,
			'profile'=>$profile,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionUpdate()
	{
		$model=$this->loadModel();
		$profile=$model->profile;
		if(isset($_POST['User']))
		{
		  if (isset($_POST['User']))
			 $model->attributes=$_POST['User'];
			
			if (isset($_POST['Profile']))
			 $profile->attributes=$_POST['Profile'];
      
      $model->modified = date('Y-m-d H:i:s');

			if($model->validate()&&$profile->validate()) {
				$old_password = User::model()->notsafe()->findByPk($model->id);
				if ($old_password->password!=$model->password) {
					$model->password=UserModule::encrypting($model->password);
					$model->activkey=UserModule::encrypting(microtime().$model->password);
				}
        
        $relatedData = array(
          'games' => $_POST['User']['games'] === '' ? null : $_POST['User']['games'],
          'subjectMatters' => $_POST['User']['subjectMatters'] === '' ? null : $_POST['User']['subjectMatters'],
          );
        
        if($model->saveWithRelated($relatedData)) {
          $profile->save();
          $this->redirect(array('view','id'=>$model->id));  
        }
				
			} else $profile->validate();
		}

		$this->render('update',array(
			'model'=>$model,
			'profile'=>$profile,
		));
	}


	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 */
	public function actionDelete()
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$model = $this->loadModel();
			$profile = Profile::model()->findByPk($model->id);
			$profile->delete();
			$model->delete();
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_POST['ajax']))
				$this->redirect(array('/user/admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}
	
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 */
	public function loadModel()
	{
		if($this->_model===null)
		{
			if(isset($_GET['id'])) {
			  $this->_model=User::model()->notsafe()->findbyPk($_GET['id']);
        
			}
				
			if($this->_model===null)
				throw new CHttpException(404,'The requested page does not exist.');
		}
		return $this->_model;
	}
	
  /**
   * Returns the data model based on the primary key given in the GET variable.
   * If the data model is not found, an HTTP exception will be raised.
   * @param integer the primary key value. Defaults to null, meaning using the 'id' GET variable
   */
  public function loadUser($id=null)
  {
    if($this->_model===null)
    {
      if($id!==null || isset($_GET['id']))
        $this->_model=User::model()->findbyPk($id!==null ? $id : $_GET['id']);
      if($this->_model===null)
        throw new CHttpException(404,'The requested page does not exist.');
    }
    return $this->_model;
  }
}