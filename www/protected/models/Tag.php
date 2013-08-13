<?php

Yii::import('application.models._base.BaseTag');

class Tag extends BaseTag
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
  
  public function rules() {
    return array(
      array('tag, created, modified', 'required'),
      array('tag', 'length', 'max'=>64),
      array('id, tag, created, modified', 'safe', 'on'=>'search'),
    );
  }
  
  /**
   * Provides the CActiveDataProvider for the media tools grid view filter function
   * 
   * @return CActiveDataProvider
   */
  public function search() {
    $criteria = new CDbCriteria;
    $criteria->alias = "t";
    $criteria->join = "LEFT JOIN {{tag_use}} tu ON tu.tag_id=t.id"; // all conditions make use of tag_use;
    $criteria->distinct = true;
    
    $criteria->compare('t.id', $this->id);
    $criteria->compare('t.tag', $this->tag, true);
    
    if ($this->created)
      $criteria->compare('t.created', $this->created, true);
    
    if ($this->modified)
      $criteria->compare('t.modified', $this->modified, true);
    
    if (isset($_GET["Custom"])) {
      
      if (isset($_GET["Custom"]["tagweight"]) && trim($_GET["Custom"]["tagweight"]) != "") {
        $criteria->compare('tu.weight', trim($_GET["Custom"]["tagweight"]), true);
      } else {
        $criteria->compare('tu.weight', "> 0", true);
      }
      
      if (isset($_GET["Custom"]["collections"]) && is_array($_GET["Custom"]["collections"])) {
        $criteria->join .= "  LEFT JOIN {{media}} i ON i.id=tu.media_id
                              LEFT JOIN {{collection_to_media}} isi ON isi.media_id=i.id";
        $criteria->addInCondition('isi.collection_id', array_values($_GET["Custom"]["collections"]));
      }
      
      if (isset($_GET["Custom"]["username"]) && trim($_GET["Custom"]["username"]) != "") {
        $criteria->distinct = true;
        
        $criteria->join .= "  LEFT JOIN {{game_submission}} gs ON gs.id=tu.game_submission_id
                              LEFT JOIN {{session}} s ON s.id=gs.session_id
                              LEFT JOIN {{user}} u ON u.id=s.user_id";
        $criteria->addSearchCondition('u.username', $_GET["Custom"]["username"]);
      }
      
    } else {
      $criteria->compare('tu.weight', "> 0", true);
    }
    
    if(!Yii::app()->request->isAjaxRequest)
        $criteria->order = 'tag ASC';

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination'=>array(
        'pageSize'=>Yii::app()->fbvStorage->get("settings.pagination_size"),
      ),
    ));
  }
  
  /**
   * Provides a CArrayDataProvider of all tags and their compound information (use count, 
   * average weight) submitted by the given user. 
   * 
   * @param int $user_id The id of the user for which the tags should be listed
   * @return CArrayDataProvider
   */
  public function searchUserTags($user_id) {
    $tags = Yii::app()->db->createCommand()
                  ->select('count(t.id) as counted, AVG(tu.weight) as weight, t.id, t.tag')
                  ->from('{{session}} s')
                  ->join('{{game_submission}} gs', 'gs.session_id=s.id')
                  ->join('{{tag_use}} tu', 'tu.game_submission_id = gs.id')
                  ->join('{{tag}} t', 'tu.tag_id = t.id')
                  ->where(array('and', 'tu.weight > 0', 's.user_id=:userID'), array(":userID" => $user_id))
                  ->group('t.id, t.tag')
                  ->order('counted DESC')
                  ->queryAll();
    
    return  new CArrayDataProvider($tags, array(
      'id'=>'id',
      'sort'=>array(
          'attributes'=>array(
               'id', 'tag', 'counted', 'weight',
          ),
      ),
      'pagination'=>array(
          'pageSize'=> Yii::app()->fbvStorage->get("settings.pagination_size") * 2,
       ),
    ));
  }
  
  /**
   * Provides a CArrayDataProvider of all tags and their compound information (use count, 
   * average weight) for the given media.
   * 
   * @param int $media_id The id of the media for which the tags should be listed
   * @return CArrayDataProvider
   */
  public function searchMediaTags($media_id) {
    $tags = Yii::app()->db->createCommand()
                  ->select('count(t.id) as counted, AVG(tu.weight) as weight, t.id, t.tag')
                  ->from('{{tag_use}} tu')
                  ->join('{{tag}} t', 'tu.tag_id = t.id')
                  ->where(array('and', 'tu.weight > 0', 'tu.media_id=:mediaID'), array(":mediaID" => $media_id))
                  ->group('t.id, t.tag')
                  ->order('counted DESC')
                  ->queryAll();
    
    return new CArrayDataProvider($tags, array(
      'id'=>'id',
      'sort'=>array(
          'attributes'=>array(
               'id', 'tag', 'counted', 'weight',
          ),
      ),
      'pagination'=>array(
          'pageSize'=> Yii::app()->fbvStorage->get("settings.pagination_size") * 4,
       ),
    ));
  }
  
  /**
   * Get the top n medias used the tag has been used for
   * 
   * @param int $num_medias The amount of medias to be listed
   * @return string Partial HTML. The linked medias or empty string
   */
  public function getTopMedias($num_medias=10) {
    $medias = Yii::app()->db->createCommand()
                  ->select('count(i.id) as counted, i.id, i.name')
                  ->from('{{tag_use}} tu')
                  ->join('{{media}} i', 'tu.media_id = i.id')
                  ->where(array('and', 'tu.weight > 0', 'i.locked=1', 'tu.tag_id=:tagID'), array(":tagID" => $this->id))
                  ->group('i.id, i.name')
                  ->order('counted DESC')
                  ->limit($num_medias)
                  ->queryAll();
      
    if ($medias) {
      $out = "";
      foreach ($medias as $media) {
        $html_media = CHtml::image(Yii::app()->getBaseUrl() . Yii::app()->fbvStorage->get('settings.app_upload_url') . '/thumbs/'. $media['name'], $media['name']) . " <span>x " . $media['counted'] . "</span>";
        $out .= CHtml::link( $html_media, array("/admin/media/view", "id" => $media["id"]));
      }
      return $out;
    } else {
      return ""; 
    }
  }
  
  /**
   * Get the top n users that submitted the tag
   * 
   * @param int $num_users The amount of users to be listed
   * @return string Partial HTML. The linked users or empty string 
   */
  public function getTopUsers($num_users=10) {
    $users = Yii::app()->db->createCommand()
                  ->select('count(u.id) as counted, u.id, u.username')
                  ->from('{{user}} u')
                  ->join('{{session}} s', 's.user_id = u.id')
                  ->join('{{game_submission}} gs', 'gs.session_id=s.id')
                  ->join('{{tag_use}} tu', 'tu.game_submission_id = gs.id')
                  ->where(array('and', 'tu.weight > 0', 'tu.tag_id=:tagID'), array(":tagID" => $this->id))
                  ->group('u.id, u.username')
                  ->order('counted DESC')
                  ->limit($num_users)
                  ->queryAll();
      
    if ($users) {
      $out = array();
      foreach ($users as $user) {
        $out[] = CHtml::link($user['username'] . '(' . $user["counted"]. ')', array("/admin/user/view", "id" => $user["id"]));  
      }
      return implode(", ", $out);
    } else {
      return ""; 
    }
  }
  
  /**
   * Returns a row of tag use info for the active tag
   * 
   * @param array The result or null
   */ 
  public function tagUseInfo() {
    return Yii::app()->db->createCommand()
                  ->select('count(tu.id) as use_count, AVG(tu.weight) as average, MAX(tu.weight) as max_weight, MIN(tu.weight) as min_weight, count(distinct tu.media_id) as media_count')
                  ->from('{{tag_use}} tu')
                  ->where(array('and', 'tu.weight > 0', 'tu.tag_id=:tagID'), array(":tagID" => $this->id))
                  ->queryRow();
  }
  
  /**
   * Creates a partial HTML with the compound tag use info for the active tag.
   * 
   * @return string List of info or empty string
   */
  public function getTagUseInfo() {
    $tag_info = $this->tagUseInfo();
    if ($tag_info) {
      $params = array(
        '{use_count}' => $tag_info['use_count'],
        '{media_count}' => $tag_info['media_count'],
        '{average}' => $tag_info['average'],
        '{min_weight}' => $tag_info['min_weight'],
        '{max_weight}' => $tag_info['max_weight'],
      );
      return Yii::t('app', '<h5>Used <b>{use_count}</b> time(s) on <b>{media_count}</b> media(s).</h5>Weight: AVG({average}), MIN({min_weight}), MAX({max_weight})', $params);
    } else {
      return "";
    }
  }
  
  /**
   * lists tags that contain the passed parameter. It is mainly used for autocomplete
   * widgets
   * 
   * @param string $tag the begin of the tag that should be found
   * @return mixed array containing the tag column or null
   */
  function searchForTags($tag) {
    return Yii::app()->db->createCommand()
                  ->selectDistinct('t.tag')
                  ->from('{{tag}} t')
                  ->join('{{tag_use}} tu', 'tu.tag_id = t.id')
                  ->where(array('and', array('like', 't.tag', '%' . $tag . '%'), 'tu.weight > 0'))
                  ->order('t.tag')
                  ->limit(50)
                  ->queryColumn();
  }
}