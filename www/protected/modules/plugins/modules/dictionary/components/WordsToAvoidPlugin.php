<?php
/**
 * This is the implentation of the wordstoavoid functionality
 */

class WordsToAvoidPlugin extends MGDictionaryPlugin  {
  public $enableOnInstall = true;
  public $hasAdmin = true;
  
  /**
   * This handler allows dictionary plugins to contribute to the game submissions parsing
   * 
   * @param object $game the game object
   * @param object $game_model the active model of the current game
   * @return boolean true if the validation after parsing was successful
   */
  function parseSubmission(&$game, &$game_model) {
    $game->request->wordstoavoid = array(); 
    if (isset($_POST["wordstoavoid"]) && is_array($_POST["wordstoavoid"]) && count($_POST["wordstoavoid"]) > 0) {
      foreach ($_POST["wordstoavoid"] as $image_id => $image) {
        if (is_array($image) && count($image) > 0) {
          $game->request->wordstoavoid[$image_id] = $image;
        }
      }
    }
    return true;
  }
    
    
  function setWeights(&$game, &$game_model, $tags) {
    // go through last turns words to avoid and weight matching tags 0
    if (isset($game->request->wordstoavoid) && is_array($game->request->wordstoavoid)) {
      foreach ($game->request->wordstoavoid as $wta_image_id => $wta_image) {
        if (array_key_exists($wta_image_id, $tags)) {
          foreach ($wta_image as $wta_tag_id => $wta_tag) {
            if (array_key_exists($wta_tag["tag"], $tags[$wta_image_id])) {
              $tags[$wta_image_id][$wta_tag["tag"]]["type"] = 'wordstoavoid';
              $tags[$wta_image_id][$wta_tag["tag"]]["weight"] = 0;
            }
          }
        }
      }
    }
    return $tags;
  }

  function wordsToAvoid(&$wordsToAvoid, &$used_images, &$game, &$game_model, &$tags) {
    $model = new WordsToAvoid;
    $model->fbvLoad();
    $wordsToAvoid = MGTags::getTagsByWeightThreshold($used_images, $model->words_to_avoid_threshold);
  }
  
  function install() {
    $model = new WordsToAvoid;
    $model->fbvSave();
  }

}
