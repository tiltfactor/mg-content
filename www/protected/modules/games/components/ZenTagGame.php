<?php
/**
 * Implementation of the needed methods for Zen Tag
 *
 * @package    MG
 * @author     Vincent Van Uffelen <novazembla@gmail.com>
 */
class ZenTagGame extends MGGame implements MGGameInterface {
  
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
    // make the requests submission available for further method calls 
    $game->request->submissions = array();  
    
    $success = true;
    
    // check the POST request if the expected submission field is presend and correctly set
    if (isset($_POST["submissions"]) && is_array($_POST["submissions"]) && count($_POST["submissions"]) > 0) {
      // loop through all submissions and validate them
      foreach ($_POST["submissions"] as $submission) {
        if ($submission["media_id"] && (int)$submission["media_id"] != 0
          && $submission["tags"] && (string)$submission["tags"] != "") {
	  // DEBUG - Figure out what's in the tag array here.
	  //Yii::log("Tags here are: " + var_export($submission["tags"], true),
	  //         "Error");

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
   * + Retrieve wordstoavoid
   * + Retrieve licence info
   * 
   * 
   * @param object $game The game object
   * @param object $game_model The game model
   * @param Array the tags submitted by the player for each media
   * @return Array the turn information that will be sent to the players client
   */
  public function getTurn(&$game, &$game_model, $tags=array()) {
    $data = array();
    
    // check if the game is not actually over
    if ($game->turn < $game->turns) {
      
      //retrieve the collections that are active for this game
        $collections = $this->getCollections($game, $game_model);
      
      $data["medias"] = array();
      
      $used_medias = array();
      
      // get a one medias that is active for the game
      $medias = $this->getMedias($collections, $game, $game_model);
      
      
      if ($medias && count($medias) > 0) {
        $i = array_rand($medias, 1); // select one random item out of the medias
      
        // the needed information for the media.
        // make sure the media is present in all versions. rescale media if not
        // by calling MGHelper::createScaledImage(...)
        $path = Yii::app()->getBaseUrl(true) . Yii::app()->fbvStorage->get('settings.app_upload_url');
        $data["medias"][] = array(
          "media_id" => $medias[$i]["id"],
          "full_size" => $path . "/medias/". $medias[$i]["name"],
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
        
        // the following lines call the wordsToAvoid methods of the activated dictionary 
        // plugin this generates a words to avoid list 
        $data["wordstoavoid"] = array();
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
      $data["licences"] = array(); // no need to show licences on the last screen as the previous turns are cached by javascript and therefore all licence info is available
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
    
    // User Passing: If the user has passed on this turn, we'll just
    // assign a score of zero (0) and return.
    //
    // NOTE: Even though in an ordinary "passing" round we'd only have
    // a single tag, given that we are passed a complicated packed
    // datastructure here, it's easiest to use foreach loops to check
    // for the PASS code.
    foreach ($tags as $media_id => $media_tags) {
      foreach ($media_tags as $tag => $tag_info) {
	if(strcasecmp($tag, "PASSONTHISTURN") == 0) {
	  return 0;
	}
      }
    }
    
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
    
    // loop through all submissions for this turn
    foreach ($game->request->submissions as $submission) {
      
      // the media that has been tagged by the user in the previous turn
      $media_ids[] = $submission["media_id"];
      $media_tags = array();
      
      // the submission has in the case of ZenTag just one media and a
      // string of commaseparated tags. 
      
      // Attempt to extract these 
      foreach (MGTags::parseTags($submission["tags"]) as $tag) {
        $media_tags[strtolower($tag)] = array(
          'tag' => $tag,
          'weight' => 1,
          'type' => 'new',
          'tag_id' => 0
        );
      }
      // add the extracted tags to the media info
      $data[$submission["media_id"]] = $media_tags;
    }
    
    // the following line work with one or more medias. The code might be
    // a bit more complex than needed in this case. 
    
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
    
    return $data;
  }
}
