<?php
/**
 * This plugin makes use of the two player information to match the players submitted tags.
 * In case both player have submitted the same matching or new tag a bonus will be added to the score. 
 */
class GuessWhatScoringPlugin extends MGWeightingPlugin  {
  public $enableOnInstall = true;
  public $hasAdmin = TRUE;
  public $accessRole = "dbmanager";
  
  /**
   * Give each tag that has been submitted by both users a bit more weight
   * by adding a small value that can be set on the plugin's settings page
   * 
   * @param object $game The currently active game
   * @param object $game_model The currently instance of the 
   * @param array $tags The tags that have to be rewighted
   * @return array The weightened tags
   */
  function setWeights(&$game, &$game_model, $tags) {
    $model = new GuessWhatScoring;
    $model->fbvLoad();
    
    if (!$game->played_against_computer && (float)$model->additional_weight_first_guess > 0) {
      // if an image has been guessed with one guess the tag should get an higher weight as it apparently describes 
      // the image very accurately
      foreach ($game->request->submissions as $submission) {
        if (count($submission['guesses']) == 1) {
          foreach ($tags as $i_id => $i_tags) {
            foreach ($i_tags as $i_tag => $i_tag) {
              $this->adjustWeight($tags[$i_id][$i_tag], (float)$model->additional_weight_first_guess);
            }
          }
        }
      }
    }
    return $tags;
  }
  
  /**
   * This is the implementation of the score method. It give the describing user 
   * points for new and matching tags. And the guessing user extra points for guessing
   * the image on the first, second, or third attempt. 
   * 
   * All point values can be set on the plugin's setting page 
   *  
   * @param object $game The currently active game
   * @param object $game_model The currently instance of the 
   * @param array $tags The tags that will be used as base for scoring
   * @param int $score The score that might be increased decreased 
   * @return int The new score after scroring through this plugin
   */
  function score(&$game, &$game_model, &$tags, $score) {
    $model = new GuessWhatScoring;
    $model->fbvLoad();
    
    foreach ($game->request->submissions as $submission) {
      if (!$game->played_against_computer && $submission['mode'] == 'describe') { 
        // the user has described an image this turn and becomes thus poins for new tags
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
      }
      
      if (is_array($submission["guesses"]) && count($submission["guesses"]) && in_array($submission["image_id"], $submission["guesses"])) {
        switch (count($submission["guesses"])) {
          case 1: // image guessed on first try
            $score += (int)$model->score_first_guess;
            break;
            
          case 2: // image guessed on second try
            $score += (int)$model->score_second_guess;
            break;
            
          default: // image guessed on all other attempts
            $score += (int)$model->score_third_guess;
            break; 
        }
      }
      
      break; // we expect only one submission.
    }
    return $score;
  }
  
  /**
   * Ensures that the needed settings are saved in the setting file
   */
  function install() {
    $model = new GuessWhatScoring;
    $model->fbvSave();
  }
}
