<?php

class MGGame extends CComponent {
  /**
   * @var boolean $two_player_game Set TRUE if the game is a two player game
   */
  public $two_player_game = false;
  
  /**
   * @var Array in two player games this value will be set to the opponents two player 
   */
  public $opponents_submission = null;
  
  /**
   * Saves the user's submission to the database
   * 
   * @param Object $game the current game object
   * @param Game $game_model The current game's model
   * @return null
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
  
  /**
   * Retrieves the active media sets for the current game.
   * In future this method could be expanded to disable one or more of the 
   * active media set by dynamic criteria (e.g. the users ip address range)
   * 
   * @param Object $game the current game object
   * @param Game $game_model The current game's model
   * @return Array the ids of the active media sets
   */
  protected function getCollections($game, &$game_model) {
    $collections = array();
    
    foreach ($game_model->collections as $collection) {
      //TODO: here could be the media set access filter magic
      $collections[] = $collection->id;
    }
    
    return $collections;
  }
  
  /**
   * This method retrieves medias that are available for the user. It looks in the database
   * for all medias that belong to the games media sets and returns the full list of these medias
   * minus the ones the user has already used in this session.
   * 
   * @param array $collections The id's of the media sets assigned to the game
   * @param object $game The game object
   * @param object $game_model The game model
   * @param int $num_medias how many medias have to be found at least, if not enough medias are found the system clears the seen medias in the session and recalls this method
   * @param boolean $second_attempt used to indicate that the seen medias in the session have been cleared an the system tries a second time to load medias
   * @return array Array of Array: array(array("id", "name"), ...)
   */
  protected function getMedias($collections, $game, &$game_model, $num_medias=1, $second_attempt=false) {
    
    $used_medias = $this->getUsedMedias($game, $game_model);
    
    $limit = $num_medias * 5;
    $limit = ($limit < 50)? 50 : $limit;
    
    
    if (Yii::app()->user->isGuest) {
      $medias = Yii::app()->db->createCommand()
                  ->selectDistinct('i.id, i.name, is.licence_id, (i.last_access IS NULL OR i.last_access <= now()-is.last_access_interval) as last_access_ok')
                  ->from('{{collection_to_media}} is2i')
                  ->join('{{media}} i', 'i.id=is2i.media_id')
                  ->join('{{collection}} is', 'is.id=is2i.collection_id')
                  ->where(array('and', 'i.locked=1', array('in', 'is2i.collection_id', $collections), array('not in', 'i.id', $used_medias)))
                  ->order('i.last_access ASC')
                  ->limit($limit)
                  ->queryAll();  
    } else {
      // if a player is logged in the medias should be weight by interest
      $medias = Yii::app()->db->createCommand()
                  ->selectDistinct('i.id, i.name, is.licence_id, MAX(usm.interest) as max_interest, (i.last_access IS NULL OR i.last_access <= now()-is.last_access_interval) as last_access_ok')
                  ->from('{{collection_to_media}} is2i')
                  ->join('{{media}} i', 'i.id=is2i.media_id')
                  ->join('{{collection}} is', 'is.id=is2i.collection_id')
                  ->leftJoin('{{collection_to_subject_matter}} is2sm', 'is2sm.collection_id=is2i.collection_id')
                  ->leftJoin('{{user_to_subject_matter}} usm', 'usm.subject_matter_id=is2sm.subject_matter_id')
                  ->where(array('and', '(usm.user_id IS NULL OR usm.user_id=:userID)', 'i.locked=1', array('in', 'is2i.collection_id', $collections), array('not in', 'i.id', $used_medias)), array(':userID' => Yii::app()->user->id))
                  ->group('i.id, i.name, is.licence_id')
                  ->order('max_interest DESC, i.last_access ASC')
                  ->limit($limit)
                  ->queryAll();  
    }
    
    if ($medias && count($medias) >= $num_medias) {
      $arr_media = array();
      $blocked_by_last_access = array();

      foreach ($medias as $media) {
        if (!array_key_exists($media["id"], $blocked_by_last_access)) {
          if (!array_key_exists($media["id"], $arr_media)) {
            $arr_media[$media["id"]] = array(
              "id" => $media["id"],
              "name" => $media["name"],
              "licences" => array((int)$media["licence_id"])
            );
          } else {
            $arr_media[$media["id"]]["licences"][] = (int)$media["licence_id"];
          }

          if (!$media["last_access_ok"]) {
            unset($arr_media[$media["id"]]);
            $blocked_by_last_access[$media["id"]] = true;
          }
        }
      }
      
      if (count($arr_media) >= $num_medias) {
        foreach ($arr_media as $key => $media) { // we want to hide the default licence if the media has got another licence
          if (count($arr_media[$key]["licences"]) > 1) {
            $arr_media[$key]["licences"] = array_diff($arr_media[$key]["licences"], array(1));
          }
        }
        return array_values($arr_media);
      } else if ($second_attempt) {
        return null;
      } else {
        $this->resetUsedMedias($game, $game_model);
        return $this->getMedias($collections, $game, $game_model, $num_medias, true);
      }
    }
    else if ($second_attempt) {
      return null;
    } else {
      // no medias available could it be that the user has already seen all in this session?
      // reset session medias and try again
      $this->resetUsedMedias($game, $game_model);
      return $this->getMedias($collections, $game, $game_model, $num_medias, true);
    }
  } 
 
  /**
   * Retrieve the IDs of all medias that have been seen/used by the current user
   * on a per game and per session basis.
   * 
   * @param Object $game the current game object
   * @param Game $game_model The current game's model
   * @return Array the ids of the medias that have been already seen by the current user in this session
   */
  protected function getUsedMedias($game, &$game_model) {
    
    $used_medias = array();
    $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
    if (!isset(Yii::app()->session[$api_id .'_GAMES_USED_IMAGES'])) {
      Yii::app()->session[$api_id .'_GAMES_USED_IMAGES'] = array();
    } else {
      $arr_img = Yii::app()->session[$api_id .'_GAMES_USED_IMAGES'];
      if (!array_key_exists($game->gid, $arr_img)) {
        $arr_img[$game->gid] = array();
        Yii::app()->session[$api_id .'_GAMES_USED_IMAGES'] = $arr_img;
      } else {
        $used_medias = $arr_img[$game->gid];
      }
    }
          
    // TODO: we could add a data base driven version (a.k.a) retrieve the medias the user has tagged and add them to the list
    return $used_medias;
  }
  
  /**
   * Add medias to the used medias list stored in the current session for the currently
   * played game
   * 
   * @param Array $usedMedias the medias that have been shown to the user
   * @param Object $game the current game object
   * @param Game $game_model The current game's model
   */
  protected function setUsedMedias($usedMedias, $game, &$game_model) {
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
    $arr_img[$game->gid] = array_unique(array_merge($arr_img[$game->gid], $usedMedias));
    
    Media::model()->setLastAccess($usedMedias);
    
    Yii::app()->session[$api_id .'_GAMES_USED_IMAGES'] = $arr_img;
  }
  
  /**
   * Clears the used medias in the session for the current game.
   * 
   * @param Object $game the current game object
   * @param Game $game_model The current game's model
   */
  protected function resetUsedMedias($game, &$game_model) {
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
   * Returns the full distinct info about licences used on this turn.
   * 
   * @param Array the licence IDs of the medias of this turn
   * @return Array the aggregated licence info. Empty array if no info could be found
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
   * In two player games both users have to see the same turn (info). As SESSION data cannot be
   * shared between users this can in a stateless system only be ensured by storing the turn in 
   * the database. This method loads a turn from the played_game_turn_info table and parses and 
   * returns the stored turn info
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
 * Interface for Game Logic.
 * Each game has to implement these methods as these will be called in the 
 * game controller. 
 * 
 * @abstract
 */
interface MGGameInterface
{
  /**
   * As the JSON submitted/posted by the JavaScript implementation of the game 
   * can vary each game has to implement a parsing function to make it available
   * for the further methods. This is also the right place to sanity check the 
   * submission received by the server
   * 
   * @param object $game The game object
   * @param object $game_model The game model
   * @return boolean TRUE if the submission has been successfully parsed
   */
  public function parseSubmission(&$game, &$game_model);
  
  /**
   * Take the information from the submission and extract the tags for each media
   * involved in the current turn.
   * 
   * @param object $game The game object
   * @param object $game_model The game model
   * @return Array the tags for each media
   */
  public function parseTags(&$game, &$game_model);
  
  /**
   * Allows to implement weighting of the submitted tags. Here you should usually 
   * provide hooks to the setWeight methods of the dictionary and weighting plugins.
   * 
   * @param object $game The game object
   * @param object $game_model The game model
   * @param Array the tags submitted by the player for each media
   * @return Array the tags (with additional weight information)
   */
  public function setWeights(&$game, &$game_model, $tags);
  
  /**
   * Creates the needed data for a turn. This data will be passed on to the 
   * players client and there rendered. It will most likely involve the follwoing 
   * tasks. 
   * 
   * + Retrive a new media list for the next turn
   * + Call the wordstoavoid method of the dictionary plugins
   * 
   * If two player game 
   * + check/store a created turn in the database. so both users play with the same
   * turn. See ZenPondGame->getTurn for a exemplary implementation.
   * 
   * @param object $game The game object
   * @param object $game_model The game model
   * @param Array the tags submitted by the player for each media
   * @return Array the turn information that will be sent to the players client
   */
  public function getTurn(&$game, &$game_model, $tags=array());
  
  /**
   * This method should hold the implementation that allows the scoring 
   * of the turn's submitted tags. It is the place to call the weighting 
   * plugin's 'scoring' methods. 
   * 
   * @param object $game The game object
   * @param object $game_model The game model
   * @param Array the tags submitted by the player for each media
   * @return int the score for this turn
   */
  public function getScore(&$game, &$game_model, &$tags);
}
