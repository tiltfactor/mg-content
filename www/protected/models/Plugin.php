<?php

Yii::import('application.models._base.BasePlugin');

class Plugin extends BasePlugin
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
  
  public function attributeLabels() {
    return array(
      'id' => Yii::t('app', 'ID'),
      'type' => Yii::t('app', 'Type'),
      'active' => Yii::t('app', 'Active'),
      'unique_id' => Yii::t('app', 'Unique Name'),
      'created' => Yii::t('app', 'Created'),
      'modified' => Yii::t('app', 'Modified'),
    );
  }
  
  public function search() {
    $criteria = new CDbCriteria;

    $criteria->compare('type', $this->type, true);
    $criteria->compare('active', $this->active);
    $criteria->compare('unique_id', $this->unique_id, true);
    $criteria->compare('created', $this->created, true);
    $criteria->compare('modified', $this->modified, true);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination'=>array(
        'pageSize'=>Yii::app()->params['pagination.pageSize'],
      ),
    ));
  }
}