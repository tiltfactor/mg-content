<?php

Yii::import('application.models._base.BasePlugin');

class Plugin extends BasePlugin
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}