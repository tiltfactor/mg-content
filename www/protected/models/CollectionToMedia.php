<?php

Yii::import('application.models._base.BaseCollectionToMedia');

class CollectionToMedia extends BaseCollectionToMedia
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}