<?php

Yii::import('application.models._base.BaseUserToGame');

class UserToGame extends BaseUserToGame
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
  
  public function banUser($user_id) {
    $command=Yii::app()->db->createCommand()
              ->update('{{user_to_game}}', array('score'=>0), 'user_id=:userID', array (':userID' => $user_id));        
  }
}