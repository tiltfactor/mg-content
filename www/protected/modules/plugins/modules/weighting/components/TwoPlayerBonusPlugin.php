<?php
/**
 * This plugin makes use of the two player information to match the players submitted tags.
 * In case both player have submitted the same matching or new tag a bonus will be added to the score. 
 */

class TwoPlayerBonusPlugin extends MGWeightingPlugin  {
  public $enableOnInstall = true;
  public $hasAdmin = TRUE;
  public $accessRole = "dbmanager";
  
  /**
   * Give each tag that has been submitted by both users a bit more weight
   * 
   * @param object $game The currently active game
   * @param object $game_model The currently instance of the 
   * @param array $tags The tags that have to be rewighted
   * @return array The weightened tags
   */
  function setWeights(&$game, &$game_model, $tags) {
    if (!$game->played_against_computer) {
      // go through last turns words to avoid and weight matching tags 0
      if (isset($game->opponents_submission) && isset($game->opponents_submission["parsed"]) && is_array($game->opponents_submission["parsed"])) { // make sure the game is really a two player game and the opponents_submission is set
        foreach ($game->opponents_submission["parsed"] as $submitted_image_id => $submitted_image_tags) {
          if (array_key_exists($submitted_image_id, $tags)) {
            foreach ($submitted_image_tags as $submitted_tag => $sval) {
              foreach ($tags[$submitted_image_id] as $image_tag_id => $ival) {
                if ($submitted_tag == strtolower($ival["tag"])) {
                  $this->adjustWeight($tags[$submitted_image_id][$submitted_tag], 0.5);
                  break;
                }
              }          
            }
          }  
        }
      }
    }
    return $tags;
  }
  
  /**
   * Compares the submitted tags with the tags of the images and adds points 
   * whether the tag is new or matched. All extra points can be set via the 
   * backend. 
   * 
   * @param object $game The currently active game
   * @param object $game_model The currently instance of the 
   * @param array $tags The tags that will be used as base for scoring
   * @param int $score The score that might be increased decreased 
   * @return int The new score after scroring through this plugin
   */
  function score(&$game, &$game_model, &$tags, $score) {
    if (!$game->played_against_computer) { // make sure there is a human opponent
      $model = new TwoPlayerBonus;
      $model->fbvLoad();
      if (isset($game->opponents_submission) && isset($game->opponents_submission["parsed"]) && is_array($game->opponents_submission["parsed"])) { // make sure the game is really a two player game and the opponents_submission is set
        foreach ($game->opponents_submission["parsed"] as $submitted_image_id => $submitted_image_tags) {
          if (array_key_exists($submitted_image_id, $tags)) {
            foreach ($submitted_image_tags as $submitted_tag => $sval) {
              foreach ($tags[$submitted_image_id] as $image_tag_id => $ival) {
                if ($submitted_tag == strtolower($ival["tag"])) {
                  switch ($ival["type"]) {
                    case "new":
                      $this->addScore($tags[$submitted_image_id][$submitted_tag], (int)$model->score_new);
                      $score = $score + (int)$model->score_new;
                      break;
                      
                    case "match":
                      $this->addScore($tags[$submitted_image_id][$submitted_tag], (int)$model->score_match);
                      $score = $score + (int)$model->score_match;
                      break;
                  }
                  break;
                }
              }          
            }
          }
        }  
      }
    }
    return $score;
  }
  
  /**
   * Ensures that the needed settings are saved in the setting file
   */
  function install() {
    $model = new ScoreNewMatch;
    $model->fbvSave();
  }
}
