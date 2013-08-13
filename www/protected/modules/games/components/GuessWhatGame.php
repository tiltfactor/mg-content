<?php
/**
 * Implementation of the needed methods for Guess What?
 *
 * @package    MG
 * @author     Vincent Van Uffelen <novazembla@gmail.com>
 */
class GuessWhatGame extends MGGame implements MGGameInterface {
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
        if (  isset($submission["mode"]) &&
              isset($submission["hints"]) &&
              isset($submission["guesses"]) &&
              isset($submission["media_id"])) {
                
          if (!isset($submission['tags']))
            $submission['tags'] = array();
          
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
      if (is_null($turn)) {
        $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
        
        // thus we have to create one.
        $turn = $this->_createTurn($game, $game_model, $tags);
        
        // it might happen that for both user it might appear to be the first one to read the table
        // thus the next statement check whether the turn has been saved for this played game and turn
        // if it has been a unique exception forces the second user to load the turn from the db
        // can't use table locks as $game_engine->getTurn uses various and potentually unknown tables that would all have to 
        // be included into the lock statement
        if (!$this->saveTwoPlayerTurnToDb($game->played_game_id, $game->turn + 1, (int)Yii::app()->session[$api_id .'_SESSION_ID'], $turn)) {
          // try to load again as the first user seems to have been faster to write the turn into the database
          $turn = $this->loadTwoPlayerTurnFromDb($game->played_game_id, $game->turn + 1); 
          
          // GuessWhat player are alternating in guessing/describing.
          // turns are created with mode set to guess. 
          // the first turn served to the second user (thus after loading it from the DB)
          // has to be initialized as 'describe'
          if ($game->turn + 1 == 1)
            $turn["mode"] = "describe"; 
        }
      } else {
        
        // GuessWhat player are alternating in guessing/describing.
        // turns are created with mode set to guess. 
        // the first turn served to the second user (thus after loading it from the DB)
        // has to be initialized as 'describe'
        if ($game->turn + 1 == 1)
          $turn["mode"] = "describe";
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
    $data["mode"] = "guess"; // initialize a turn as mode guess.
    if (!$game->played_against_computer) {
      $data["mode"] = "guess"; // if played against the computer the player always guesses
    }
    
    // check if the game is not actually over
    if ($game->turn < $game->turns) {
      
      //retrieve the collections that are active for this game
      $collections = $this->getCollections($game, $game_model);
    
      $data["medias"] = array(
        "guess" => array(),
        "describe" => array(),
      );
      
      $used_medias = array();
      
      // get twelve medias that are active for the game
      $medias = $this->getMedias($collections, $game, $game_model, 36);
      
      if ($medias && count($medias) >= 12) {
        $path = Yii::app()->getBaseUrl(true) . Yii::app()->fbvStorage->get('settings.app_upload_url');
        
        $turn_medias = array_rand($medias, 12);
        $describe_media_id = 0; // one media will be identified as the one to be described
        $media_tags = array();
        
        foreach ($turn_medias as $i) {
          $used_medias[] = (int)$medias[$i]["id"];
        }
        
        // if played against the computer we have to make sure that at least 
        // one of the medias has got more than 10 tags to give hints on.
        if ($game->played_against_computer) {  
          $attempts = 10; // we want not to waste too much time checking for tags so do it only ten times.
          $number_of_tag_needed = 1; // how many tags have to be available for an media
          $available_medias = array();
          
          while ($attempts > 0) {
            // get tags for the selection of 12 medias
            $media_tags = MGTags::getTags($used_medias);
            $found_one = false;
            $available_medias = array();
            
            // check if one of the medias has the needed tags for the play against the computer mode
            foreach ($media_tags as $media_id => $tags) {
              if (count($tags) >= $number_of_tag_needed) {
                $available_medias[] = $media_id;
                break;
              } 
            }
            
            if (count($available_medias)) {
              // found one with more than 1 tags
              break;
            } else {
              // haven't found one with >= $number_of_tag_needed tag.
              // select 12 other random medias out of the available one
              $used_medias = array();
              $turn_medias = array_rand($medias, 12);
              foreach ($turn_medias as $i) {
                $used_medias[] = (int)$medias[$i]["id"];
              }
            }
            $attempts--; 
          }  
          
          if ($attempts == 0)
            throw new CHttpException(600, $game->name . Yii::t('app', ': Not enough tagged medias available to play in computer mode'));
          
          // select one of the medias as $describe_media_id
          $describe_media_id = $available_medias[array_rand($available_medias, 1)];
        }   
          
        
        $media_licences = array();
        $arr_licence_ids = array();
        
        // the needed information for the media.
        // make sure the media is present in all versions. rescale media if not
        // by calling MGHelper::createScaledMedia(...)
        foreach ($turn_medias as $i) {
          $data["medias"]["guess"][] = array(
            "media_id" => $medias[$i]["id"],
            "full_size" => $path . "/images/". $medias[$i]["name"],
            "thumbnail" => $path . "/thumbs/". $medias[$i]["name"],
            "guess" => $path . "/scaled/". MGHelper::createScaledMedia($medias[$i]["name"], "", "scaled", $game->image_grid_width, $game->image_grid_height, 80, 10),
            "scaled" => $path . "/scaled/". MGHelper::createScaledMedia($medias[$i]["name"], "", "scaled", $game->image_width, $game->image_height, 80, 10),
            "licences" => $medias[$i]["licences"],
          );
          // add the media's licence info to the licence info array
          $media_licences = array_merge($media_licences, $medias[$i]["licences"]);
          $used_medias[] = (int)$medias[$i]["id"];
        }
        
        shuffle($data["medias"]["guess"]); // mix the medias up
        
        // one of the medias has to be selected as media to be described
        $data["medias"]["describe"] = array();
        if ($game->played_against_computer) {
          // if played against the computer prepare guess medias
          foreach ($data["medias"]["guess"] as $desc_media) {
            if ($desc_media["media_id"] == $describe_media_id) {
              $data["medias"]["describe"] = $desc_media;
              break;
            }
          }
          shuffle($media_tags[$describe_media_id]); // mix them up
          $data["medias"]["describe"]["hints"] = $media_tags[$describe_media_id];
          $describe_media_id = array($describe_media_id); // wrap it in array for words to avoid plugin(s) call
        } else {
          // select out of the twelve one that will be shown to the describing user
          $data["medias"]["describe"] = $data["medias"]["guess"][array_rand($turn_medias, 1)];
          $describe_media_id = array((int)$data["medias"]["describe"]["media_id"]); // wrap it in array for words to avoid plugin(s) call
        }
        
        // extract needed licence info
        $data["licences"] = $this->getLicenceInfo($media_licences);
        
        // save the used media data.
        $this->setUsedMedias($used_medias, $game, $game_model);
        
        // in the first turn this field is empty in further turns it contains the 
        // previous turns weightened tags
        $data["tags"] = array();
        $data["tags"]["user"] = $tags;
        
        // the following lines call the wordsToAvoid methods of the activated dictionary 
        // plugin this generates a words to avoid list
        $data["wordstoavoid"] = array();
        $plugins = PluginsModule::getActiveGamePlugins($game->game_id, "dictionary");
        if (count($plugins) > 0) {
          foreach ($plugins as $plugin) {
            if (method_exists($plugin->component, "wordsToAvoid")) {
              // this method gets all elements by reference. $data["wordstoavoid"] might be changed
              $plugin->component->wordsToAvoid($data["wordstoavoid"], $describe_media_id, $game, $game_model, $tags);
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
      if ($submission['mode'] == 'describe') { // GuessWhat generates tags only on the describing mode
        // extract the media id
        $media_ids[] = $submission["media_id"];
        $media_tags = array();
        if (is_array($submission["tags"])) {
          // attempt to parse the tags. 
          foreach (MGTags::parseTags($submission["tags"]) as $tag) {
            $media_tags[strtolower($tag)] = array(
              'tag' => $tag,
              'weight' => 1,
              'type' => 'new',
              'tag_id' => 0
            );
          }  
        }
        $data[$submission["media_id"]] = $media_tags;
      }
    }
    
    if (count($media_ids)) { // if medias are subitted
      
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
              
              // if the submitted media has been tag with a tag that already exists
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
    }
    return $data;
  }
  
  /**
   * This method is a callback to be called via the game API. It checks a whether a hint is in the stop word
   * list if the StopWord plugin is activated for guess what?
   *  
   * @param object $game The game object
   * @param object $game_model The game model
   * @param object $parameter optional parameter for the method call
   * @return string empty if the first hint is on stopword list
   */
  public function validateHint(&$game, &$game_model, $parameter) {
    $return_valid = "";
    
    // sanitiy check needed variables set?  
    if (isset($parameter->hint) && trim($parameter->hint) != "") {
      
      // make hint to tag (this is also a sanity check)
      $arr_tags = MGTags::parseTags($parameter->hint);
      if (count($arr_tags)) {
        
        // load stopword plugin if available
        $plugin = PluginsModule::getActiveGamePlugin($game->game_id, "dictionary", 'StopWord');
        if ($plugin) {
          
          if (method_exists($plugin->component, "lookup")) {
            
            // check if lookup method exists
            // if yes call it
            $tags = $plugin->component->lookup($game, $game_model, array($arr_tags[0]));
            
            foreach ($tags as $tag => $valid) {
              if ($tag == strtolower($arr_tags[0]) && !$valid) {
                // if hint in validation and not valid 
                $return_valid = $arr_tags[0];
                break;
              }
            }
          }
        } else {
          $return_valid = $arr_tags[0];
        } 
      }
    }
    return $return_valid;
  } 
}
