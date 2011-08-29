<?php

class MGGame extends CComponent {
  public $two_player_game = false;
  
  /**
   * @param array Array of image id's that have been used.
   */
  public function saveSubmission($game, &$game_model) {
    $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
    if (isset($game->submissions) && is_array($game->submissions)&& count($game->submissions) > 0) {
      $game_submission = new GameSubmission;
      $game_submission->submission = json_encode($game->submissions);
      $game_submission->session_id = (int)Yii::app()->session[$api_id .'_SESSION_ID'];
      $game_submission->played_game_id = $game->played_game_id;
      $game_submission->created = date('Y-m-d H:i:s'); 
    
      if ($game_submission->validate()) {
        $game_submission->save();
        return $game_submission->id;
      } else {
        throw new CHttpException(500, Yii::t('app', 'Internal Server Error.'));
      }
    }
    return null;
  }
  
  protected function getImageSets($game, &$game_model) {
    $imageSets = array();
    
    foreach ($game_model->imageSets as $imageSet) {
      //xxx here comes the image set access filter magic
      $imageSets[] = $imageSet->id;
    }
    
    return $imageSets;
  }
  
  protected function getImages($imageSets, $game, &$game_model) {
    
    // for performance reasons we use a direct db query to load the images.
    // no need to load the models
    // xxx test distinct xxx rewrite to use proper InCondition like tags
    $images = Yii::app()->db->createCommand()
                ->select('id, name', 'distinct')
                ->from('{{image_set_to_image}} is2i')
                ->join('{{image}} i', 'i.id=is2i.image_id')
                ->where('is2i.image_set_id in (:ids)', array(':ids'=>implode(",", $imageSets)))
                ->queryAll();
    
    //xxx  make something with it.
    $used_images = $this->getUsedImages($game, $game_model);
    
    return $images;
    
    // make something with it.
    
    //xxx $this->saveUsedImages
  } 
 
  /**
   * 
   */
  protected function getUsedImages($game, &$game_model) {
    
    $used_images = array();
    $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
    if (!isset(Yii::app()->session[$api_id .'_GAMES_USED_IMAGES'])) {
      Yii::app()->session[$api_id .'_GAMES_USED_IMAGES'] = array();
    } else {
      $arr_img = Yii::app()->session[$api_id .'_GAMES_USED_IMAGES'];
      if (!array_key_exists($game->gid, $arr_img)) {
        $arr_img[$game->gid] = array();
        Yii::app()->session[$api_id .'_GAMES_USED_IMAGES'] = $arr_img;
      } else {
        $used_images["session"] = $arr_img[$game->gid];
      }
    }
          
    // we could add a data base driven version xxx (a.k.a) retrieve the images the user has tagged and add them to the list
    return $used_images;    
  }
  
  /**
   * @param array Array of image id's that have been used.
   */
  protected function setUsedImages($usedImages, $game, &$game_model) {
    $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
    
    $arr_img = array();
    if (!isset(Yii::app()->session[$api_id .'_GAMES_USED_IMAGES'])) {
      Yii::app()->session[$api_id .'_GAMES_USED_IMAGES'] = $arr_img;
    } else {
      $arr_img = Yii::app()->session[$api_id .'_GAMES_USED_IMAGES'];
      if (!array_key_exists($game->gid, $arr_img)) {
        $arr_img[$game->gid] = array();
      }
    }
    $arr_img = array_merge($arr_img[$game->gid], $usedImages);
    
    Yii::app()->session[$api_id .'_GAMES_USED_IMAGES'] = $arr_img;
  }
}

/**
 * Interface for Game Logic
 * @abstract
 */
interface MGGameInterface
{
  public function validateSubmission($game, &$game_model);
  public function getTags($game, &$game_model);
  public function setWeights($game, &$game_model, $tags);
  public function getTurn($game, &$game_model);
  public function getScore($game, &$game_model, $tags);
}
