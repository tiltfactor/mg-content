<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class ZenTag extends MGGameModel implements MGGameModelInterface
{
  public $active = 0; //active will never be saved in the games FBVStorage settings it is just a handler for the Game database entry
  public $name = "Zen Tag";
  public $arcade_image = "zentag_arcade.png";
  public $description = "Clear your mind and you will hear the voice of the serene tagger within you. Ohm.";
  public $more_info_url = "";
	public $play_once_and_move_on = 0;
  public $play_once_and_move_on_url = "";
  public $turns = 4;
  public $score_new = 2;
  public $score_match = 1;
  public $score_expert = 3;
  public $image_width = 450;
  public $image_height = 450;
  public $words_to_avoid_threshold = 10;
  
  public function rules() {
    return array(
        array('name, description, arcade_image, active, play_once_and_move_on, score_new, score_match, score_expert, turns, words_to_avoid_threshold', 'required'),
        array('name', 'length', 'min'=>1, 'max'=>100),
        array('description', 'length', 'min'=>50, 'max'=>500),
        array('more_info_url, play_once_and_move_on_url','url'),
        array('image_width, image_height', 'numerical', 'min'=>50, 'max'=>1000),
        array('active, play_once_and_move_on', 'numerical', 'min'=>0, 'max'=>1),
        array('score_new, score_match, score_expert, turns, words_to_avoid_threshold', 'numerical', 'min'=>1, 'max'=>1000),
    );
  }
  
  public function attributeLabels() {
    return array(
      'name' => Yii::t('app', 'Name'),
      'arcade_image' => Yii::t('app', 'Name'),
      'description' => Yii::t('app', 'Description'),
      'play_once_and_move_on' => Yii::t('app', 'Play once and move on'),
      'play_once_and_move_on_url' => Yii::t('app', 'Play once/move on forward to URL'),
      'score_new' => Yii::t('app', 'Score (new)'),
      'score_match' => Yii::t('app', 'Score (match)'),
      'score_expert' => Yii::t('app', 'Score (expert)'),
      'image_width' => Yii::t('app', 'Stage Image max. Width'),
      'image_height' => Yii::t('app', 'Stage Image max. Height'),
      'turns' => Yii::t('app', 'Turns'),
      'words_to_avoid_threshold' => Yii::t('app', 'Words to avoid weight threshold'),
    );
  }
  
  public function fbvLoad() {
    $game_data = Yii::app()->fbvStorage->get("games." . $this->getGameID(), null);
    if (is_array($game_data)) {
      $this->name = $game_data["name"];
      $this->description = $game_data["description"];
      $this->arcade_image = $game_data["arcade_image"];
      $this->more_info_url = $game_data["more_info_url"];
      $this->play_once_and_move_on = (int)$game_data["play_once_and_move_on"];
      $this->play_once_and_move_on_url = (string)$game_data["play_once_and_move_on_url"];
      $this->turns = (int)$game_data["turns"];
      $this->score_new = (int)$game_data["score_new"];
      $this->score_match = (int)$game_data["score_match"];
      $this->score_expert = (int)$game_data["score_expert"];
      $this->image_width = (int)$game_data["image_width"];
      $this->image_height = (int)$game_data["image_height"];
      $this->words_to_avoid_threshold = (int)$game_data["words_to_avoid_threshold"];
    }
  }
  
  public function fbvSave() {
    $game_data = array(
      'name' => $this->name,
      'description' => $this->description,
      'arcade_image' => $this->arcade_image,
      'more_info_url' => $this->more_info_url,
      'play_once_and_move_on' => $this->play_once_and_move_on,
      'play_once_and_move_on_url' => $this->play_once_and_move_on_url,
      'turns' => $this->turns,
      'score_new' => $this->score_new,
      'score_match' => $this->score_match,
      'score_expert' => $this->score_expert,
      'image_width' => $this->image_width,
      'image_height' => $this->image_height,
      'words_to_avoid_threshold' => $this->words_to_avoid_threshold,
    );
    Yii::app()->fbvStorage->set("games." . $this->getGameID(), $game_data);
  }
  
  public function getGameID() {
    return __CLASS__;    
  }
}
