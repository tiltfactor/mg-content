<?php

Yii::import('application.models._base.BaseStopWords');

class StopWords extends BaseStopWords
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}