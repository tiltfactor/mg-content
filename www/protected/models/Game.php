<?php

Yii::import('application.models._base.BaseGame');

class Game extends BaseGame
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
  
  public function relations() {
    return array(
      'collections' => array(self::MANY_MANY, 'Collection', 'game_to_collection(game_id, collection_id)'),
      'playedGames' => array(self::HAS_MANY, 'PlayedGame', 'game_id'),
      'plugins' => array(self::MANY_MANY, 'Plugin', 'game_to_plugin(game_id, plugin_id)'),
      'users' => array(self::MANY_MANY, 'User', 'user_to_game(game_id, user_id)'),
    );
  }
  
  public function attributeLabels() {
    return array(
      'id' => Yii::t('app', 'ID'),
      'active' => Yii::t('app', 'Active'),
      'number_played' => Yii::t('app', 'Number Played'),
      'unique_id' => Yii::t('app', 'Game ID'),
      'created' => Yii::t('app', 'Created'),
      'modified' => Yii::t('app', 'Modified'),
      'collections' => null,
      'plugins' => null,
      'playedGames' => null,
      'users' => null,
    );
  }
}