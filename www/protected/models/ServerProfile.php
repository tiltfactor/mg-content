<?php

Yii::import('application.models._base.BaseServerProfile');

class ServerProfile extends BaseServerProfile
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}