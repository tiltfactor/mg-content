<?php

Yii::import('application.models._base.BaseGame');

class Game extends BaseGame
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
  
  public function attributeLabels() {
    return array(
      'id' => Yii::t('app', 'ID'),
      'active' => Yii::t('app', 'Active'),
      'number_played' => Yii::t('app', 'Number Played'),
      'unique_id' => Yii::t('app', 'Game ID'),
      'created' => Yii::t('app', 'Created'),
      'modified' => Yii::t('app', 'Modified'),
      'imageSets' => null,
      'playedGames' => null,
      'users' => null,
    );
  }
}