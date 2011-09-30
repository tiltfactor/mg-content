<?php

Yii::import('application.models._base.BasePlayedGame');

class PlayedGame extends BasePlayedGame
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
  
  /**
   * load played game model based on its primary key
   * 
   * @param int $id the primary key
   * @return mixed null or the model object
   */
  public static function load($id) {
    return PlayedGame::model()->findByPk($id);
  }
}