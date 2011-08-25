<?php

class DefaultController extends Controller
{
	public function actionIndex()
	{
		if (Yii::app()->user->checkAccess('editor')) {
      // renders the view file 'protected/views/admin/index.php'
      // using the default layout 'protected/views/layouts/main.php'
      
      $tools = array();
      $registered_tools = Yii::app()->fbvStorage->get("admin-tools");
      
      foreach ($registered_tools as $tool) {
        if (Yii::app()->user->checkAccess($tool['role'])) {
          $tool['url'] = $this->createUrl($tool['url']);
          $tools[] = $tool;
        }
      }
                           
      $this->render('index',
        array (
          'tools' => $tools 
        )
      );  
    } else {
      throw new CHttpException(403, Yii::t('app', 'Access Denied.'));
    }
	}
}