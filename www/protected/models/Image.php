<?php

Yii::import('application.models._base.BaseImage');

class Image extends BaseImage
{
  // xxx make use of last_access
  
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
  
  public function search() {
    $criteria = new CDbCriteria;
    $criteria->alias = 't';
    $criteria->join = "";
    $criteria->compare('id', $this->id);
    $criteria->compare('name', $this->name, true);
    $criteria->compare('size', $this->size);
    $criteria->compare('mime_type', $this->mime_type, true);
    $criteria->compare('batch_id', $this->batch_id, true);
    $criteria->compare('last_access', $this->last_access, true);
    $criteria->compare('locked', 1);
    $criteria->compare('created', $this->created, true);
    $criteria->compare('modified', $this->modified, true);
    
    if (isset($_GET["Custom"])) {
      
      if (isset($_GET["Custom"]["tags"])) {
        $parsed_tags = MGTags::parseTags($_GET["Custom"]["tags"]);
        if (count($parsed_tags) > 0) {
          $cmd =  Yii::app()->db->createCommand();
          $cmd->distinct = true; 
          
          $tags = null;
          if ($_GET["Custom"]["tags_search_option"] == "OR") {
            $tags = $cmd->select('tu.image_id')
                    ->from('{{tag_use}} tu')
                    ->join('{{tag}} tag', 'tu.tag_id = tag.id')
                    ->where(array('and', 'tu.weight >= 1',array('in', 'tag.tag', array_values($parsed_tags))))
                    ->queryAll();
          } else {
            $tags = $cmd->select('tu.image_id, COUNT(DISTINCT tu.tag_id) as counted')
                    ->from('{{tag_use}} tu')
                    ->join('{{tag}} tag', 'tu.tag_id = tag.id')
                    ->where(array('and', 'tu.weight >= 1',array('in', 'tag.tag', array_values($parsed_tags))))
                    ->group('tu.image_id')
                    ->having('counted = :counted', array(':counted' => count($parsed_tags)))
                    ->queryAll();
          }
          
          if ($tags) {
            $ids = array();
            foreach ($tags as $tag) {
              $ids[] = $tag["image_id"];
            }
            $criteria->addInCondition('t.id', array_values($ids));
          } else {
            $criteria->addInCondition('t.id', array(0));
          }
        }
      }
    
      if (isset($_GET["Custom"]["imagesets"]) && is_array($_GET["Custom"]["imagesets"])) {
        $criteria->join .= ' LEFT JOIN {{image_set_to_image}} isi ON isi.image_id=t.id';
        $criteria->addInCondition('isi.image_set_id', array_values($_GET["Custom"]["imagesets"]));
      }
      
      if (isset($_GET["Custom"]["username"]) && trim($_GET["Custom"]["username"]) != "") {
        $criteria->distinct = true;
        
        $criteria->join .= "  LEFT JOIN {{tag_use tu}} ON tu.image_id=t.id
                              LEFT JOIN {{game_submission}} gs ON gs.id=tu.game_submission_id
                              LEFT JOIN {{session}} s ON s.id=gs.session_id
                              LEFT JOIN {{user}} u ON u.id=s.user_id";
                              
        $criteria->addSearchCondition('u.username', $_GET["Custom"]["username"]);                    
      }
    }

    if(!Yii::app()->request->isAjaxRequest)
        $criteria->order = 'name ASC';
    
    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination'=>array(
        'pageSize'=> Yii::app()->fbvStorage->get("settings.pagination_size"),
      ),
    ));
  }
  
  public function unprocessed() {
    $criteria = new CDbCriteria;

    $criteria->compare('id', $this->id);
    $criteria->compare('name', $this->name, true);
    $criteria->compare('size', $this->size);
    $criteria->compare('mime_type', $this->mime_type, true);
    $criteria->compare('batch_id', $this->batch_id, true);
    $criteria->compare('last_access', $this->last_access, true);
    $criteria->compare('locked', 0);
    $criteria->compare('created', $this->created, true);
    $criteria->compare('modified', $this->modified, true);
    
    if(!Yii::app()->request->isAjaxRequest)
        $criteria->order = 'name ASC';
        
    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination'=>array(
        'pageSize'=>Yii::app()->fbvStorage->get("settings.pagination_size") * 2,
      ),
    ));
  }
  
  /**
   * as images have got files we have to make sure that all files are removed from the file system once an image
   * has been deleted.
   */
  public function afterDelete() {
    $path = realpath(Yii::app()->getBasePath() . Yii::app()->fbvStorage->get("settings.app_upload_path"));
    $path_parts = pathinfo($this->name);
    
    //remove file from .../uploads/images
    if (file_exists($path . "/images/" . $this->name) && is_writable($path . "/images/" . $this->name)) 
      unlink($path . "/images/" . $this->name);
    
    //remove file from .../uploads/images
    if (file_exists($path . "/thumbs/" . $this->name) && is_writable($path . "/thumbs/" . $this->name)) 
      unlink($path . "/thumbs/" . $this->name);
    
    //remove all scaled versions
    $files = glob($path . "/scaled/" . $path_parts["filename"] . ".mg-scaled.*");
    if (is_array($files) && count($files) > 0) {
      foreach ($files as $file) {
        unlink($file);
      }
    }
    
    parent::afterDelete(); 
  }
  
  public function searchUserImages($user_id) {
    $command = Yii::app()->db->createCommand()
                  ->select('i.id, i.name')
                  ->from('{{session}} s')
                  ->join('{{game_submission}} gs', 'gs.session_id=s.id')
                  ->join('{{tag_use}} tu', 'tu.game_submission_id = gs.id')
                  ->join('{{image}} i', 'i.id = tu.image_id')
                  ->where(array('and', 'tu.weight >= 1', 's.user_id=:userID'), array(":userID" => $user_id))
                  ->order('gs.created DESC');
    $command->distinct = true;          
    $tags = $command->queryAll();
    return  new CArrayDataProvider($tags, array(
      'id'=>'id',
      'sort'=>array(
          'attributes'=>array(
               'id', 'name',
          ),
      ),
      'pagination'=>array(
          'pageSize'=> Yii::app()->fbvStorage->get("settings.pagination_size")
      ),
    ));
  }
  
  public function getTopTags($num_tags=10) {
    $tags = Yii::app()->db->createCommand()
                  ->select('count(t.id) as counted, t.id, t.tag')
                  ->from('{{tag_use}} tu')
                  ->join('{{tag}} t', 'tu.tag_id = t.id')
                  ->where(array('and', 'tu.weight >= 1', 'tu.image_id=:imageID'), array(":imageID" => $this->id))
                  ->group('t.id, t.tag')
                  ->order('counted DESC')
                  ->limit($num_tags)
                  ->queryAll();
        
    if ($tags) {
      $out = array();
      foreach ($tags as $tag) {
        $out[] = CHtml::link($tag["tag"] . '(' .$tag["counted"] . ')', array("/admin/tag/view", "id" =>$tag["id"]));  
      }
      return implode(", ", $out);
    } else {
      return ""; 
    }
  }

}