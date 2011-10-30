<?php

class GuessWhatGame extends MGGame implements MGGameInterface {
  public $two_player_game = true;
  
  public function parseSubmission(&$game, &$game_model) {
    $game->request->submissions = array();  
    
    $success = true;
    
    if (isset($_POST["submissions"]) && is_array($_POST["submissions"]) && count($_POST["submissions"]) > 0) {
      foreach ($_POST["submissions"] as $submission) {
        if (  isset($submission["mode"]) &&
              isset($submission["hints"]) &&
              isset($submission["guesses"]) &&
              isset($submission["image_id"]) &&
              isset($submission["tags"])) {
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
    if (!$game->played_against_computer) {
      $data["mode"] = "guess";
    }
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
        $describe_image_id = 0;
        $image_tags = array();
        
        foreach ($turn_images as $i) {
          $used_images[] = (int)$images[$i]["id"];
        }
        
        if ($game->played_against_computer) { // we have to make sure that at least one of the images has got more than 10 tags to give hints on. 
          $attempts = 10;
          $available_images = array();  
          
          while ($attempts > 0) {
            $image_tags = MGTags::getTags($used_images);
            $found_one = false;
            $available_images = array();
            
            foreach ($image_tags as $image_id => $tags) {
                if (count($tags) > 10) {
                  $available_images[] = $image_id;
                  break;
                } 
            }
           
            if (count($available_images)) {
              break;
            } else {
              $used_images = array();
              $turn_images = array_rand($images, 12);
              foreach ($turn_images as $i) {
                $used_images[] = (int)$images[$i]["id"];
              }
            }
            $attempts--; 
          }  
          
          if ($attempts == 0)
            throw new CHttpException(500, $game->name . Yii::t('app', ': Not enough tagged images available to play in computer mode'));
          
          $describe_image_id = $available_images[array_rand($available_images, 1)];
        }   
          
        
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
        
        $data["images"]["describe"] = array();
        if ($game->played_against_computer) {
          foreach ($data["images"]["guess"] as $desc_image) {
            if ($desc_image["image_id"] == $describe_image_id) {
              $data["images"]["describe"] = $desc_image;
              break;
            }
          }
          shuffle($image_tags[$describe_image_id]);
          $data["images"]["describe"]["hints"] = $image_tags[$describe_image_id];
          $describe_image_id = array($describe_image_id); // wrap it in array for words to avoid plugin(s) call
        } else {
          // select out of the twelve one that will be shown to the describing user
          $data["images"]["describe"] = $data["images"]["guess"][array_rand($turn_images, 1)];
          $describe_image_id = array((int)$data["images"]["describe"]["image_id"]); // wrap it in array for words to avoid plugin(s) call
        }
        
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
      if ($submission['mode'] == 'describe') { // GuessWhat generates tags only on the describing mode
        $image_ids[] = $submission["image_id"];
        $image_tags = array();
        if (is_array($submission["tags"])) {
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
    
    if (count($image_ids)) {
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
        } else {
          $return_valid = $arr_tags[0];
        } 
      }
    }
    return $return_valid;
  } 
}
