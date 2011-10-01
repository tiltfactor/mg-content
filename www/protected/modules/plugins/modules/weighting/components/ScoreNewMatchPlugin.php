<?php
/**
 * This is the implementation of a weighting plugin. 
 *  
 */

class ScoreNewMatchPlugin extends MGWeightingPlugin  {
  public $enableOnInstall = true;
  public $hasAdmin = TRUE;
  public $accessRole = "dbmanager";
  
  function score(&$game, &$game_model, &$tags, $score) {
    $model = new ScoreNewMatch;
    $model->fbvLoad();
    
    foreach ($tags as $image_id => $image_tags) {
      foreach ($image_tags as $tag => $tag_info) {
        if ($tag_info["weight"] > 0) {
          switch ($tag_info["type"]) {
            case "new":
              $this->addScore($tags[$image_id][$tag], (int)$model->score_new);
              $score = $score + (int)$model->score_new;
              break;
              
            case "match":
              $this->addScore($tags[$image_id][$tag], (int)$model->score_match);
              $score = $score + (int)$model->score_match;
              break;
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
