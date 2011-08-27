<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class ZenPondForm extends MGGameForm implements MGGameFormInterface
{
  public $active = 0; //active will never be saved in the games FBVStorage settings it is just a handler for the Game database entry
  public $name = "Zen Pond";
  public $description = "This is a short description of Zen Pond";
  public $more_info_url = "";
	public $play_once_and_move_on = 0;
  public $turns = 5;
  public $score_new = 2;
  public $score_match = 1;
  public $score_expert = 3;
  
  public function rules() {
    return array(
        array('name, description, active, play_once_and_move_on, score_new, score_match, score_expert, turns', 'required'),
        array('name', 'length', 'min'=>1, 'max'=>100),
        array('description', 'length', 'min'=>50, 'max'=>500),
        array('more_info_url','url'),
        array('active, play_once_and_move_on', 'numerical', 'min'=>0, 'max'=>1),
        array('score_new, score_match, score_expert, turns', 'numerical', 'min'=>1, 'max'=>1000),
    );
  }
  
  public function attributeLabels() {
    return array(
      'name' => Yii::t('app', 'Name'),
      'description' => Yii::t('app', 'Description'),
      'play_once_and_move_on' => Yii::t('app', 'Play once and move on'),
      'score_new' => Yii::t('app', 'Score (new)'),
      'score_match' => Yii::t('app', 'Score (match)'),
      'score_expert' => Yii::t('app', 'Score (expert)'),
      'turns' => Yii::t('app', 'Turns'),
    );
  }
  
  public function load() {
    $game_data = Yii::app()->fbvStorage->get("games." . $this->getGameID(), null);
    if (is_array($game_data)) {
      $this->name = $game_data["name"];
      $this->description = $game_data["description"];
      $this->more_info_url = $game_data["more_info_url"];
      $this->play_once_and_move_on = (int)$game_data["play_once_and_move_on"];
      $this->turns = (int)$game_data["turns"];
      $this->score_new = (int)$game_data["score_new"];
      $this->score_match = (int)$game_data["score_match"];
      $this->score_expert = (int)$game_data["score_expert"];
    }
  }
  
  public function save() {
    $game_data = array(
      'name' => $this->name,
      'description' => $this->description,
      'more_info_url' => $this->more_info_url,
      'play_once_and_move_on' => $this->play_once_and_move_on,
      'turns' => $this->turns,
      'score_new' => $this->score_new,
      'score_match' => $this->score_match,
      'score_expert' => $this->score_expert,
    );
    Yii::app()->fbvStorage->set("games." . $this->getGameID(), $game_data);
  }
  
  public function getGameID() {
    return str_replace("Form", "", __CLASS__);    
  }
}
