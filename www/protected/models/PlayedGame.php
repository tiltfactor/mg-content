<?php

Yii::import('application.models._base.BasePlayedGame');

class PlayedGame extends BasePlayedGame
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}