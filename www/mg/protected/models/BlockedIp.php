<?php

Yii::import('application.models._base.BaseBlockedIp');

class BlockedIp extends BaseBlockedIp
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}