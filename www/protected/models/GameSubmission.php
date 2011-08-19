<?php

Yii::import('application.models._base.BaseGameSubmission');

class GameSubmission extends BaseGameSubmission
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}