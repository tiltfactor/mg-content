<?php

class LogoutController extends Controller
{
	public $defaultAction = 'logout';
	
  public function filters() {
    return array( // add blocked IP filter here
        'IPBlock',
    );
  }
  
	/**
	 * Logout the current user and redirect to returnLogoutUrl.
	 */
	public function actionLogout()
	{
	  Yii::app()->session->destroy(); //remove all of the session variables.
	  Yii::app()->user->logout();
		$this->redirect(Yii::app()->controller->module->returnLogoutUrl);
	}

}