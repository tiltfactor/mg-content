<?php

Yii::import('application.models._base.BaseSession');

class Session extends BaseSession
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}