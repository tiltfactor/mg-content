<?php

/**
 */
class ZenPond extends MGGameModel implements MGGameModelInterface
{
  public $active = 0; //active will never be saved in the games FBVStorage settings it is just a handler for the Game database entry
  public $name = "Zen Pond";
  public $arcade_image = "zenpond_arcade.png";
  public $description = "Clear your mind and you will hear the voice of the serene tagger within you. Ohm.";
  public $more_info_url = "";
  public $turns = 4;
  public $image_width = 450;
  public $image_height = 450;
  public $partner_wait_threshold = 20; // how many seconds should the system wait to look for a partner
  public $play_against_computer = 1; // if true the system will simulate a human player
  
  public function rules() {
    return array(
        array('name, description, arcade_image, active, turns', 'required'),
        array('name', 'length', 'min'=>1, 'max'=>100),
        array('description', 'length', 'min'=>50, 'max'=>500),
        array('more_info_url','url'),
        array('image_width, image_height', 'numerical', 'min'=>50, 'max'=>1000),
        array('active', 'numerical', 'min'=>0, 'max'=>1),
        array('play_against_computer', 'numerical', 'min'=>0, 'max'=>1),
        array('turns, partner_wait_threshold', 'numerical', 'min'=>1, 'max'=>1000),
    );
  }
  
  public function attributeLabels() {
    return array(
      'name' => Yii::t('app', 'Name'),
      'arcade_image' => Yii::t('app', 'Game Image Location'),
      'description' => Yii::t('app', 'Description'),
      'image_width' => Yii::t('app', 'Stage Image max. Width'),
      'image_height' => Yii::t('app', 'Stage Image max. Height'),
      'turns' => Yii::t('app', 'Turns'),
      'partner_wait_threshold' => Yii::t('app', 'Partner Search Time Frame (seconds)'),
      'play_against_computer' => Yii::t('app', 'Enable play against computer mode'),
    );
  }
  
  public function fbvLoad() {
    $game_data = Yii::app()->fbvStorage->get("games." . $this->getGameID(), null);
    if (is_array($game_data)) {
      $this->name = $game_data["name"];
      $this->description = $game_data["description"];
      $this->arcade_image = $game_data["arcade_image"];
      $this->more_info_url = $game_data["more_info_url"];
      $this->turns = (int)$game_data["turns"];
      $this->image_width = (int)$game_data["image_width"];
      $this->image_height = (int)$game_data["image_height"];
      $this->partner_wait_threshold = (int)$game_data["partner_wait_threshold"];
      $this->play_against_computer = (int)$game_data["play_against_computer"];
    }
  }
  
  public function fbvSave() {
    $game_data = array(
      'name' => $this->name,
      'description' => $this->description,
      'arcade_image' => $this->arcade_image,
      'more_info_url' => $this->more_info_url,
      'turns' => $this->turns,
      'image_width' => $this->image_width,
      'image_height' => $this->image_height,
      'partner_wait_threshold' => $this->partner_wait_threshold,
      'play_against_computer' => $this->play_against_computer,
    );
    
    Yii::app()->fbvStorage->set("games." . $this->getGameID(), $game_data);
  }
  
  public function getGameID() {
    return __CLASS__;    
  }
}
