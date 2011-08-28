<?php

class MGGame extends CApplicationComponent {
  public $two_player_game = false;
  
  public function getImages($game) {
    
  }
}

/**
 * Interface for Game Logic
 * @abstract
 */
interface MGGameInterface
{
  public function validateSubmission($game);
  public function getTurn($game);
  public function getScore($game);
}
