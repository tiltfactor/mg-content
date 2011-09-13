<?php

class ZenTagGame extends MGGame implements MGGameInterface {
  public $two_player_game = false;
  
  public function parseSubmission(&$game, &$game_model) {
    $game->request->submissions = array();  

    if (isset($_POST["submissions"]) && is_array($_POST["submissions"]) && count($_POST["submissions"]) > 0) {
      foreach ($_POST["submissions"] as $submission) {
        if ($submission["image_id"] && (int)$submission["image_id"] != 0
          && $submission["tags"] && (string)$submission["tags"] != "") {
          $game->request->submissions[] = $submission;
        } 
      }
    }
    
    $game->request->wordstoavoid = array(); 
    if (isset($_POST["wordstoavoid"]) && is_array($_POST["wordstoavoid"]) && count($_POST["wordstoavoid"]) > 0) {
      foreach ($_POST["wordstoavoid"] as $image_id => $image) {
        if (is_array($image) && count($image) > 0) {
          $game->request->wordstoavoid[$image_id] = $image;
        }
      }
    }
    
    return (count($game->request->submissions) > 0);
  }
    
  public function getTurn($game, &$game_model, $tags=array()) {
    $data = array();
    if ($game->turn < $game->turns) {
      $imageSets = $this->getImageSets($game, $game_model);
    
      $data["images"] = array();
      
      $used_images = array();
      $images = $this->getImages($imageSets, $game, $game_model);
      
      if ($images && count($images) > 0) {
        $i = array_rand($images, 1);
      
        $path = Yii::app()->getBaseUrl(true) . Yii::app()->fbvStorage->get('settings.app_upload_url');
        $data["images"][] = array(
          "image_id" => $images[$i]["id"],
          "full_size" => $path . "/images/". $images[$i]["name"],
          "thumbnail" => $path . "/thumbs/". $images[$i]["name"],
          "final_screen" => $path . "/scaled/". MGHelper::createScaledImage($images[$i]["name"], "", "scaled", 212, 171, 80, 10),
          "scaled" => $path . "/scaled/". MGHelper::createScaledImage($images[$i]["name"], "", "scaled", $game->image_width, $game->image_height, 80, 10),
          "licences" => $images[$i]["licences"],
        );
        $used_images[] = (int)$images[$i]["id"];
        
        $data["licences"] = $this->getLicenceInfo($images[$i]["licences"]);
        
        $this->setUsedImages($used_images, $game, $game_model);

        $data["tags"] = array();
        $data["tags"]["user"] = $tags;
        $data["wordstoavoid"] = MGTags::getTagsByWeightThreshold($used_images, (int)$game->words_to_avoid_threshold);
        
      } else 
        throw new CHttpException(500, $game->name . Yii::t('app', ': Not enough images available'));
      
    } else {
      $data["tags"] = array();
      $data["tags"]["user"] = $tags;
      $data["licences"] = array(); // xxx implement
    } 
    
    return $data;
  }
  
  public function setWeights($game, &$game_model, $tags) {
    
    // go through last turns words to avoid and weight matching tags 0
    if (isset($game->request->wordstoavoid) && is_array($game->request->wordstoavoid)) {
      foreach ($game->request->wordstoavoid as $wta_image_id => $wta_image) {
        if (array_key_exists($wta_image_id, $tags)) {
          foreach ($wta_image as $wta_tag_id => $wta_tag) {
            if (array_key_exists($wta_tag["tag"], $tags[$wta_image_id])) {
              $tags[$wta_image_id][$wta_tag["tag"]]["type"] = 'wordstoavoid';
              $tags[$wta_image_id][$wta_tag["tag"]]["weight"] = 0;
            }
          }
        }
      }
    }
    
    // xxx implement stopword weight here
    return $tags;
  }
  
  public function getScore($game, &$game_model, &$tags) {
    $score = 0;
    foreach ($tags as $image_id => $image_tags) {
      foreach ($image_tags as $tag => $tag_info) {
        if ($tag_info["weight"] > 0) {
          switch ($tag_info["type"]) {
            case "new":
              $tags[$image_id][$tag]["score"] = (int)$game->score_new;
              $score = $score + (int)$game->score_new;
              break;
              
            case "match":
              $tags[$image_id][$tag]["score"] = (int)$game->score_match;
              $score = $score + (int)$game->score_match;
              break;
              
            //xxx get expert trust whatever scoring here
          }
        }
      }
    }
    return $score;
  }
  
  public function parseTags($game, &$game_model) {
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
    
    $image_tags = MGTags::getTags($image_ids);
    
    foreach ($data as $submitted_image_id => $submitted_image_tags) {
      foreach ($submitted_image_tags as $submitted_tag => $sval) {
        if (isset($image_tags[$submitted_image_id])) {
          foreach ($image_tags[$submitted_image_id] as $image_tag_id => $ival) {
            if ($submitted_tag == strtolower($ival["tag"])) {
              $data[$submission["image_id"]][$submitted_tag]['type'] = 'match';
              $data[$submission["image_id"]][$submitted_tag]['tag_id'] = $image_tag_id;
              break;
            }
          }          
        }
      }
    }
    
    // xxx get somehow tags of user here tags here
     
    return $data;
  }
}
