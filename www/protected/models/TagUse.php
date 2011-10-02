<?php

Yii::import('application.models._base.BaseTagUse');

class TagUse extends BaseTagUse
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
  
  public function banUser($user_id) {
    $sql = "  UPDATE tag_use tu
              LEFT JOIN game_submission gs ON gs.id=tu.game_submission_id
              LEFT JOIN session s ON s.id=gs.session_id
              SET weight=0, type = CONCAT(type, '|banned')
              WHERE s.user_id=:userID";
              
    $command=Yii::app()->db->createCommand($sql);        
    $command->bindValue(':userID', $user_id);
    $command->execute();
  }
}