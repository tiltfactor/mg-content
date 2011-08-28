<?php

class ZenTagGame extends MGGame implements MGGameInterface {
  public $two_player_game = false;
  
  public function validateSubmission($game) {
    return true; // implement this one
  }
  
  public function getTurn($game) {
    $data = array();
    $data["images"] = array();
    $data["licences"] = array();
    $data["wordstoavoid"] = array();
    return $data;
  }
  
  public function getScore($game, $tags) {
    
  }
}
