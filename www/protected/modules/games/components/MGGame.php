<?php

class MGGame extends CComponent {
  public $two_player_game = false;
  public $opponents_submission = null;
  
  /**
   * @param array Array of image id's that have been used.
   */
  public function saveSubmission($game, &$game_model) {
    $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
    if (isset($game->request->submissions) && is_array($game->request->submissions)&& count($game->request->submissions) > 0) {
      $game_submission = new GameSubmission;
      $game_submission->submission = json_encode($game->request->submissions);
      $game_submission->turn = $game->turn;
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
      //TODO: here comes the image set access filter magic
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
   * @param object $game_model The game model
   * @param int $num_images how many images have to be found at least, if not enough images are found the system clears the seen images in the session and recalls this method
   * @param boolean $second_attempt used to indicate that the seen images in the session have been cleared an the system tries a second time to load images
   * @return array Array of Array: array(array("id", "name"), ...)
   */
  protected function getImages($imageSets, $game, &$game_model, $num_images=1, $second_attempt=false) {
    
    $used_images = $this->getUsedImages($game, $game_model);
    
    $limit = $num_images * 5;
    $limit = ($limit < 50)? 50 : $limit;
    
    
    if (Yii::app()->user->isGuest) {
      $images = Yii::app()->db->createCommand()
                  ->selectDistinct('i.id, i.name, is.licence_id, (i.last_access <= now()-is.last_access_interval) as last_access_ok')
                  ->from('{{image_set_to_image}} is2i')
                  ->join('{{image}} i', 'i.id=is2i.image_id')
                  ->join('{{image_set}} is', 'is.id=is2i.image_set_id')
                  ->where(array('and', 'i.locked=1', array('in', 'is2i.image_set_id', $imageSets), array('not in', 'i.id', $used_images))) 
                  ->order('i.last_access ASC')
                  ->limit($limit)
                  ->queryAll();  
    } else {
      // if a player is logged in the images should be weight by interest
      $images = Yii::app()->db->createCommand()
                  ->selectDistinct('i.id, i.name, is.licence_id, MAX(usm.interest) as max_interest, (i.last_access <= now()-is.last_access_interval) as last_access_ok')
                  ->from('{{image_set_to_image}} is2i')
                  ->join('{{image}} i', 'i.id=is2i.image_id')
                  ->join('{{image_set}} is', 'is.id=is2i.image_set_id')
                  ->rightJoin('{{image_set_to_subject_matter}} is2sm', 'is2sm.image_set_id=is2i.image_set_id')
                  ->rightJoin('{{user_to_subject_matter}} usm', 'usm.subject_matter_id=is2sm.subject_matter_id')
                  ->where(array('and', 'usm.user_id=:userID', 'i.locked=1', array('in', 'is2i.image_set_id', $imageSets), array('not in', 'i.id', $used_images)), array(':userID' => Yii::app()->user->id))
                  ->group('i.id, i.name, is.licence_id')
                  ->order('max_interest DESC, i.last_access ASC')
                  ->limit($limit)
                  ->queryAll();  
    }
    
    if ($images && count($images) >= $num_images) {
      $arr_image = array();
      $blocked_by_last_access = array();
      
      foreach ($images as $image) {
        if (!array_key_exists($image["id"], $blocked_by_last_access)) {
          if (!array_key_exists($image["id"], $arr_image)) {
            $arr_image[$image["id"]] = array(
              "id" => $image["id"],
              "name" => $image["name"],
              "licences" => array((int)$image["licence_id"])
            );
          } else {
            $arr_image[$image["id"]]["licences"][] = (int)$image["licence_id"];
          }
          
          if (!$image["last_access_ok"]) {
            unset($arr_image[$image["id"]]);
            $blocked_by_last_access[$image["id"]] = true;
          }
        }
      }
      
      if (count($arr_image) >= $num_images) {
        foreach ($arr_image as $key => $image) { // we want to hide the default licence if the image has got another licence
          if (count($arr_image[$key]["licences"]) > 1) {
            $arr_image[$key]["licences"] = array_diff($arr_image[$key]["licences"], array(1));
          }
        }
        return array_values($arr_image);
      } else if ($second_attempt) {
        return null;
      } else {
        $this->resetUsedImages($game, $game_model);
        return $this->getImages($imageSets, $game, $game_model, $num_images, true);
      }
    } else if ($second_attempt) {
      return null;
    } else {
      // no images available could it be that the user has already seen all in this session?
      // reset session images and try again
      $this->resetUsedImages($game, $game_model);
      return $this->getImages($imageSets, $game, $game_model, $num_images, true);
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
          
    // TODO: we could add a data base driven version (a.k.a) retrieve the images the user has tagged and add them to the list
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
    
    Image::model()->setLastAccess($usedImages);
    
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
  
  /** 
   * Loads a turn from the played_game_turn_info table and parses and returns the stored turn info
   * 
   * @param int $played_game_id the played game id
   * @param int $turn the turn number
   * @param boolean $assoc flag for the json_decode assoc array flag (if true elements will be returned as array and not objects)
   * @return mixed array of turn data or null
   */
  protected function loadTwoPlayerTurnFromDb($played_game_id, $turn, $assoc=TRUE) {
    $turn_data = Yii::app()->db->createCommand()
                  ->select('pgti.data')
                  ->from('{{played_game_turn_info}} pgti')
                  ->where('pgti.turn=:turn AND pgti.played_game_id = :pGameID', array(
                      ':turn' => $turn,
                      ':pGameID' => $played_game_id)) 
                  ->queryScalar();
    if ($turn_data) {
      return json_decode($turn_data, $assoc);
    } else {
      return null;
    }
  }
  
  /** 
   * stores a turn into the database. the passed data will be json_encoded
   * 
   * @param int $played_game_id the played game id
   * @param int $turn the turn number
   * @param int $session_id the current user's session id
   * @param mixed $data the data that shall be stored in the database 
   */
  protected function saveTwoPlayerTurnToDb($played_game_id, $turn, $session_id, $data) {
    try {
      $cmd  = Yii::app()->db->createCommand()
                  ->insert('{{played_game_turn_info}}', array(
                    'played_game_id' => $played_game_id, 
                    'turn' => $turn, 
                    'created_by_session_id' => $session_id, 
                    'data' => json_encode($data),
                  ));
    } catch (CDbException $e) {
      return false;
    }
    return true;
  }
  
  /**
   * This method allows for extension of the game api. The API provides an action that bridges all
   * valid calls to the this method.
   * 
   * @param object $game The game object
   * @param object $game_model The game model
   * @param string $method the name of the method to be called
   * @param object $parameter optional parameter for the method call
   */
  public function gameAPI(&$game, &$game_model, $method, $parameter) {
    if (method_exists($this, $method)) {
      return call_user_func_array(array($this, $method), array($game, $game_model, $parameter));
    }
  }
}

/**
 * Interface for Game Logic
 * @abstract
 */
interface MGGameInterface
{
  public function parseSubmission(&$game, &$game_model);
  public function parseTags(&$game, &$game_model);
  public function setWeights(&$game, &$game_model, $tags);
  public function getTurn(&$game, &$game_model, $tags=array());
  public function getScore(&$game, &$game_model, &$tags);
}
