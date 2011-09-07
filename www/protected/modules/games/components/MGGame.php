<?php

class MGGame extends CComponent {
  public $two_player_game = false;
  
  /**
   * @param array Array of image id's that have been used.
   */
  public function saveSubmission($game, &$game_model) {
    $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
    if (isset($game->submissions) && is_array($game->submissions)&& count($game->submissions) > 0) {
      $game_submission = new GameSubmission;
      $game_submission->submission = json_encode($game->submissions);
      $game_submission->session_id = (int)Yii::app()->session[$api_id .'_SESSION_ID'];
      $game_submission->played_game_id = $game->played_game_id;
      $game_submission->created = date('Y-m-d H:i:s'); 
    
      if ($game_submission->validate()) {
        $game_submission->save();
        return $game_submission->id;
      } else {
        throw new CHttpException(500, Yii::t('app', 'Internal Server Error.'));
      }
    }
    return null;
  }
  
  protected function getImageSets($game, &$game_model) {
    $imageSets = array();
    
    foreach ($game_model->imageSets as $imageSet) {
      //xxx here comes the image set access filter magic
      $imageSets[] = $imageSet->id;
    }
    
    return $imageSets;
  }
  
  /**
   * This method retrieves images that are available for the user. It looks in the database 
   * for all images that belong to the games image sets and returns the full list of these images
   * minus the ones the user has already used in this session.
   * 
   * @param array $imageSets The id's of the image sets assigned to the game
   * @param object $game The game object
   * @param object $game_model Tha game model
   * @return array Array of Array: array(array("id", "name"), ...)
   */
  protected function getImages($imageSets, $game, &$game_model, $second_attempt=false) {
    
    $used_images = $this->getUsedImages($game, $game_model);
    
    // for performance reasons we use a direct db query to load the images.
    $images = Yii::app()->db->createCommand()
                ->selectDistinct('i.id, i.name, is.licence_id')
                ->from('{{image_set_to_image}} is2i')
                ->join('{{image}} i', 'i.id=is2i.image_id')
                ->join('{{image_set}} is', 'is.id=is2i.image_set_id')
                ->where(array('and', 'i.locked=1', array('in', 'is2i.image_set_id', $imageSets), array('not in', 'i.id', $used_images))) 
                ->queryAll();
    
    if ($images) {
      $arr_image = array();
      
      foreach ($images as $image) {
        if (!array_key_exists($image["id"], $arr_image)) {
          $arr_image[$image["id"]] = array(
            "id" => $image["id"],
            "name" => $image["name"],
            "licences" => array((int)$image["licence_id"])
          );
        } else {
          $arr_image[$image["id"]]["licences"][] = (int)$image["licence_id"];
        }
      }
      return array_values($arr_image);    
    } else if ($second_attempt) {
      return null;
    } else {
      // no images available could it be that the user has already seen all in this session?
      // reset session images and try again
      $this->resetUsedImages($game, $game_model);
      return $this->getImages($imageSets, $game, $game_model, true);
    }
  } 
 
  /**
   * 
   */
  protected function getUsedImages($game, &$game_model) {
    
    $used_images = array();
    $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
    if (!isset(Yii::app()->session[$api_id .'_GAMES_USED_IMAGES'])) {
      Yii::app()->session[$api_id .'_GAMES_USED_IMAGES'] = array();
    } else {
      $arr_img = Yii::app()->session[$api_id .'_GAMES_USED_IMAGES'];
      if (!array_key_exists($game->gid, $arr_img)) {
        $arr_img[$game->gid] = array();
        Yii::app()->session[$api_id .'_GAMES_USED_IMAGES'] = $arr_img;
      } else {
        $used_images = $arr_img[$game->gid];
      }
    }
          
    // xxx we could add a data base driven version xxx (a.k.a) retrieve the images the user has tagged and add them to the list
    return $used_images;    
  }
  
  /**
   * @param array Array of image id's that have been used.
   */
  protected function setUsedImages($usedImages, $game, &$game_model) {
    $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
    
    $arr_img = array();
    if (!isset(Yii::app()->session[$api_id .'_GAMES_USED_IMAGES'])) {
      Yii::app()->session[$api_id .'_GAMES_USED_IMAGES'] = $arr_img;
    } else {
      $arr_img = Yii::app()->session[$api_id .'_GAMES_USED_IMAGES'];
      if (!array_key_exists($game->gid, $arr_img)) {
        $arr_img[$game->gid] = array();
      }
    }
    $arr_img[$game->gid] = array_unique(array_merge($arr_img[$game->gid], $usedImages));
    
    Yii::app()->session[$api_id .'_GAMES_USED_IMAGES'] = $arr_img;
  }
  
  /**
   * @param array Array of image id's that have been used.
   */
  protected function resetUsedImages($game, &$game_model) {
    $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
    
    $arr_img = array();
    if (!isset(Yii::app()->session[$api_id .'_GAMES_USED_IMAGES'])) {
      Yii::app()->session[$api_id .'_GAMES_USED_IMAGES'] = $arr_img;
    } else {
      $arr_img = Yii::app()->session[$api_id .'_GAMES_USED_IMAGES'];
      if (!array_key_exists($game->gid, $arr_img)) {
        $arr_img[$game->gid] = array();
      }
    }
    $arr_img[$game->gid] = array();
    
    Yii::app()->session[$api_id .'_GAMES_USED_IMAGES'] = $arr_img;
  }
  
  /**
   * returns the full info about licences used on this turn.
   * 
   * @param array
   */
  protected function getLicenceInfo($licenceIDs) {
    $data = array();
    if (is_array($licenceIDs) && count($licenceIDs)) {
      $data = Yii::app()->db->createCommand()
                ->selectDistinct('l.id, l.name, l.description')
                ->from('{{licence}} l')
                ->where(array('in', 'l.id', $licenceIDs)) 
                ->queryAll();
    }
    return $data; 
  }
}

/**
 * Interface for Game Logic
 * @abstract
 */
interface MGGameInterface
{
  public function validateSubmission($game, &$game_model);
  public function getTags($game, &$game_model);
  public function setWeights($game, &$game_model, $tags);
  public function getTurn($game, &$game_model, $tags=array());
  public function getScore($game, &$game_model, &$tags);
}
