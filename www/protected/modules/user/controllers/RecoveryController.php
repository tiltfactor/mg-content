<?php

class RecoveryController extends Controller
{
	public $defaultAction = 'recovery';
	
  public function filters() {
    return array( // add blocked IP filter here
        /*'IPBlock',*/
    );
  }
  
	/**
	 * Recovery password
	 */
	public function actionRecovery () {
		MGHelper::setFrontendTheme();  
		$frontendArctions = new UFrontendActionHelper;
    $frontendArctions->passwordRecovery($this);
	}
}