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
        if ($submission["image_id"] && (int)$submission["image_id"] != 0
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
   * + Retrive a new image list for the next turn
   * + Retrieve wordstoavoid
   * + Retrieve licence info
   * 
   * 
   * @param object $game The game object
   * @param object $game_model The game model
   * @param Array the tags submitted by the player for each image
   * @return Array the turn information that will be sent to the players client
   */
  public function getTurn(&$game, &$game_model, $tags=array()) {
    $data = array();
    
    // check if the game is not actually over
    if ($game->turn < $game->turns) {
      
      //retrieve the image sets that are active for this game
      $imageSets = $this->getImageSets($game, $game_model);
      
      $data["images"] = array();
      
      $used_images = array();
      
      // get a one images that is active for the game
      $images = $this->getImages($imageSets, $game, $game_model);
      
      
      if ($images && count($images) > 0) {
        $i = array_rand($images, 1); // select one random item out of the images
      
        // the needed information for the image.
        // make sure the image is present in all versions. rescale image if not 
        // by calling MGHelper::createScaledImage(...)
        $path = Yii::app()->getBaseUrl(true) . Yii::app()->fbvStorage->get('settings.app_upload_url');
        $data["images"][] = array(
          "image_id" => $images[$i]["id"],
          "full_size" => $path . "/images/". $images[$i]["name"],
          "thumbnail" => $path . "/thumbs/". $images[$i]["name"],
          "final_screen" => $path . "/scaled/". MGHelper::createScaledImage($images[$i]["name"], "", "scaled", 212, 171, 80, 10),
          "scaled" => $path . "/scaled/". MGHelper::createScaledImage($images[$i]["name"], "", "scaled", $game->image_width, $game->image_height, 80, 10),
          "licences" => $images[$i]["licences"],
        );
        
        // add the image to the list of images that will be saved in the session so the
        // user sees the image only once
        $used_images[] = (int)$images[$i]["id"];
        
        // extract needed licence info
        $data["licences"] = $this->getLicenceInfo($images[$i]["licences"]);
        
        // save the used image data.
        $this->setUsedImages($used_images, $game, $game_model);
        
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
              $plugin->component->wordsToAvoid($data["wordstoavoid"], $used_images, $game, $game_model, $tags);
            }
          }
        }
        
      } else 
        throw new CHttpException(600, $game->name . Yii::t('app', ': Not enough images available')); 
      
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
    
    // loop through all submissions for this turn
    foreach ($game->request->submissions as $submission) {
      
      // the image that has been tagged by the user in the previous turn
      $image_ids[] = $submission["image_id"]; 
      $image_tags = array();
      
      // the submission has in the case of ZenTag just one image and a
      // string of commaseparated tags. 
      
      // Attempt to extract these 
      foreach (MGTags::parseTags($submission["tags"]) as $tag) {
        $image_tags[strtolower($tag)] = array(
          'tag' => $tag,
          'weight' => 1,
          'type' => 'new',
          'tag_id' => 0
        );
      }
      // add the extracted tags to the image info 
      $data[$submission["image_id"]] = $image_tags;
    }
    
    // the following line work with one or more images. The code might be 
    // a bit more complex than needed in this case. 
    
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
    
    return $data;
  }
}
