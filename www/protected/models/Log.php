<?php

Yii::import('application.models._base.BaseLog');

class Log extends BaseLog
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
  
  public function search() {
    $criteria = new CDbCriteria;

    $criteria->compare('id', $this->id);
    $criteria->compare('category', $this->category, true);
    $criteria->compare('message', $this->message, true);
    $criteria->compare('user_id', $this->user_id);
    $criteria->compare('created', $this->created, true);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination'=>array(
        'pageSize'=> 100,
      ),
    ));
  }
}