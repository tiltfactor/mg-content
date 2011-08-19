<?php

Yii::import('application.models._base.BaseStopWord');

class StopWord extends BaseStopWord
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}