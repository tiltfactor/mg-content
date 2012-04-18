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
              isset($submission["image_id"])) {
                
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
   * + Retrive a new image list for the next turn
   * + Call the wordstoavoid method of the dictionary plugins
   * + retrieve licence info
   * 
   * @param object $game The game object
   * @param object $game_model The game model
   * @param Array the tags submitted by the player for each image
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
   * @param Array the tags submitted by the player for each image
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
      
      //retrieve the image sets that are active for this game
      $imageSets = $this->getImageSets($game, $game_model);
    
      $data["images"] = array(
        "guess" => array(),
        "describe" => array(),
      );
      
      $used_images = array();
      
      // get twelve images that are active for the game
      $images = $this->getImages($imageSets, $game, $game_model, 36);
      
      if ($images && count($images) >= 12) {
        $path = Yii::app()->getBaseUrl(true) . Yii::app()->fbvStorage->get('settings.app_upload_url');
        
        $turn_images = array_rand($images, 12);
        $describe_image_id = 0; // one image will be identified as the one to be described
        $image_tags = array();
        
        foreach ($turn_images as $i) {
          $used_images[] = (int)$images[$i]["id"];
        }
        
        // if played against the computer we have to make sure that at least 
        // one of the images has got more than 10 tags to give hints on.
        if ($game->played_against_computer) {  
          $attempts = 10; // we want not to waste too much time checking for tags so do it only ten times.
          $number_of_tag_needed = 1; // how many tags have to be available for an image
          $available_images = array();  
          
          while ($attempts > 0) {
            // get tags for the selection of 12 images
            $image_tags = MGTags::getTags($used_images);
            $found_one = false;
            $available_images = array();
            
            // check if one of the images has the needed tags for the play against the computer mode
            foreach ($image_tags as $image_id => $tags) {
              if (count($tags) >= $number_of_tag_needed) {
                $available_images[] = $image_id;
                break;
              } 
            }
            
            if (count($available_images)) {
              // found one with more than 1 tags
              break;
            } else {
              // haven't found one with >= $number_of_tag_needed tag.
              // select 12 other random images out of the available one
              $used_images = array();
              $turn_images = array_rand($images, 12);
              foreach ($turn_images as $i) {
                $used_images[] = (int)$images[$i]["id"];
              }
            }
            $attempts--; 
          }  
          
          if ($attempts == 0)
            throw new CHttpException(600, $game->name . Yii::t('app', ': Not enough tagged images available to play in computer mode'));
          
          // select one of the images as $describe_image_id
          $describe_image_id = $available_images[array_rand($available_images, 1)];
        }   
          
        
        $image_licences = array();
        $arr_licence_ids = array();
        
        // the needed information for the image.
        // make sure the image is present in all versions. rescale image if not 
        // by calling MGHelper::createScaledImage(...)
        foreach ($turn_images as $i) {
          $data["images"]["guess"][] = array(
            "image_id" => $images[$i]["id"],
            "full_size" => $path . "/images/". $images[$i]["name"],
            "thumbnail" => $path . "/thumbs/". $images[$i]["name"],
            "guess" => $path . "/scaled/". MGHelper::createScaledImage($images[$i]["name"], "", "scaled", $game->image_grid_width, $game->image_grid_height, 80, 10),
            "scaled" => $path . "/scaled/". MGHelper::createScaledImage($images[$i]["name"], "", "scaled", $game->image_width, $game->image_height, 80, 10),
            "licences" => $images[$i]["licences"],
          );
          // add the image's licence info to the licence info array
          $image_licences = array_merge($image_licences, $images[$i]["licences"]);
          $used_images[] = (int)$images[$i]["id"];
        }
        
        shuffle($data["images"]["guess"]); // mix the images up
        
        // one of the images has to be selected as image to be described
        $data["images"]["describe"] = array();
        if ($game->played_against_computer) {
          // if played against the computer prepare guess images
          foreach ($data["images"]["guess"] as $desc_image) {
            if ($desc_image["image_id"] == $describe_image_id) {
              $data["images"]["describe"] = $desc_image;
              break;
            }
          }
          shuffle($image_tags[$describe_image_id]); // mix them up
          $data["images"]["describe"]["hints"] = $image_tags[$describe_image_id];
          $describe_image_id = array($describe_image_id); // wrap it in array for words to avoid plugin(s) call
        } else {
          // select out of the twelve one that will be shown to the describing user
          $data["images"]["describe"] = $data["images"]["guess"][array_rand($turn_images, 1)];
          $describe_image_id = array((int)$data["images"]["describe"]["image_id"]); // wrap it in array for words to avoid plugin(s) call
        }
        
        // extract needed licence info
        $data["licences"] = $this->getLicenceInfo($image_licences);
        
        // save the used image data.
        $this->setUsedImages($used_images, $game, $game_model);
        
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
              $plugin->component->wordsToAvoid($data["wordstoavoid"], $describe_image_id, $game, $game_model, $tags);
            }
          }
        }
        
      } else 
        throw new CHttpException(600, $game->name . Yii::t('app', ': Not enough images available'));
      
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
   * @param Array the tags submitted by the player for each image
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
   * @param Array the tags submitted by the player for each image
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
   * Take the information from the submission and extract the tags for each image
   * involved in the current turn.
   * 
   * @param object $game The game object
   * @param object $game_model The game model
   * @return Array the tags for each image
   */
  public function parseTags(&$game, &$game_model) {
    $data = array();
    $image_ids = array();
    
    // go through all submissions 
    foreach ($game->request->submissions as $submission) {
      if ($submission['mode'] == 'describe') { // GuessWhat generates tags only on the describing mode
        // extract the image id
        $image_ids[] = $submission["image_id"];
        $image_tags = array();
        if (is_array($submission["tags"])) {
          // attempt to parse the tags. 
          foreach (MGTags::parseTags($submission["tags"]) as $tag) {
            $image_tags[strtolower($tag)] = array(
              'tag' => $tag,
              'weight' => 1,
              'type' => 'new',
              'tag_id' => 0
            );
          }  
        }
        $data[$submission["image_id"]] = $image_tags;
      }
    }
    
    if (count($image_ids)) { // if images are subitted
      
      // retrieve all tags for the tagged image
      $image_tags = MGTags::getTags($image_ids);
      
      // loop through all the submitted images
      foreach ($data as $submitted_image_id => $submitted_image_tags) {
        
        // loop through all the submitted tags
        foreach ($submitted_image_tags as $submitted_tag => $sval) {
          
          // has the submitted image already tags
          if (isset($image_tags[$submitted_image_id])) {
            
            // if the submitted image has tags loop through all of them
            foreach ($image_tags[$submitted_image_id] as $image_tag_id => $ival) {
              
              // if the submitted image has been tag with a tag that already exists 
              if ($submitted_tag == strtolower($ival["tag"])) {
                
                // set the tag type to match
                $data[$submitted_image_id][$submitted_tag]['type'] = 'match';
                $data[$submitted_image_id][$submitted_tag]['tag_id'] = $image_tag_id;
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
