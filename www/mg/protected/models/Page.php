<?php

Yii::import('application.models._base.BasePage');

class Page extends BasePage
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}