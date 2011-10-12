<?php

class GuessWhatGame extends MGGame implements MGGameInterface {
  public $two_player_game = true;
  
  public function parseSubmission(&$game, &$game_model) {
    $game->request->submissions = array();  
    
    $success = true;
    
    if (isset($_POST["submissions"]) && is_array($_POST["submissions"]) && count($_POST["submissions"]) > 0) {
      foreach ($_POST["submissions"] as $submission) {
        if ($submission["image_id"] && (int)$submission["image_id"] != 0
          && $submission["tags"] && (string)$submission["tags"] != "") {
          $game->request->submissions[] = $submission;
        } 
      }
    }
    $success = (count($game->request->submissions) > 0);
    
    $plugins = PluginsModule::getActiveGamePlugins($game->game_id, "dictionary");
    if (count($plugins) > 0) {
      foreach ($plugins as $plugin) {
        if (method_exists($plugin->component, "parseSubmission")) {
          $success = $success  && $plugin->component->parseSubmission($game, $game_model);
        }
      }
    }
    
    $plugins = PluginsModule::getActiveGamePlugins($game->game_id, "weighting");
    if (count($plugins) > 0) {
      foreach ($plugins as $plugin) {
        if (method_exists($plugin->component, "parseSubmission")) {
          $success = $success  && $plugin->component->parseSubmission($game, $game_model);
        }
      }
    }
    
    return $success;
  }
    
  public function getTurn(&$game, &$game_model, $tags=array()) {
    if ($game->played_against_computer) {
      return $this->_createTurn($game, $game_model, $tags);
    } else {
      $turn = $this->loadTwoPlayerTurnFromDb($game->played_game_id, $game->turn + 1);
      if (is_null($turn)) {
        $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
        $turn = $this->_createTurn($game, $game_model, $tags);
        
        Yii::log('aaaa','error');
        
        // it might happen that for both user it might appear to be the first one to read the table
        // thus the next statement check whether the turn has been saved for this played game and turn
        // if it has been a unique exception forces the second user to load the turn from the db
        // can't use table locks as $game_engine->getTurn uses various and potentually unknown tables that would all have to 
        // be included into the lock statement
        if (!$this->saveTwoPlayerTurnToDb($game->played_game_id, $game->turn + 1, (int)Yii::app()->session[$api_id .'_SESSION_ID'], $turn)) {
          $turn = $this->loadTwoPlayerTurnFromDb($game->played_game_id, $game->turn + 1); 
          if ($game->turn + 1 == 1)
            $turn["mode"] = "describe";
        }
      } else {
        if ($game->turn + 1 == 1)
          $turn["mode"] = "describe";
      }
    }
    
    if (is_null($turn)) {
      throw new CHttpException(500, Yii::t('app', 'Internal Server Error.'));
    }
    
    return $turn;
  }  
    
  private function _createTurn(&$game, &$game_model, $tags=array()) {
    $data = array();
    $data["mode"] = "guess";
    
    if ($game->turn < $game->turns) {
      $imageSets = $this->getImageSets($game, $game_model);
    
      $data["images"] = array(
        "guess" => array(),
        "describe" => array(),
      );
      
      $used_images = array();
      $images = $this->getImages($imageSets, $game, $game_model, 12);
      
      if ($images && count($images) >= 12) {
        $path = Yii::app()->getBaseUrl(true) . Yii::app()->fbvStorage->get('settings.app_upload_url');
        $turn_images = array_rand($images, 12);
        
        $image_licences = array();
        
        $arr_licence_ids = array();
        foreach ($turn_images as $i) {
          $data["images"]["guess"][] = array(
            "image_id" => $images[$i]["id"],
            "full_size" => $path . "/images/". $images[$i]["name"],
            "thumbnail" => $path . "/thumbs/". $images[$i]["name"],
            "guess" => $path . "/scaled/". MGHelper::createScaledImage($images[$i]["name"], "", "scaled", $game->image_grid_width, $game->image_grid_height, 80, 10),
            "scaled" => $path . "/scaled/". MGHelper::createScaledImage($images[$i]["name"], "", "scaled", $game->image_width, $game->image_height, 80, 10),
            "licences" => $images[$i]["licences"],
          );
          $image_licences = array_merge($image_licences, $images[$i]["licences"]);
          $used_images[] = (int)$images[$i]["id"];
        }
        
        shuffle($data["images"]["guess"]); // mix the images up
        
        // select out of the twelve one that will be shown to the describing user
        $data["images"]["describe"] = $data["images"]["guess"][array_rand($turn_images, 1)];
        $describe_image_id = array((int)$data["images"]["describe"]["image_id"]);
        
        $used_images[] = (int)$images[$i]["id"];
        
        $data["licences"] = $this->getLicenceInfo($image_licences);
        
        $this->setUsedImages($used_images, $game, $game_model);

        $data["tags"] = array();
        $data["tags"]["user"] = $tags;
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
        throw new CHttpException(500, $game->name . Yii::t('app', ': Not enough images available'));
      
    } else {
      $data["tags"] = array();
      $data["tags"]["user"] = $tags;
      $data["licences"] = array();
    } 
    
    return $data;
  }
  
  public function setWeights(&$game, &$game_model, $tags) {
    $plugins = PluginsModule::getActiveGamePlugins($game->game_id, "dictionary");
    if (count($plugins) > 0) {
      foreach ($plugins as $plugin) {
        if (method_exists($plugin->component, "setWeights")) {
          $tags = $plugin->component->setWeights($game, $game_model, $tags);
        }
      }
    }
    
    $plugins = PluginsModule::getActiveGamePlugins($game->game_id, "weighting");
    if (count($plugins) > 0) {
      foreach ($plugins as $plugin) {
        if (method_exists($plugin->component, "setWeights")) {
          $tags = $plugin->component->setWeights($game, $game_model, $tags);
        }
      }
    }
    return $tags;
  }
  
  public function getScore(&$game, &$game_model, &$tags) {
    
    /*
     * new tag = +1 point
[08/10/2011 17:41:39] punkybuddha: guess on first try = +5 point
[08/10/2011 17:41:50] punkybuddha: guess on second try = +3 point
[08/10/2011 17:41:56] punkybuddha: guess on third try = +1 point
     */
    $score = 0;
    $plugins = PluginsModule::getActiveGamePlugins($game->game_id, "weighting");
    if (count($plugins) > 0) {
      foreach ($plugins as $plugin) {
        if (method_exists($plugin->component, "score")) {
          $score = $plugin->component->score($game, $game_model, $tags, $score);
        }
      }
    }
    return $score;
  }
  
  public function parseTags(&$game, &$game_model) {
    $data = array();
    $image_ids = array();
    foreach ($game->request->submissions as $submission) {
      $image_ids[] = $submission["image_id"];
      $image_tags = array();
      foreach (MGTags::parseTags($submission["tags"]) as $tag) {
        $image_tags[strtolower($tag)] = array(
          'tag' => $tag,
          'weight' => 1,
          'type' => 'new',
          'tag_id' => 0
        );
      }
      $data[$submission["image_id"]] = $image_tags;
    }
    
    if (!$game->played_against_computer && $this->two_player_game && isset($game->opponents_submission) && is_array($game->opponents_submission)) {
      // it is really a two player game and we have to parse the oppenents_submission to make the tags info available for later use
      
      $game->opponents_submission["parsed"] = array();
      
      foreach ($game->opponents_submission as $image) {
        if (is_object($image)) {
          $image_ids[] = $image->image_id;
        
          $image_tags = array();
          foreach (MGTags::parseTags($image->tags) as $tag) {
            $image_tags[strtolower($tag)] = array(
              'tag' => $tag,
              'weight' => 1,
              'type' => 'new',
              'tag_id' => 0
            );
          }
          $game->opponents_submission["parsed"][$image->image_id] = $image_tags;
        }
      }
    }
    
    $image_tags = MGTags::getTags($image_ids);
    foreach ($data as $submitted_image_id => $submitted_image_tags) {
      foreach ($submitted_image_tags as $submitted_tag => $sval) {
        if (isset($image_tags[$submitted_image_id])) {
          foreach ($image_tags[$submitted_image_id] as $image_tag_id => $ival) {
            if ($submitted_tag == strtolower($ival["tag"])) {
              $data[$submitted_image_id][$submitted_tag]['type'] = 'match';
              $data[$submitted_image_id][$submitted_tag]['tag_id'] = $image_tag_id;
              break;
            }
          }          
        }
      }
    }
    
    if (!$game->played_against_computer && $this->two_player_game && isset($game->opponents_submission) && is_array($game->opponents_submission["parsed"])) {
      foreach ($game->opponents_submission["parsed"] as $submitted_image_id => $submitted_image_tags) {
        foreach ($submitted_image_tags as $submitted_tag => $sval) {
          if (isset($image_tags[$submitted_image_id])) {
            foreach ($image_tags[$submitted_image_id] as $image_tag_id => $ival) {
              if ($submitted_tag == strtolower($ival["tag"])) {
                $game->opponents_submission["parsed"][$submitted_image_id][$submitted_tag]['type'] = 'match';
                $game->opponents_submission["parsed"][$submitted_image_id][$submitted_tag]['tag_id'] = $image_tag_id;
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
   * @return boolean true if the hint is valid false if the hint has been found in the stopword list.
   */
  public function validateHint(&$game, &$game_model, $parameter) {
    $return_valid = "";
      
    if (isset($parameter->hint) && trim($parameter->hint) != "") {
      $arr_tags = MGTags::parseTags($parameter->hint);
      if (count($arr_tags)) {
          
        $plugin = PluginsModule::getActiveGamePlugin($game->game_id, "dictionary", 'StopWord');
        if ($plugin) {
          if (method_exists($plugin->component, "lookup")) {
            $tags = $plugin->component->lookup($game, $game_model, array($arr_tags[0]));
            
            foreach ($tags as $tag => $valid) {
              if ($tag == strtolower($arr_tags[0]) && !$valid) {
                $return_valid = $arr_tags[0];
                break;
              }
            }
          }
        }
      }
    }
    return $return_valid;
  } 
}
