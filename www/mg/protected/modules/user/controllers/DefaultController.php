<?php

class DefaultController extends Controller
{
	
	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Users', array(
			'criteria'=>array(
		        'condition'=>'status>'.User::STATUS_BANNED,
		    ),
			'pagination'=>array(
				'pageSize'=>Yii::app()->params['pagination.pagesize'],
			),
		));

		$this->render('/user/index',array(
			'dataProvider'=>$dataProvider,
		));
	}

}