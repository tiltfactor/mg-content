<?php

class RecoveryController extends Controller
{
	public $defaultAction = 'recovery';
	
	/**
	 * Recovery password
	 */
	public function actionRecovery () {
		MGHelper::setFrontendTheme();  
		$frontendArctions = new UFrontendActionHelper;
    $frontendArctions->passwordRecovery($this);
	}
}