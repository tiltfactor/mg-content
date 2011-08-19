<?php

Yii::import('application.models._base.BaseMessage');

class Message extends BaseMessage
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}