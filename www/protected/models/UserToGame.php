<?php

Yii::import('application.models._base.BaseUserToGame');

class UserToGame extends BaseUserToGame
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
  
  /**
   * If a user has been banned his/her game's score save in the user_to_game table
   * has to be set to 0
   * 
   * @param int $user_id The user ID of the user whom's game scrore has to be set to 0
   */
  public function banUser($user_id) {
    $command=Yii::app()->db->createCommand()
              ->update('{{user_to_game}}', array('score'=>0), 'user_id=:userID', array (':userID' => $user_id));        
  }
}