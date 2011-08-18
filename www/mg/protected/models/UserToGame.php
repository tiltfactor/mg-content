<?php

Yii::import('application.models._base.BaseUserToGame');

class UserToGame extends BaseUserToGame
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}