<?php
/**
 * This plugin makes use of the two player information to match the players submitted tags.
 * In case both player have submitted the same matching or new tag a bonus will be added to the score. 
s */

class TwoPlayerBonusPlugin extends MGWeightingPlugin  {
  public $enableOnInstall = true;
  public $hasAdmin = TRUE;
  public $accessRole = "dbmanager";
  
  /**
   * give each tag that has been submitted by both users a bit more weight
   */
  function setWeights(&$game, &$game_model, $tags) {
    // go through last turns words to avoid and weight matching tags 0
    if (isset($game->oponenents_submission) && isset($game->oponenents_submission["parsed"]) && is_array($game->oponenents_submission["parsed"])) { // make sure the game is really a two player game and the oponents_submission is set
      foreach ($game->oponenents_submission["parsed"] as $submitted_image_id => $submitted_image_tags) {
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
    return $tags;
  }
  
  function score(&$game, &$game_model, &$tags, $score) {
    $model = new TwoPlayerBonus;
    $model->fbvLoad();
    if (isset($game->oponenents_submission) && isset($game->oponenents_submission["parsed"]) && is_array($game->oponenents_submission["parsed"])) { // make sure the game is really a two player game and the oponents_submission is set
      foreach ($game->oponenents_submission["parsed"] as $submitted_image_id => $submitted_image_tags) {
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
    return $score;
  }
  
  function install() {
    $model = new ScoreNewMatch;
    $model->fbvSave();
  }
}
