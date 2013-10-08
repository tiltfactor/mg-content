<?php

Yii::import('application.models._base.BaseCollection');

class Collection extends BaseCollection
{
    const NOACTIVE = 0;
    const ACTIVE = 1;

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public static function itemAlias($type, $code = NULL)
    {
        $_items = array(
            'Ip Restrict' => array(
                self::NOACTIVE => 'Not active',
                self::ACTIVE => 'Active',
            )
        );
        if (isset($code))
            return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
        else
            return isset($_items[$type]) ? $_items[$type] : false;
    }
}