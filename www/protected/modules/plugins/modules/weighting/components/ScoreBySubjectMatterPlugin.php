<?php
/**
 * This is the implementation of a weighting plugin. 
 *  
 */

class ScoreBySubjectMatterPlugin extends MGWeightingPlugin  {
  public $enableOnInstall = true;
  public $hasAdmin = TRUE;
  public $accessRole = "dbmanager";
  
  function score(&$game, &$game_model, &$tags, $score) {
    $model = new ScoreBySubjectMatter;
    $model->fbvLoad();
    
    $image_ids = array_keys($tags); // retrieve used images
    
    $image_subject_matter = array(); // store default level per image
    foreach ($image_ids as $image_id) {
      $image_subject_matter[$image_id] = "normal";
    }
    
    if (!Yii::app()->user->isGuest) {
      $subject_matters = UserToSubjectMatter::listMAXForUserAndImages(Yii::app()->user->id, $image_ids);
      if ($subject_matters) {
        foreach ($subject_matters as $subject_matter_info) {
          if ($subject_matter_info["expertise"] > 50) { // i assume everything > 50 is expert and expertise more important than trust
            $image_subject_matter[$subject_matter_info["image_id"]] = "expert";
            continue;
          }
          
          if ($subject_matter_info["trust"] > 50) { // i assume everything > 50 is expert
            $image_subject_matter[$subject_matter_info["image_id"]] = "trusted";
            continue;
          }
        }  
      }
    }
    
    foreach ($tags as $image_id => $image_tags) {
      foreach ($image_tags as $tag => $tag_info) {
        if ($tag_info["weight"] > 0) {
          switch ($tag_info["type"]) {
            case "new":
              
              switch ($image_subject_matter[$image_id]) {
                case "expert":
                  $this->addScore($tags[$image_id][$tag], (int)$model->score_new_expert);
                  $score = $score + (int)$model->score_new_expert;
                  break;
                  
                case "trusted":
                  $this->addScore($tags[$image_id][$tag], (int)$model->score_new_trusted);
                  $score = $score + (int)$model->score_new_trusted;
                  break;
                  
                default:
                  $this->addScore($tags[$image_id][$tag], (int)$model->score_new);
                  $score = $score + (int)$model->score_new;
                  break;
              }
              break;
              
            case "match":
              switch ($image_subject_matter[$image_id]) {
                case "expert":
                  $this->addScore($tags[$image_id][$tag], (int)$model->score_match_expert);
                  $score = $score + (int)$model->score_match_expert;
                  break;
                  
                case "trusted":
                  $this->addScore($tags[$image_id][$tag], (int)$model->score_match_trusted);
                  $score = $score + (int)$model->score_match_trusted;
                  break;
                  
                default:
                  $this->addScore($tags[$image_id][$tag], (int)$model->score_match);
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
  
  function install() {
    $model = new ScoreBySubjectMatter;
    $model->fbvSave();
  }
}
