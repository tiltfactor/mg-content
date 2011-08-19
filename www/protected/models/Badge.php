<?php

Yii::import('application.models._base.BaseBadge');

class Badge extends BaseBadge
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}