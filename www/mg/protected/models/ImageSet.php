<?php

Yii::import('application.models._base.BaseImageSet');

class ImageSet extends BaseImageSet
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}