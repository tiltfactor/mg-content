<?php
/**
 * This is the implementation of a weighting plugin. 
 *  
 */

class ScoreNewMatchPluXXXgin extends MGWeightingPlugin  {
  public $enableOnInstall = true;
  public $hasAdmin = TRUE;
  public $accessRole = "dbmanager";
  
  function score(&$game, &$game_model, $tags, $score) {
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
              
          }
        }
      }
    }
    return $score;
  }
  
  function install() {
    $model = new ScoreBySubjectMatter;
    $model->fbvSave();
  }
}
