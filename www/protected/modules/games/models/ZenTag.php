<?php

/**
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
  public $image_width = 450;
  public $image_height = 450;
  
  public function rules() {
    return array(
        array('name, description, arcade_image, active, play_once_and_move_on, turns', 'required'),
        array('name', 'length', 'min'=>1, 'max'=>100),
        array('description', 'length', 'min'=>25, 'max'=>500),
        array('more_info_url, play_once_and_move_on_url','url'),
        array('image_width, image_height', 'numerical', 'min'=>50, 'max'=>1000),
        array('active, play_once_and_move_on', 'numerical', 'min'=>0, 'max'=>1),
        array('turns', 'numerical', 'min'=>1, 'max'=>1000),
    );
  }
  
  public function attributeLabels() {
    return array(
      'name' => Yii::t('app', 'Name'),
      'arcade_image' => Yii::t('app', 'Game Image Location'),
      'description' => Yii::t('app', 'Description'),
      'play_once_and_move_on' => Yii::t('app', 'Play Once and Move On'),
      'play_once_and_move_on_url' => Yii::t('app', 'Play Once/Move On Forward to URL'),
      'image_width' => Yii::t('app', 'Maximum Image Width'),
      'image_height' => Yii::t('app', 'Maximum Image Height'),
      'turns' => Yii::t('app', 'Turns'),
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
      $this->image_width = (int)$game_data["image_width"];
      $this->image_height = (int)$game_data["image_height"];
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
      'image_width' => $this->image_width,
      'image_height' => $this->image_height,
    );
    Yii::app()->fbvStorage->set("games." . $this->getGameID(), $game_data);
  }
  
  public function getGameID() {
    return __CLASS__;    
  }
}
