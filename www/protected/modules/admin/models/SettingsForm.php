<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class SettingsForm extends Game
{
  public $app_name = "Meta Data Games";  
  public $throttle_interval = 2500; //interval in miliseconds
  public $app_email = "sukie@tiltfaktor.org";
  public $pagination_size = 25;
  public $app_upload_path = "/../uploads";
  public $app_upload_url = "/uploads";
  
  public function rules() {
    return array(
        array('app_name, throttle_interval, app_email, pagination_size, app_upload_path, app_upload_url', 'required'),
        array('throttle_interval', 'numerical', 'integerOnly'=>true, 'min'=>500),
        array('pagination_size', 'numerical', 'integerOnly'=>true, 'min'=>10),
        array('app_email', 'email'),
    );
  }
  
  public function attributeLabels() {
    return array(
      'app_name' => Yii::t('app', 'Application Name'), // xxx make use of setting throughout the system
      'throttle_interval' => Yii::t('app', 'Throttle Interval (how many millisecond have to be between two api requests)'), // xxx make use of setting throughout the system
      'app_email' => Yii::t('app', 'E-Mail address (e-mails are send from and contact form messages are send to)'),
      'pagination_size' => Yii::t('app', 'Listings pagination size'), // xxx make use of setting throughout the system
      'app_upload_path' => Yii::t('app', 'Upload folder (relative path to application folder)'), // xxx make use of setting throughout the system
      'app_upload_url' => Yii::t('app', 'Upload folder URL'), // xxx make use of setting throughout the system
    );
  }
  
  public function fbvLoad() {
    $game_data = Yii::app()->fbvStorage->get("settings", null);
    if (is_array($game_data)) {
      $this->app_name = (isset($game_data["app_name"]))? $game_data["app_name"] : $this->app_name;
      $this->throttle_interval =(isset($game_data["throttle_interval"]))? $game_data["throttle_interval"] : $this->throttle_interval;
      $this->app_email = (isset($game_data["app_email"]))? $game_data["app_email"] : $this->app_email;
      $this->pagination_size = (isset($game_data["pagination_size"]))? $game_data["pagination_size"] : $this->pagination_size;
      $this->app_upload_path = (isset($game_data["app_upload_path"]))? $game_data["app_upload_path"] : $this->app_upload_path;
      $this->app_upload_url = (isset($game_data["app_upload_url"]))? $game_data["app_upload_url"] : $this->app_upload_url;
    }
  }
  
  public function fbvSave() {
    $settings = array(
      'app_name' => $this->app_name,
      'throttle_interval' => $this->throttle_interval,
      'app_email' => $this->app_email,
      'pagination_size' => $this->pagination_size,
      'app_upload_path' => $this->app_upload_path,
      'app_upload_url' => $this->app_upload_url,
    );
    return Yii::app()->fbvStorage->set("settings", $settings);
  }
  
  public function getGameID() {
    return __CLASS__;    
  }
}
