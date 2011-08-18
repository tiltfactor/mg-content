<?php

Yii::import('application.models._base.BaseLog');

class Log extends BaseLog
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}