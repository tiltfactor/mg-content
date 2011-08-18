<?php

Yii::import('application.models._base.BaseGame');

class Game extends BaseGame
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}