<?php

Yii::import('application.models._base.BaseGameToPlugin');

class GameToPlugin extends BaseGameToPlugin
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}