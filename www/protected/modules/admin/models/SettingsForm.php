<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class SettingsForm extends Game
{
  public $app_name = "Meta Data Games";  
  public $throttle_interval = 500; //interval in miliseconds
  public $message_queue_interval = 500; // interval in miliseconds can be smaller than throttle_interval
  public $app_email = "sukie@tiltfaktor.org";
  public $pagination_size = 25;
  public $app_upload_path = "/../uploads";
  public $app_upload_url = "/uploads";
  
  public function rules() {
    return array(
        array('app_name, throttle_interval, message_queue_interval, app_email, pagination_size, app_upload_path, app_upload_url', 'required'),
        array('throttle_interval', 'numerical', 'integerOnly'=>true, 'min'=>500),
        array('message_queue_interval', 'numerical', 'integerOnly'=>true, 'min'=>100),
        array('pagination_size', 'numerical', 'integerOnly'=>true, 'min'=>10),
        array('app_email', 'email'),
    );
  }
  
  public function attributeLabels() {
    return array(
      'app_name' => Yii::t('app', 'Application Name'), 
      'throttle_interval' => Yii::t('app', 'Throttle Interval <br />(how many milliseconds have to be between two API requests)'), 
      'message_queue_interval' => Yii::t('app', 'Message Queue Interval <br />(how many milliseconds have to be between two message queue requests)<br />Message queue requests are not throttled.'), 
      'app_email' => Yii::t('app', 'E-Mail Address <br />(e-mails are sent from and contact form messages are sent to)'),
      'pagination_size' => Yii::t('app', 'Listings Pagination Size'), 
      'app_upload_path' => Yii::t('app', 'Upload Folder <br />(relative path to application folder)'), 
      'app_upload_url' => Yii::t('app', 'Upload Folder URL'), 
    );
  }
  
  public function fbvLoad() {
    $game_data = Yii::app()->fbvStorage->get("settings", null);
    if (is_array($game_data)) {
      $this->app_name = (isset($game_data["app_name"]))? $game_data["app_name"] : $this->app_name;
      $this->throttle_interval =(isset($game_data["throttle_interval"]))? $game_data["throttle_interval"] : $this->throttle_interval;
      $this->message_queue_interval =(isset($game_data["message_queue_interval"]))? $game_data["message_queue_interval"] : $this->message_queue_interval;
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
      'message_queue_interval' => $this->message_queue_interval,
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
