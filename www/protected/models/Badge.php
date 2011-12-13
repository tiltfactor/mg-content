<?php

Yii::import('application.models._base.BaseBadge');

class Badge extends BaseBadge
{
	var $image_inactive;
  var $image_active;
  
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
  
  public function rules() {
    return array(
      array('title, points, image_inactive, image_active', 'required'),
      array('points', 'numerical', 'integerOnly'=>true),
      array('title', 'length', 'max'=>45),
      
      array('image_inactive, image_active', 'file','on'=>'insert',
          'types'=> 'png',
          'maxSize' => 1024 * 256,               
          'tooLarge' => Yii::t('app','The file was larger than 256KB. Please upload a smaller file.'),                
      ),
      array('image_inactive, image_active', 'file','on'=>'update',
          'allowEmpty' => true,
          'types'=> 'png',
          'maxSize' => 1024 * 256,              
          'tooLarge' => Yii::t('app','The file was larger than 256KB. Please upload a smaller file.'),                
      ),
      array('id, title, points', 'safe', 'on'=>'search'),
    );
  }
  
  public function attributeLabels() {
    return array(
      'id' => Yii::t('app', 'ID'),
      'title' => Yii::t('app', 'Title'),
      'points' => Yii::t('app', 'Points'),
      'image_inactive' => Yii::t('app', 'Badge Image (inactive)'),
      'image_active' => Yii::t('app', 'Badge Image (active)'),
    );
  }
}