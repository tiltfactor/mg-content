<?php
/**
 * This plugin takes subject matter and the players interest, trust, and
 * expertise into account. 
 *  
 */

class ScoreBySubjectMatterPlugin extends MGWeightingPlugin  {
  public $enableOnInstall = true;
  public $hasAdmin = TRUE;
  public $accessRole = "dbmanager";
  
  /**
   * Reads the players subject matter and the players interest, trust, and
   * expertise. And gives extra points for these. All values can be set on 
   * the plugin's settings pages. 
   * 
   * @param object $game The currently active game
   * @param object $game_model The currently instance of the 
   * @param array $tags The tags that will be used as base for scoring
   * @param int $score The score that might be increased decreased 
   * @return int The new score after scroring through this plugin
   */
  function score(&$game, &$game_model, &$tags, $score) {
    $model = new ScoreBySubjectMatter;
    $model->fbvLoad();
    
    $media_ids = array_keys($tags); // retrieve used medias
    
    $media_subject_matter = array(); // store default level per media
    foreach ($media_ids as $media_id) {
      $media_subject_matter[$media_id] = "normal";
    }
    
    if (!Yii::app()->user->isGuest) {
      // current player is not guest
      $subject_matters = UserToSubjectMatter::listMAXForUserAndMedias(Yii::app()->user->id, $media_ids);
      // extract subject matter information
      if ($subject_matters) {
        foreach ($subject_matters as $subject_matter_info) {
          if ($subject_matter_info["expertise"] > 50) { // i assume everything > 50 is expert and expertise more important than trust
            $media_subject_matter[$subject_matter_info["media_id"]] = "expert";
            continue;
          }
          
          if ($subject_matter_info["trust"] > 50) { // i assume everything > 50 is trusted
            $media_subject_matter[$subject_matter_info["media_id"]] = "trusted";
            continue;
          }
        }  
      }
    }
    
    // loop through all tags and medias and add extra points if the user is
    // expert or trusted
    foreach ($tags as $media_id => $media_tags) {
      foreach ($media_tags as $tag => $tag_info) {
        if ($tag_info["weight"] > 0) {
          switch ($tag_info["type"]) {
            case "new":
              
              switch ($media_subject_matter[$media_id]) {
                case "expert":
                  $this->addScore($tags[$media_id][$tag], (int)$model->score_new_expert);
                  $score = $score + (int)$model->score_new_expert;
                  break;
                  
                case "trusted":
                  $this->addScore($tags[$media_id][$tag], (int)$model->score_new_trusted);
                  $score = $score + (int)$model->score_new_trusted;
                  break;
                  
                default:
                  $this->addScore($tags[$media_id][$tag], (int)$model->score_new);
                  $score = $score + (int)$model->score_new;
                  break;
              }
              break;
              
            case "match":
              switch ($media_subject_matter[$media_id]) {
                case "expert":
                  $this->addScore($tags[$media_id][$tag], (int)$model->score_match_expert);
                  $score = $score + (int)$model->score_match_expert;
                  break;
                  
                case "trusted":
                  $this->addScore($tags[$media_id][$tag], (int)$model->score_match_trusted);
                  $score = $score + (int)$model->score_match_trusted;
                  break;
                  
                default:
                  $this->addScore($tags[$media_id][$tag], (int)$model->score_match);
                  $score = $score + (int)$model->score_match;
                  break;
              }
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
    $model = new ScoreBySubjectMatter;
    $model->fbvSave();
  }
}
