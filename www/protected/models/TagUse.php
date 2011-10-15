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
  
  public function search() {
    $criteria = new CDbCriteria;

    $criteria->compare('id', $this->id);
    $criteria->compare('image_id', $this->image_id);
    $criteria->compare('tag_id', $this->tag_id);
    $criteria->compare('weight', $this->weight);
    $criteria->compare('type', $this->type, true);
    $criteria->compare('created', $this->created, true);
    $criteria->compare('game_submission_id', $this->game_submission_id);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination'=>array(
        'pageSize'=>Yii::app()->fbvStorage->get("settings.pagination_size") * 2,
      ),
    ));
  }
  
  public static function getUsedTypes() {
    static $types;
    
    if (is_null($types)) {
      $types = array();
      
      $cmd = Yii::app()->db->createCommand()
                    ->select('tu.type')
                    ->from('{{tag_use}} tu');
      $cmd->distinct = true;
      $tag_use_types = $cmd->queryAll();
      
      if ($tag_use_types) {
        foreach ($tag_use_types as $tu_type) {
          $types[$tu_type['type']] = $tu_type['type'];
        }
      }
    }
    return $types;
  }
}