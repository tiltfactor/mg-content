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
      
      if (isset($_GET["Custom"]["imagesets"]) && is_array($_GET["Custom"]["imagesets"])) {
        var_dump($_GET["Custom"]["imagesets"]);
        $criteria->join .= "  LEFT JOIN {{image}} i ON i.id=tu.image_id
                              LEFT JOIN {{image_set_to_image}} isi ON isi.image_id=i.id";
        $criteria->addInCondition('isi.image_set_id', array_values($_GET["Custom"]["imagesets"]));
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
  
  public function searchImageTags($image_id) {
    $tags = Yii::app()->db->createCommand()
                  ->select('count(t.id) as counted, AVG(tu.weight) as weight, t.id, t.tag')
                  ->from('{{tag_use}} tu')
                  ->join('{{tag}} t', 'tu.tag_id = t.id')
                  ->where(array('and', 'tu.weight > 0', 'tu.image_id=:imageID'), array(":imageID" => $image_id))
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
  
  public function getTopImages($num_images=10) {
    $images = Yii::app()->db->createCommand()
                  ->select('count(i.id) as counted, i.id, i.name')
                  ->from('{{tag_use}} tu')
                  ->join('{{image}} i', 'tu.image_id = i.id')
                  ->where(array('and', 'tu.weight > 0', 'i.locked=1', 'tu.tag_id=:tagID'), array(":tagID" => $this->id))
                  ->group('i.id, i.name')
                  ->order('counted DESC')
                  ->limit($num_images)
                  ->queryAll();
      
    if ($images) {
      $out = "";
      foreach ($images as $image) {
        $html_image = CHtml::image(Yii::app()->getBaseUrl() . Yii::app()->fbvStorage->get('settings.app_upload_url') . '/thumbs/'. $image['name'], $image['name']) . " <span>x " . $image['counted'] . "</span>";
        $out .= CHtml::link( $html_image, array("/admin/image/view", "id" => $image["id"]));  
      }
      return $out;
    } else {
      return ""; 
    }
  }
  
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
  
  public function tagUseInfo() {
    return Yii::app()->db->createCommand()
                  ->select('count(tu.id) as use_count, AVG(tu.weight) as average, MAX(tu.weight) as max_weight, MIN(tu.weight) as min_weight, count(distinct tu.image_id) as image_count')
                  ->from('{{tag_use}} tu')
                  ->where(array('and', 'tu.weight > 0', 'tu.tag_id=:tagID'), array(":tagID" => $this->id))
                  ->queryRow();
  }
  
  public function getTagUseInfo() {
    $tag_info = $this->tagUseInfo();
    if ($tag_info) {
      $params = array(
        '{use_count}' => $tag_info['use_count'],
        '{image_count}' => $tag_info['image_count'],
        '{average}' => $tag_info['average'],
        '{min_weight}' => $tag_info['min_weight'],
        '{max_weight}' => $tag_info['max_weight'],
      );
      return Yii::t('app', '<h5>Used <b>{use_count}</b> time(s) on <b>{image_count}</b> image(s).</h5>Weight: AVG({average}), MIN({min_weight}), MAX({max_weight})', $params);
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