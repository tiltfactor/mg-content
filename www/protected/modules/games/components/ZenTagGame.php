<?php

class ZenTagGame extends MGGame implements MGGameInterface {
  public $two_player_game = false;
  
  public function validateSubmission($game, &$game_model) {
    return true; // implement this one
  }
  
  public function getTurn($game, &$game_model) {
    $data = array();
    
    $imageSets = $this->getImageSets($game, $game_model);
    
    
    $data["images"] = array();
    
    $used_images = array();
    $images = $this->getImages($imageSets, $game, $game_model);
    
    $i = array_rand($images, 1);
    
    $path = Yii::app()->getBaseUrl(true) . Yii::app()->params['upload_url'];
    $data["images"][] = array(
      "full_size" => $path . "/images/". $images[$i]["name"],
      "thumbnail" => $path . "/thumbs/". $images[$i]["name"],
      "scaled" => $path . "/scaled/". MGHelper::createScaledImage($images[$i]["name"], "", "scaled", $game->image_width, $game->image_height, 80, 10),
    );
    $used_images[] = (int)$images[$i]["id"];
    
    
    $this->setUsedImages($used_images, $game, $game_model);
    
    
    $data["licences"] = array();
    $data["wordstoavoid"] = array();
    return $data;
  }
  
  public function getScore($game, &$game_model, $tags) {
    
  }
}
