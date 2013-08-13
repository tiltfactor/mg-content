<?php

Yii::import('application.models._base.BaseTagUse');

class TagUse extends BaseTagUse
{
  public $username;
  public $user_id;
  public $ip_address;

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
  
  public function rules() {
    return array(
      array('media_id, tag_id, created, game_submission_id', 'required'),
      array('media_id, tag_id', 'numerical', 'integerOnly'=>true),
      array('weight', 'numerical', 'min'=>0, 'max'=>10000),
      array('type', 'length', 'max'=>64),
      array('game_submission_id', 'length', 'max'=>10),
      array('weight, type', 'default', 'setOnEmpty' => true, 'value' => null),
      array('id, media_id, tag_id, weight, type, created, game_submission_id, username, user_id, ip_address', 'safe', 'on'=>'search'),
    );
  }
  
  public function attributeLabels() {
    return array(
      'id' => Yii::t('app', 'ID'),
      'media_id' => null,
      'tag_id' => null,
      'weight' => Yii::t('app', 'Weight'),
      'type' => Yii::t('app', 'Type'),
      'created' => Yii::t('app', 'Created'),
      'game_submission_id' => null,
      'tagOriginalVersions' => null,
      'gameSubmission' => null,
      'media' => null,
      'tag' => null,
      'ip_address' => Yii::t('app', 'Submitters IP address')
    );
  }
  
  /**
   * Set all instances of tag weight's of the to be banned user to 0
   * 
   * @param int $user_id The banned user's id 
   */
  public function banUser($user_id) {
    $sql = "  UPDATE tag_use tu
              LEFT JOIN game_submission gs ON gs.id=tu.game_submission_id
              LEFT JOIN session s ON s.id=gs.session_id
              SET weight=0, type = CONCAT(type, '|user-banned')
              WHERE s.user_id=:userID";
              
    $command=Yii::app()->db->createCommand($sql);        
    $command->bindValue(':userID', $user_id);
    $command->execute();
  }
  
  /**
   * Implementation of the ban tag functionality. It includes the following steps 
   * <ul>
   *  <li>Increase edited count by one</li>
   *  <li>Set tag weights of the banned tags tag uses to 0</li>
   *  <li>Add |tag-banned to the tag uses type column</li>
   * </ul>
   * 
   * @param int $tag_id The id of the tag to be banned
   */
  public function banTag($tag_id) {
    $users = Yii::app()->db->createCommand()
                  ->select('u.id')
                  ->from('{{user}} u')
                  ->join('{{session}} s', 's.user_id = u.id')
                  ->join('{{game_submission}} gs', 'gs.session_id=s.id')
                  ->join('{{tag_use}} tu', 'tu.game_submission_id = gs.id')
                  ->where('tu.tag_id=:tagID', array(":tagID" => $tag_id))
                  ->queryColumn();
    
    if ($users && count($users) > 0) {
      $sql = "  UPDATE user
                SET edited_count=edited_count+1
                WHERE id IN (" . implode(",", $users) . ")";
      
      $command=Yii::app()->db->createCommand($sql);        
      $command->execute();
    }
    
    $sql = "  UPDATE tag_use tu
              SET weight=0, type = CONCAT(type, '|tag-banned')
              WHERE tu.tag_id=:tagID";
    
    $command=Yii::app()->db->createCommand($sql);        
    $command->bindValue(':tagID', $tag_id);
    $command->execute();
  }
  
  /**
   * Create the data provider for the tag use data grid
   * 
   * @return CActiveDataProvider 
   */
  public function search() {
    $criteria = new CDbCriteria;
    $criteria->alias = 't';
    $criteria->select = 't.*, u.username, u.id as user_id, s.ip_address';
    $criteria->distinct = true;
    $criteria->join .= "  LEFT JOIN {{game_submission}} gs ON gs.id=t.game_submission_id
                          LEFT JOIN {{session}} s ON s.id=gs.session_id
                          LEFT JOIN {{user}} u ON u.id=s.user_id";
    $criteria->compare('t.id', $this->id);
    $criteria->compare('t.media_id', $this->media_id);
    $criteria->compare('t.tag_id', $this->tag_id);
    $criteria->compare('t.weight', $this->weight);
    $criteria->compare('t.type', $this->type, true);
    $criteria->compare('t.created', $this->created, true);
    $criteria->compare('t.game_submission_id', $this->game_submission_id);
    $criteria->compare('t.type', $this->type, true);
    $criteria->compare('s.ip_address', ip2long($this->ip_address));
    
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
  
  /**
   * List all used tag use types
   * 
   * @return array List of types
   */
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
          if ($tu_type['type'] != "") {
            if (strpos($tu_type['type'], "|") !== false) {
              $a = explode("|", $tu_type['type']);
              foreach ($a as $t) {
                if (trim($t) != "")
                  $types[$t] = $t; 
              }
            } else {
              $types[$tu_type['type']] = $tu_type['type'];  
            }
          }
        }
      }
    }
    ksort($types);
    return $types;
  }
  
  /**
   * Returns a linked user name of the tag use creator or guest.
   * 
   * @return string The username
   */
  public function getUserName($in_search = false) {
    if ($in_search) {
      if ($this->username && $this->user_id) {
        return CHtml::link($this->username, array('user/view', 'id' => $this->user_id));
      } else {
        return Yii::t('app', 'Guest'); 
      }
    } else {
      $username = $this->getSubmittingUser();
      if ($username) {
        return CHtml::link($username['username'], array('user/view', 'id' => $username['id']));
      } else {
        return Yii::t('app', 'Guest');
      }
    }
  }
  
  /**
   * Returns the user id and username of a tag use if the particular tag use has been submitted 
   * by a registered user
   *   
   * @return mixed array('username' => ..., 'id' => ...)
   */
  public function getSubmittingUser() {
     return Yii::app()->db->createCommand()
                      ->select('u.id, u.username')
                      ->from('{{tag_use}} tu')
                      ->join('{{game_submission}} gs', 'gs.id=tu.game_submission_id')
                      ->join('{{session}} s', 's.id=gs.session_id')
                      ->join('{{user}} u', 'u.id=s.user_id')
                      ->where('tu.id=:tuID', array("tuID" => $this->id))
                      ->queryRow();
  }
  
  /**
   * Returns the ip address of the submitting session.
   * 
   * @return string the ip address
   */
  public function getIpAddress() {
     $ip_address = Yii::app()->db->createCommand()
                      ->select('s.ip_address')
                      ->from('{{tag_use}} tu')
                      ->join('{{game_submission}} gs', 'gs.id=tu.game_submission_id')
                      ->join('{{session}} s', 's.id=gs.session_id')
                      ->where('tu.id=:tuID', array("tuID" => $this->id))
                      ->queryScalar();
    if ($ip_address) {
      return long2ip($ip_address);
    } else {
      return "-";
    }
  }
  
  /**
   * Returns the id and user names of all registered users that submitted the given tag
   * 
   * @param int $tag_id the id of the tag of which user shall be retrieved  
   * @return array array('username' => ..., 'id' => ...)
   */
  public function getSubmittingUsers($tag_id) {
    return Yii::app()->db->createCommand()->selectDistinct('u.id, u.username')
                ->from('{{tag_use}} tu')
                ->join('{{game_submission}} gs', 'gs.id=tu.game_submission_id')
                ->join('{{session}} s', 's.id=gs.session_id')
                ->join('{{user}} u', 'u.id=s.user_id')
                ->where('tu.tag_id=:tagID', array(":tagID" => $tag_id))
                ->queryAll();
  }
  
  /**
   * Update the weight of one tag use
   * 
   * @param float $weight The new tag weight
   * @param int $tag_id The id of the tag to be updated
   */
  public function updateWeightWithTag($weight, $tag_id) {
    return Yii::app()->db->createCommand()
            ->update('{{tag_use}}', array('weight' => $weight), 'tag_id = :tagID', array(':tagID' => $tag_id), 'weight > 0');      
  }
  
  /**
   * Mass updates the tag use weight of all tag weights matching the given 
   * parameter. Adds |reweight to the tag uses type column
   * 
   * @param float $weight The new weight
   * @param int $tag_id The id of the tag which tag uses should be updated
   * @param int $user_id The id of the user which tag uses should be updated
   */
  public function updateWeightWithTagAndUser($weight, $tag_id, $user_id) {
    $sql = "  UPDATE tag_use tu
              LEFT JOIN game_submission gs ON gs.id=tu.game_submission_id
              LEFT JOIN session s ON s.id=gs.session_id
              SET weight=:weight, type = CONCAT(type, '|reweight')
              WHERE weight > 0 AND s.user_id=:userID AND tu.tag_id=:tagID";
              
    $command=Yii::app()->db->createCommand($sql);        
    $command->bindValue(':userID', $user_id);
    $command->bindValue(':tagID', $tag_id);
    $command->bindValue(':weight', $weight);
    $command->execute();
  }
  
  /**
   * Mass updates all tag uses of a tag submitted by guests. 
   * Adds |reweight to the tag uses type column
   * 
   * @param float $weight The new weight
   * @param int $tag_id The id of the tag which tag uses should be updated
   */
  public function updateWeightWithTagForGuests($weight, $tag_id) {
    $sql = "  UPDATE tag_use tu
              LEFT JOIN game_submission gs ON gs.id=tu.game_submission_id
              LEFT JOIN session s ON s.id=gs.session_id
              SET weight=:weight, type = CONCAT(type, '|reweight')
              WHERE weight > 0 AND s.user_id IS NULL AND tu.tag_id=:tagID";
              
    $command=Yii::app()->db->createCommand($sql);        
    $command->bindValue(':tagID', $tag_id);
    $command->bindValue(':weight', $weight);
    $command->execute();
  }
  
  /**
   * Provides additional links for the tag use admin tools grid view
   * 
   * @return string Partial html (links to the tag use tools)
   */
  public function getTagToolLink() {
    $linkEdit = CHtml::link($this->tag, array("/admin/tag/view", "id" => $this->tag_id), array('class' => 'edit ir'));
    $linkView = CHtml::link($this->tag, array("/admin/tag/view", "id" => $this->tag_id), array('class' => 'tag'));
    return '<div class="tag-dialog">' . $linkEdit . $linkView . '</div>';
  }
}