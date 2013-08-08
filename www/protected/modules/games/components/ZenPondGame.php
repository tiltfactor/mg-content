<?php
/**
 * Implementation of the needed methods for Zen Pond
 *
 * @package    MG
 * @author     Vincent Van Uffelen <novazembla@gmail.com>
 */
class ZenPondGame extends MGGame implements MGGameInterface {
  /**
   * @var boolean $two_player_game is set to TRUE!!! to activate two player mode 
   */  
  public $two_player_game = true;
  
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
  public function parseSubmission(&$game, &$game_model) {
    $game->request->submissions = array();  
    
    $success = true;
    
    // check the POST request if the expected submission field is presend and correctly set
    if (isset($_POST["submissions"]) && is_array($_POST["submissions"]) && count($_POST["submissions"]) > 0) {
      // loop through all submissions and validate them
      foreach ($_POST["submissions"] as $submission) {
        if ($submission["media_id"] && (int)$submission["media_id"] != 0
          && $submission["tags"] && (string)$submission["tags"] != "") {
          // add the submission the the array 
          $game->request->submissions[] = $submission;
        } 
      }
    }
    
    // if a submission has been posted everything might be ok
    $success = (count($game->request->submissions) > 0);
    
    // the following lines call plugins to manipulate & validate the submission further.
    
    // call all dictionary plugins' parseSubmission method 
    $plugins = PluginsModule::getActiveGamePlugins($game->game_id, "dictionary");
    if (count($plugins) > 0) {
      foreach ($plugins as $plugin) {
        if (method_exists($plugin->component, "parseSubmission")) {
          // parse the submission and allow it to influence the success
          $success = $success  && $plugin->component->parseSubmission($game, $game_model);
        }
      }
    }
    
    // call all weighting plugins' parseSubmission method
    $plugins = PluginsModule::getActiveGamePlugins($game->game_id, "weighting");
    if (count($plugins) > 0) {
      foreach ($plugins as $plugin) {
        if (method_exists($plugin->component, "parseSubmission")) {
          // parse the submission and allow it to influence the success
          $success = $success  && $plugin->component->parseSubmission($game, $game_model);
        }
      }
    }
    
    return $success;
  }
  
  /**
   * Creates the needed data for a turn. This data will be passed on to the 
   * players client and there rendered. It will most likely involve the follwoing 
   * tasks. 
   * 
   * + Retrive a new media list for the next turn
   * + Call the wordstoavoid method of the dictionary plugins
   * + retrieve licence info
   * 
   * @param object $game The game object
   * @param object $game_model The game model
   * @param Array the tags submitted by the player for each media
   * @return Array the turn information that will be sent to the players client
   */
  public function getTurn(&$game, &$game_model, $tags=array()) {
    if ($game->played_against_computer) {
      // no need to look for created turn in the database simply render one with the private funtion
      return $this->_createTurn($game, $game_model, $tags);
    } else {
      
      // as a two player game must show the same turn info for both users the first user generating 
      // a turn will have to trigger the turn to be stored in the database. 
      // This call attempts to load the turn from the datbase 
      $turn = $this->loadTwoPlayerTurnFromDb($game->played_game_id, $game->turn + 1);
      
      if (is_null($turn)) { // seems like the turn has not been loaded
        $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
      
        // thus we have to create one.
        $turn = $this->_createTurn($game, $game_model, $tags);
        
        // it might happen that for both user it might appear to be the first one to read the table.
        // thus the next statement check whether the turn has been saved for this played game and turn
        // if it has been a unique exception forces the second user to load the turn from the db
        // can't use table locks as $game_engine->getTurn uses various and potentually unknown tables that would all have to 
        // be included into the lock statement
        if (!$this->saveTwoPlayerTurnToDb($game->played_game_id, $game->turn + 1, (int)Yii::app()->session[$api_id .'_SESSION_ID'], $turn)) {
          // try to load again as the first user seems to have been faster to write the turn into the database
          $turn = $this->loadTwoPlayerTurnFromDb($game->played_game_id, $game->turn + 1); 
        }
      }
    }
    
    if (is_null($turn)) {
      throw new CHttpException(500, Yii::t('app', 'Internal Server Error.'));
    }
    return $turn;
  }  
  
  /**
   * This method is the actual implementation of the getTurn method
   * 
   * @param object $game The game object
   * @param object $game_model The game model
   * @param Array the tags submitted by the player for each media
   * @return Array the turn information that will be sent to the players client
   */
  private function _createTurn(&$game, &$game_model, $tags=array()) {
    $data = array();
    
    // check if the game is not actually over
    if ($game->turn < $game->turns) {
      
      //retrieve the media sets that are active for this game
      $collections = $this->getCollections($game, $game_model);
    
      $data["medias"] = array();
      
      $used_medias = array();
      
      // get a one medias that is active for the game
      $medias = $this->getMedias($collections, $game, $game_model);
      
      if ($medias && count($medias) > 0) {
        $i = array_rand($medias, 1); // select one random item out of the medias
        
        // the needed information for the media.
        // make sure the media is present in all versions. rescale media if not
        // by calling MGHelper::createScaledMedia(...)
        $path = Yii::app()->getBaseUrl(true) . Yii::app()->fbvStorage->get('settings.app_upload_url');
        $data["medias"][] = array(
          "media_id" => $medias[$i]["id"],
          "full_size" => $path . "/images/". $medias[$i]["name"],
          "thumbnail" => $path . "/thumbs/". $medias[$i]["name"],
          "final_screen" => $path . "/scaled/". MGHelper::createScaledMedia($medias[$i]["name"], "", "scaled", 212, 171, 80, 10),
          "scaled" => $path . "/scaled/". MGHelper::createScaledMedia($medias[$i]["name"], "", "scaled", $game->image_width, $game->image_height, 80, 10),
          "licences" => $medias[$i]["licences"],
        );
        
        // add the media to the list of medias that will be saved in the session so the
        // user sees the media only once
        $used_medias[] = (int)$medias[$i]["id"];
        
        // extract needed licence info
        $data["licences"] = $this->getLicenceInfo($medias[$i]["licences"]);
        
        // save the used media data.
        $this->setUsedMedias($used_medias, $game, $game_model);
        
        // prepare further data 
        $data["tags"] = array();
        
        // in the first turn this field is empty in further turns it contains the 
        // previous turns weightened tags
        $data["tags"]["user"] = $tags;
        $data["wordstoavoid"] = array();
        
        // the following lines call the wordsToAvoid methods of the activated dictionary 
        // plugin this generates a words to avoid list
        $plugins = PluginsModule::getActiveGamePlugins($game->game_id, "dictionary");
        if (count($plugins) > 0) {
          foreach ($plugins as $plugin) {
            if (method_exists($plugin->component, "wordsToAvoid")) {
              // this method gets all elements by reference. $data["wordstoavoid"] might be changed
              $plugin->component->wordsToAvoid($data["wordstoavoid"], $used_medias, $game, $game_model, $tags);
            }
          }
        }
        
      } else 
        throw new CHttpException(600, $game->name . Yii::t('app', ': Not enough medias available'));
      
    } else {
      
      // the game is over thus the needed info is sparse
      $data["tags"] = array();
      $data["tags"]["user"] = $tags;
      $data["licences"] = array();
    } 
    return $data;
  }
  
  /**
   * Allows to implement weighting of the submitted tags. Here you should usually 
   * provide hooks to the setWeight methods of the dictionary and weighting plugins.
   * 
   * @param object $game The game object
   * @param object $game_model The game model
   * @param Array the tags submitted by the player for each media
   * @return Array the tags (with additional weight information)
   */
  public function setWeights(&$game, &$game_model, $tags) {
    // call the set setWeights method of all activated dictionary plugins
    $plugins = PluginsModule::getActiveGamePlugins($game->game_id, "dictionary");
    if (count($plugins) > 0) {
      foreach ($plugins as $plugin) {
        if (method_exists($plugin->component, "setWeights")) {
          // influence the weight of the tags
          $tags = $plugin->component->setWeights($game, $game_model, $tags);
        }
      }
    }
    
    // call the set setWeights method of all activated weighting plugins
    $plugins = PluginsModule::getActiveGamePlugins($game->game_id, "weighting");
    if (count($plugins) > 0) {
      foreach ($plugins as $plugin) {
        if (method_exists($plugin->component, "setWeights")) {
          // influence the weight of the tags
          $tags = $plugin->component->setWeights($game, $game_model, $tags);
        }
      }
    }
    return $tags;
  }
  
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
  public function getScore(&$game, &$game_model, &$tags) {
    $score = 0;
    
    // call the set score method of all activated weighting plugins
    $plugins = PluginsModule::getActiveGamePlugins($game->game_id, "weighting");
    if (count($plugins) > 0) {
      foreach ($plugins as $plugin) {
        if (method_exists($plugin->component, "score")) {
          
          // let each scoring plugin add to the score based on the $tags or even
          // further submission information extracted from $game->request->submissions
          $score = $plugin->component->score($game, $game_model, $tags, $score);
        }
      }
    }
    return $score;
  }
  
  /**
   * Take the information from the submission and extract the tags for each media
   * involved in the current turn.
   * 
   * @param object $game The game object
   * @param object $game_model The game model
   * @return Array the tags for each media
   */
  public function parseTags(&$game, &$game_model) {
    $data = array();
    $media_ids = array();
    
    // go through all submissions 
    foreach ($game->request->submissions as $submission) {
      // extract the media id
      $media_ids[] = $submission["media_id"];
      $media_tags = array();
      // attempt to parse the tags. 
      foreach (MGTags::parseTags($submission["tags"]) as $tag) {
        $media_tags[strtolower($tag)] = array(
          'tag' => $tag,
          'weight' => 1,
          'type' => 'new',
          'tag_id' => 0
        );
      }
      
      $data[$submission["media_id"]] = $media_tags; // save tags
    }
    
    if (!$game->played_against_computer && $this->two_player_game && isset($game->opponents_submission) && is_array($game->opponents_submission)) {
      // it is really a two player game and we have to parse the oppenents_submission to make the tags info available for later use
      
      $game->opponents_submission["parsed"] = array();
      
      // go through the opponents submission
      foreach ($game->opponents_submission as $media) {
        if (is_object($media)) {
          // extract media id
          $media_ids[] = $media->media_id;
          
          $media_tags = array();
          // extract tags
          foreach (MGTags::parseTags($media->tags) as $tag) {
            $media_tags[strtolower($tag)] = array(
              'tag' => $tag,
              'weight' => 1,
              'type' => 'new',
              'tag_id' => 0
            );
          }
          $game->opponents_submission["parsed"][$media->media_id] = $media_tags; // save tags
        }
      }
    }
    
    // retrieve all tags for the tagged media
    $media_tags = MGTags::getTags($media_ids);
    
    // loop through all the submitted medias
    foreach ($data as $submitted_media_id => $submitted_media_tags) {
      
      // loop through all the submitted tags
      foreach ($submitted_media_tags as $submitted_tag => $sval) {
        
        // has the submitted media already tags
        if (isset($media_tags[$submitted_media_id])) {
          
          // if the submitted media has tags loop through all of them
          foreach ($media_tags[$submitted_media_id] as $media_tag_id => $ival) {
            
            // if the submitted media has been tag with a tag that already exists:
            if ($submitted_tag == strtolower($ival["tag"])) {
              
              // set the tag type to match
              $data[$submitted_media_id][$submitted_tag]['type'] = 'match';
              $data[$submitted_media_id][$submitted_tag]['tag_id'] = $media_tag_id;
              break;
            }
          }          
        }
      }
    }
    
    // if the game is not played against the computer the second submitting user has to make 
    // sure all data will be checked. As only then all data is available. 
    // Ttherefore we have to check whether the opponent has submitted something 
    // that is parsed and match the opponents submitted  
    if (!$game->played_against_computer && $this->two_player_game && isset($game->opponents_submission) && is_array($game->opponents_submission["parsed"])) {
      
      // loop through the opponets submitted medias
      foreach ($game->opponents_submission["parsed"] as $submitted_media_id => $submitted_media_tags) {
        
        // and media tags
        foreach ($submitted_media_tags as $submitted_tag => $sval) {
          
          // if the submitted media is listed in the media list
          if (isset($media_tags[$submitted_media_id])) {
             
            // loop through its tags and if it matches
            foreach ($media_tags[$submitted_media_id] as $media_tag_id => $ival) {
              if ($submitted_tag == strtolower($ival["tag"])) {
                
                // set it to match 
                $game->opponents_submission["parsed"][$submitted_media_id][$submitted_tag]['type'] = 'match';
                $game->opponents_submission["parsed"][$submitted_media_id][$submitted_tag]['tag_id'] = $media_tag_id;
                break;
              }
            }          
          }
        }
      }
    }
    return $data;
  }
}
