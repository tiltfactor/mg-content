<?php

Yii::import('application.models._base.BaseImage');

class Image extends BaseImage
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
  
  /**
   * Provides a CActiveDataProvider for the image tool search functionality
   * 
   * @return object CActiveDataProvider the dataprovider
   */
  public function search() {
    $criteria = new CDbCriteria;
    $criteria->alias = 't';
    $criteria->join = "";
    $criteria->distinct = true;
    
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
          
          $tags = null;
          if ($_GET["Custom"]["tags_search_option"] == "OR") {
            $tags = $cmd->selectDistinct('tu.image_id')
                    ->from('{{tag_use}} tu')
                    ->join('{{tag}} tag', 'tu.tag_id = tag.id')
                    ->where(array('and', 'tu.weight > 0',array('in', 'tag.tag', array_values($parsed_tags))))
                    ->queryAll();
          } else {
            $tags = $cmd->selectDistinct('tu.image_id, COUNT(DISTINCT tu.tag_id) as counted')
                    ->from('{{tag_use}} tu')
                    ->where(array('and', 'tu.weight > 0',array('in', 'tag.tag', array_values($parsed_tags))))
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
  
  /**
   * Provides a CActiveDataProvider. Lists all images that are not processed via the import tool
   * 
   * @return object CActiveDataProvider the dataprovider for the import process screen
   */
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
   * As images have got files we have to make sure that all files are removed from the file system once an image
   * has been deleted.
   * 
   * This method is automatically called as Yii behaviour
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
  
  /**
   * Created the CArrayDataProvider for the image listing on a user/player detail view.
   * 
   * @param int $user_id the user_id of the user for which the images should be listed
   * @return object CArrayDataProvider the configured dataprovider that can list all images that are tag by the given user
   */
  public function searchUserImages($user_id) {
    $command = Yii::app()->db->createCommand()
                  ->select('COUNT(i.id) as counted, COUNT(DISTINCT tu.tag_id) as tag_counted, i.id, i.name')
                  ->from('{{session}} s')
                  ->join('{{game_submission}} gs', 'gs.session_id=s.id')
                  ->join('{{tag_use}} tu', 'tu.game_submission_id = gs.id')
                  ->join('{{image}} i', 'i.id = tu.image_id')
                  ->where(array('and', 'tu.weight > 0', 's.user_id=:userID'), array(":userID" => $user_id))
                  ->group('i.id, i.name')
                  ->order('gs.created DESC');
    $command->distinct = true;          
    $tags = $command->queryAll();
    return  new CArrayDataProvider($tags, array(
      'id'=>'id',
      'sort'=>array(
          'attributes'=>array(
               'id', 'name', 'counted'
          ),
      ),
      'pagination'=>array(
          'pageSize'=> Yii::app()->fbvStorage->get("settings.pagination_size")
      ),
    ));
  }
  
  /**
   * Created the CArrayDataProvider for the image listing on a tag detail view.
   * 
   * @param int $tag_id the tag_id of the tag for which the images should be listed
   * @return object CArrayDataProvider the configured dataprovider that can list all images that are tag with the identified tag
   */
  public function searchTagImages($tag_id) {
    $command = Yii::app()->db->createCommand()
                  ->select('COUNT(i.id) as counted, COUNT(DISTINCT s.user_id) as user_counted, i.id, i.name')
                  ->from('{{session}} s')
                  ->join('{{game_submission}} gs', 'gs.session_id=s.id')
                  ->join('{{tag_use}} tu', 'tu.game_submission_id = gs.id')
                  ->join('{{image}} i', 'i.id = tu.image_id')
                  ->where(array('and', 'tu.weight > 0', 'tu.tag_id=:tagID'), array(":tagID" => $tag_id))
                  ->group('i.id, i.name')
                  ->order('gs.created DESC');
    $command->distinct = true;          
    $tags = $command->queryAll();
    return  new CArrayDataProvider($tags, array(
      'id'=>'id',
      'sort'=>array(
          'attributes'=>array(
               'id', 'name', 'counted'
          ),
      ),
      'pagination'=>array(
          'pageSize'=> Yii::app()->fbvStorage->get("settings.pagination_size")
      ),
    ));
  }
  
  /**
   * returns a comma separated list of the tag that are used most for the image. each of the listed tags
   * will be linked to its view page. in addition the use count will be given.  
   * 
   * @param int $num_tags the number of top tags to be listed
   */
  public function getTopTags($num_tags=10) {
    $tags = Yii::app()->db->createCommand()
                  ->select('count(t.id) as counted, t.id, t.tag')
                  ->from('{{tag_use}} tu')
                  ->join('{{tag}} t', 'tu.tag_id = t.id')
                  ->where(array('and', 'tu.weight > 0', 'tu.image_id=:imageID'), array(":imageID" => $this->id))
                  ->group('t.id, t.tag')
                  ->order('counted DESC')
                  ->limit($num_tags)
                  ->queryAll();
        
    if ($tags) {
      $out = array();
      foreach ($tags as $tag) {
        $linkEdit = CHtml::link($tag["tag"] . '(' .$tag["counted"] . ')', array("/admin/tag/view", "id" =>$tag["id"]), array('class' => 'edit ir'));
        $linkView = CHtml::link($tag["tag"] . '(' .$tag["counted"] . ')', array("/admin/tag/view", "id" =>$tag["id"]), array('class' => 'tag'));
        $out[] =  '<div class="tag-dialog">' . $linkEdit . $linkView . '</div>';
      }
      return implode("", $out);
    } else {
      return ""; 
    }
  }
  
  /**
   * lists all image set of the image as comma separated list of html links (linking to the imageSet/view page)
   */
  public function listImageSets() {
    $out = array();
    if (count($this->imageSets) > 0) {
      foreach ($this->imageSets as $imageSet) {
        $out[] = GxHtml::link(GxHtml::encode($imageSet->name), array('imageSet/view', 'id' => $imageSet->id));
      }
    }
    return implode(", ", $out);
  }
  
  /**
   * updates the last_access time of each image identified by the ids in the passed array.
   * 
   * @param array $image_ids array of integer - the ids of the images which last_access should be set to now
   */
  public function setLastAccess($image_ids) {
    if (is_array($image_ids) && count($image_ids)) {
      $sql = "  UPDATE image
                SET last_access=now()
                WHERE id IN (" . implode(",", $image_ids) . ")";
        
      $command=Yii::app()->db->createCommand($sql);        
      $command->execute();
    }
  }
  
}