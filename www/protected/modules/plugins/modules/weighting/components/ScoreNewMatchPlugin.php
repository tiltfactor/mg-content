<?php
/**
 * This plugin compares the submitted tags with the tags of the images and
 * adds points whether the tag is new or matched. All 
 *
 *  
 */

class ScoreNewMatchPlugin extends MGWeightingPlugin  {
  public $enableOnInstall = true;
  public $hasAdmin = TRUE;
  public $accessRole = "dbmanager";
  
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
  
  /**
   * Ensures that the needed settings are saved in the setting file
   */
  function install() {
    $model = new ScoreNewMatch;
    $model->fbvSave();
  }
}
