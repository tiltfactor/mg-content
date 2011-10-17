<?php

Yii::import('application.models._base.BaseTagUse');

class TagUse extends BaseTagUse
{
  public $username;
  public $user_id;
  
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
  
  public function rules() {
    return array(
      array('image_id, tag_id, created, game_submission_id', 'required'),
      array('image_id, tag_id, weight', 'numerical', 'integerOnly'=>true),
      array('type', 'length', 'max'=>64),
      array('game_submission_id', 'length', 'max'=>10),
      array('weight, type', 'default', 'setOnEmpty' => true, 'value' => null),
      array('id, image_id, tag_id, weight, type, created, game_submission_id, username, user_id', 'safe', 'on'=>'search'),
    );
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
    $criteria->alias = 't';
    $criteria->select = 't.*, u.username, u.id as user_id';
    $criteria->distinct = true;
    $criteria->join .= "  LEFT JOIN {{game_submission}} gs ON gs.id=t.game_submission_id
                          LEFT JOIN {{session}} s ON s.id=gs.session_id
                          LEFT JOIN {{user}} u ON u.id=s.user_id";
    $criteria->compare('t.id', $this->id);
    $criteria->compare('t.image_id', $this->image_id);
    $criteria->compare('t.tag_id', $this->tag_id);
    $criteria->compare('t.weight', $this->weight);
    $criteria->compare('t.type', $this->type, true);
    $criteria->compare('t.created', $this->created, true);
    $criteria->compare('t.game_submission_id', $this->game_submission_id);
    $criteria->compare('t.type', $this->type, true);
    
    if (isset($_GET["TagUse"])) {
      if (isset($_GET["TagUse"]["username"]) && trim($_GET["TagUse"]["username"]) != "") {
        $criteria->addSearchCondition('u.username', $_GET["TagUse"]["username"]);                    
      }
    }
    
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

  public function getUserName($in_search = false) {
    if ($in_search) {
      if ($this->username && $this->user_id) {
        return CHtml::link($this->username, array('user/view', 'id' => $this->user_id));
      } else {
        return Yii::t('app', 'Guest'); 
      }
    } else {
      $username = Yii::app()->db->createCommand()
                      ->select('u.id, u.username')
                      ->from('{{tag_use}} tu')
                      ->join('{{game_submission}} gs', 'gs.id=tu.game_submission_id')
                      ->join('{{session}} s', 's.id=gs.session_id')
                      ->join('{{user}} u', 'u.id=s.user_id')
                      ->where('tu.id=:tuID', array("tuID" => $this->id))
                      ->queryRow();
      if ($username) {
        return CHtml::link($username['username'], array('user/view', 'id' => $username['id']));
      } else {
        return Yii::t('app', 'Guest');
      }
    }
    
  }
}