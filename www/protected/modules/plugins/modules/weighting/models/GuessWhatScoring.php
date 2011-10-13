<?php
/**
 */

Yii::import('application.modules.plugins.models.MGPluginModel');

class GuessWhatScoring extends MGPluginModel implements MGPluginModelInterface
{
  public $active = 0; //active will never be saved in the games FBVStorage settings it is just a handler for the Plugin database entry
  public $score_new = 2;
  public $score_match = 1;
  public $score_first_guess = 5;
  public $score_second_guess = 3;
  public $score_third_guess = 2;
  public $additional_weight_first_guess = 0.5;
  
  public function rules() {
    return array(
        array('score_new, score_match, score_first_guess, score_second_guess, score_third_guess, additional_weight_first_guess', 'required'),
        array('score_new, score_match, score_first_guess, score_second_guess, score_third_guess, additional_weight_first_guess', 'numerical', 'min'=>0, 'max'=>100000000),
    );
  }
  
  public function attributeLabels() {
    return array(
      'score_new' => Yii::t('app', 'Bonus desribing player submitted new tag for the image'),
      'score_match' => Yii::t('app', 'Bonus describing player submitted a matching tag for the image'),
      'score_first_guess' => Yii::t('app', 'Score for both player if image has been found on first guess'),
      'score_second_guess' => Yii::t('app', 'Score for both player if image has been found on second guess'),
      'score_third_guess' => Yii::t('app', 'Score for both player if image has been found on all other attempts'),
      'additional_weight_first_guess' => Yii::t('app', 'Additional weight for tag leading to hit on first guess.'),
    );
  }
  
  public function fbvLoad() {
    $plugin_data = Yii::app()->fbvStorage->get("plugins.weighting." . $this->getPluginID(), null);
    if (is_array($plugin_data)) {
      $this->score_new = (int)$plugin_data["score_new"];
      $this->score_match = (int)$plugin_data["score_match"];
      $this->score_first_guess = (int)$plugin_data["score_first_guess"];
      $this->score_second_guess = (int)$plugin_data["score_second_guess"];
      $this->score_third_guess = (int)$plugin_data["score_third_guess"];
      $this->additional_weight_first_guess = (int)$plugin_data["additional_weight_first_guess"];
    }
  }
  
  public function fbvSave() {
    $plugin_data = array(
      'score_new' => $this->score_new,
      'score_match' => $this->score_match,
      'score_first_guess' => $this->score_first_guess,
      'score_second_guess' => $this->score_second_guess,
      'score_third_guess' => $this->score_third_guess,
      'additional_weight_first_guess' => $this->additional_weight_first_guess,
    );
    Yii::app()->fbvStorage->set("plugins.weighting." . $this->getPluginID(), $plugin_data);
  }
  
  public function getPluginID() {
    return __CLASS__;    
  }
}
