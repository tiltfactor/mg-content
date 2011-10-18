<?php

Yii::import('application.models._base.BaseTagOriginalVersion');

class TagOriginalVersion extends BaseTagOriginalVersion
{
	public $username;
  public $user_id;
  
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
  
  public static function listTagUseOriginalVersions($tag_use_id) {
    $criteria = new CDbCriteria;
    $criteria->alias = 't';
    $criteria->distinct = true;
    $criteria->select = 't.*, u.username, u.id';
    $criteria->join = " LEFT JOIN user u ON u.id = t.user_id";
    
    $criteria->compare('t.tag_use_id', $tag_use_id);
    
    if(!Yii::app()->request->isAjaxRequest)
        $criteria->order = 't.created DESC';
    
    return new CActiveDataProvider(new TagOriginalVersion, array(
      'criteria' => $criteria,
      'pagination'=>array(
        'pageSize'=>Yii::app()->fbvStorage->get("settings.pagination_size") * 2,
      ),
    ));
  }
  
  public static function listTagOriginalVersions($tag_id) {
    $criteria = new CDbCriteria;
    $criteria->alias = 't';
    $criteria->distinct = true;
    $criteria->select = 't.*, u.username, u.id';
    $criteria->join = " LEFT JOIN user u ON u.id = t.user_id
                        LEFT JOIN tag_use tu ON t.tag_use_id = tu.id";
    
    $criteria->compare('tu.tag_id', $tag_id);

    return new CActiveDataProvider(new TagOriginalVersion, array(
      'criteria' => $criteria,
      'pagination'=>array(
        'pageSize'=>Yii::app()->fbvStorage->get("settings.pagination_size") * 2,
      ),
    ));
  }
  
  public function getUserName() {
    if ($this->username && $this->user_id) {
      return CHtml::link($this->username, array('user/view', 'id' => $this->user_id));
    } else {
      return Yii::t('app', 'Guest'); 
    }
  }
}