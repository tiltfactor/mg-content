<?php

Yii::import('application.models._base.BaseUserToSubjectMatter');

class UserToSubjectMatter extends BaseUserToSubjectMatter
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}