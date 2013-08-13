<?php

Yii::import('application.models._base.BaseCollection');

class Collection extends BaseCollection
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}