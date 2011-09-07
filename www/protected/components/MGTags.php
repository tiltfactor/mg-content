<?php
/**
 * This class is a collection of helper methods for various tasks
 * 
 * Some of the code has been taken from http://code.google.com/p/yiiext/ many thanks to Alexander Makarov 
 * 
 */

class MGTags {
  
  /**
   * @param array $image_ids array of the image(s) which tags shall be retrieved
   * 
   */ 
  public static function getTags($image_ids, $user_id=null) {
    $tags = array();
    $used_tags = array();
      
    $builder = Yii::app()->db->getCommandBuilder();
    
    if ($user_id) {
      
      $used_tags = Yii::app()->db->createCommand()
                    ->select('tu.image_id, t.id as tag_id, t.tag')
                    ->from('{{tag_use}} tu')
                    ->leftJoin('{{tag}} t', 't.id = tu.tag_id')
                    ->leftJoin('{{game_submission}} gs', 'gs.id = tu.game_submission_id')
                    ->leftJoin('{{session}} s', 's.id = gs.session_id')
                    ->where(array('and', 's.user_id=:userID', array('in', 'tu.image_id', array_values($image_ids))), array(':userID' => $user_id)) 
                    ->queryAll();
                    
    } else {
      
      $used_tags = Yii::app()->db->createCommand()
                    ->select('tu.image_id, t.id as tag_id, t.tag')
                    ->from('{{tag_use}} tu')
                    ->leftJoin('{{tag}} t', 't.id = tu.tag_id')
                    ->where(array('in', 'tu.image_id', array_values($image_ids))) 
                    ->queryAll();
                    
    }
    foreach($used_tags as $tag) {
      if (!isset($tags[$tag["image_id"]]))
        $tags[$tag["image_id"]] = array();
        
      $tags[$tag["image_id"]][$tag["tag_id"]] = array("tag" => $tag["tag"]); 
    }
    
    return $tags;
  }
  
  /**
   * @param mixed $image_ids array or int of the image(s) which tags shall be retrieved
   * 
   */ 
  public static function getUsersTags($image_ids, $user_id) {
    return self::getTags($image_ids, $user_id);
  }
  
  /** 
   * 
   * $tags = array(
   *   "image_id" = array(
   *     "tag" => array(
   *        "weight" => 3,
   *        "original_tag" => "" //optional if set an original version will be created/updated 
   *        "original_comment" => "" //optional if set an original version entry will be created/updated
   *        "original_by_user_id" => null | user_id //optional if set an original version entry will be created/updated
   *        "tag_id" => -1|tag_id // the tag id mark it as -1 to make sure that the tag is registered as a new tag.
   *     )
   *     ...
   *   )
   *   ...
   * )
   * 
   * @param array $tags All needed information to save tag uses in the system
   * @param int $game_submission_id The ID of the game submission_id in the database
   * @param array $tags Tags to save (these are the final tags that will be saved in the database. they should already be cleaned up and processed)
   */
  public static function saveTags($tags, $game_submission_id) {
    foreach ($tags as $image_id => $image_tags) {
      $arr_tags = array();
      
      $all_tags_with_id = true;
      foreach ($image_tags as $tag => $tag_info) {
        
        if (!is_array($tags[$image_id][$tag])) 
          throw new CHttpException(500, Yii::t('app', "The array passed must have arrays as it's leafs."));
        
        if (!array_key_exists("tag_id", $tags[$image_id][$tag]) || (int)$tags[$image_id][$tag]["tag_id"] == 0) {
          $all_tags_with_id = false;  
          $arr_tags[] = $tag;
        }
      }  
      
      if (count($arr_tags) > 0 || $all_tags_with_id) {
        
        if (!$all_tags_with_id) {
          
          $known_tags = Yii::app()->db->createCommand()
                          ->select('t.id, t.tag')
                          ->from('{{tag}} t')
                          ->where(array('in', 't.tag', array_values($arr_tags))) 
                          ->queryAll();
          
          if ($known_tags) {
            foreach ($known_tags as $known_tag) {
              $tags[$image_id][strtolower($known_tag["tag"])]["tag_id"] = $known_tag["id"];
            }
          }  
        }
        
        foreach ($tags[$image_id] as $tag => $tag_info) {
          if (!array_key_exists("tag_id", $tags[$image_id][$tag]) || (int)$tags[$image_id][$tag]["tag_id"] == 0) { // tag does not exist we have to create it
            $tag_model = new Tag;
            $tag_model->tag = $tags[$image_id][$tag]["tag"];
            $tag_model->created = date('Y-m-d H:i:s');
            $tag_model->modified = date('Y-m-d H:i:s');
            
            if ($tag_model->validate()) {
              $tag_model->save();  
            } else {
              throw new CHttpException(500, Yii::t('app', 'Internal Server Error.'));
            }
            $tags[$image_id][$tag]["tag_id"] = $tag_model->id;
          }
          
          // now we know all tags are registered and now all the id's let's add the tag_uses
          $tag_use_model = new TagUse;
          $tag_use_model->tag_id = (int)$tags[$image_id][$tag]["tag_id"];
          $tag_use_model->image_id = (int)$image_id;
          $tag_use_model->weight = (int)$tags[$image_id][$tag]["weight"];
          $tag_use_model->game_submission_id = (int)$game_submission_id;
          $tag_use_model->created = date('Y-m-d H:i:s');
          
          if ($tag_use_model->validate()) {
            $tag_use_model->save();  
          } else {
            throw new CHttpException(500, Yii::t('app', 'Internal Server Error.'));
          }
        } 
      }
    }   
    return true;
  }
  
  /**
   * Get tags array from comma separated tags string.
   * @access private
   * @param string|array $tags
   * @return array
   */
  public static function parseTags($tags) {
    if(!is_array($tags)){
      $tags = explode(',', trim(strip_tags($tags), ' ,'));
    }

    array_walk($tags, array("MGTags", "trim"));
    
    foreach ($tags as $key=>$value) {
      if ($value == "")
        unset($tags[$key]);
    }
    return array_unique(array_values($tags));
  }
  /**
   * Used as a callback to trim tags.
   * @access private
   * @param string $item
   * @param string $key
   * @return string
   */
  
  
  
  public static function trim(&$item, $key) {
    $item = preg_replace("/\s/", " ", $item);
    while (strpos($item, "  ") !== FALSE) {
      $item = str_replace("  ", " ", $item);
    }
    $item = preg_replace("/[^\pL\pN\p{Zs}'-]/u", "", $item);
    $item = substr(trim($item), 0, 64); //we enforce the tags to have a maximum length of 64 characters after we've trimmed white spaces
  }
}
