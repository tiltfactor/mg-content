<?php

class DefaultController extends Controller
{
	public function filters() {
    return array(
      /*'IPBlock',*/
      'accessControl', 
     );
  }
  
	public function actionIndex()
	{
		$this->render('index');
	}
}