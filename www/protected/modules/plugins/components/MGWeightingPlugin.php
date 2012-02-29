<?php

/**
 * This is the base implementation of a weighting plug-in 
 */

class MGWeightingPlugin extends MGPlugin {  
  function init() {
    parent::init();
  }
  
  /**
   * The method will be called to generate the score for one turn. Several weighting 
   * plugins can be called to built up a compound score. 
   * 
   * @param object $game The currently active game
   * @param object $game_model The currently instance of the 
   * @param array $tags The tags that will be used as base for scoring
   * @param int $score The score that might be increased decreased 
   * @return int The new score after scroring through this plugin
   */
  function score(&$game, &$game_model, &$tags, $score) {
    return 0;
  }
  
  /**
   * The method called by games to reweight any passed tags. 
   * 
   * $tags is the result of the call of the game's parseTags method implementation
   * 
   * @param object $game The currently active game
   * @param object $game_model The currently instance of the 
   * @param array $tags The tags that have to be rewighted
   * @return array The weightened tags
   */
  function setWeights(&$game, &$game_model, $tags) {
    return $tags;
  }
  
  /**
   * This handler allows weighting plugins to contribute to the game submissions parsing
   * 
   * @param object $game the game object
   * @param object $game_model the active model of the current game
   * @return boolean true if parsing was successful
   */
  function parseSubmission(&$game, &$game_model) {
    return true;
  }
}
