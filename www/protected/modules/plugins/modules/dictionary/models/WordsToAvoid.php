<?php
/**
 */

Yii::import('application.modules.plugins.models.MGPluginModel');

class WordsToAvoid extends MGPluginModel implements MGPluginModelInterface
{
  public $active = 0; //active will never be saved in the games FBVStorage settings it is just a handler for the Game database entry
  public $words_to_avoid_threshold = 10;
  
  public function rules() {
    return array(
        array('words_to_avoid_threshold', 'required'),
        array('words_to_avoid_threshold', 'numerical', 'min'=>1, 'max'=>1000),
    );
  }
  
  public function attributeLabels() {
    return array(
      'words_to_avoid_threshold' => Yii::t('app', 'Words to avoid weight threshold'),
    );
  }
  
  public function fbvLoad() {
    $plugin_data = Yii::app()->fbvStorage->get("plugins.dictionary." . $this->getPluginID(), null);
    if (is_array($plugin_data)) {
      $this->words_to_avoid_threshold = (int)$plugin_data["words_to_avoid_threshold"];
    }
  }
  
  public function fbvSave() {
    $plugin_data = array(
      'words_to_avoid_threshold' => $this->words_to_avoid_threshold,
    );
    Yii::app()->fbvStorage->set("plugins.dictionary." . $this->getPluginID(), $plugin_data);
  }
  
  public function getPluginID() {
    return __CLASS__;    
  }
}
