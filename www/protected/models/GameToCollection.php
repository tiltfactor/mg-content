<?php

Yii::import('application.models._base.BaseGameToCollection');

class GameToCollection extends BaseGameToCollection
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}