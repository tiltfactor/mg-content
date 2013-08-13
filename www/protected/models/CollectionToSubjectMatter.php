<?php

Yii::import('application.models._base.BaseCollectionToSubjectMatter');

class CollectionToSubjectMatter extends BaseCollectionToSubjectMatter
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}