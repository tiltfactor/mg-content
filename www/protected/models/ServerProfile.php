<?php

Yii::import('application.models._base.BaseServerProfile');

class ServerProfile extends BaseServerProfile
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function rules() {
        $rules = parent::rules();

        $rules = array_merge($rules,array(array('logo', 'file', 'types'=>'jpg, gif, png','on' => 'create')));
        return $rules;
    }
    public function canDelete()
    {
        return false;
    }

    public function canCreate()
    {
        return false;
    }
}