<?php

Yii::import('application.models._base.BaseMenuItem');

class MenuItem extends BaseMenuItem
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}