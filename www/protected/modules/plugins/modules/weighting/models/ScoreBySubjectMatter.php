<?php
/**
 */

Yii::import('application.modules.plugins.models.MGPluginModel');

class ScoreBySubjectMatter extends MGPluginModel implements MGPluginModelInterface
{
  public $active = 0; //active will never be saved in the games FBVStorage settings it is just a handler for the Plugin database entry
  public $score_new = 2;
  public $score_match = 1;
  public $score_new_expert = 4;
  public $score_new_trusted = 4;
  public $score_match_expert = 3;
  public $score_match_trusted = 3;
  
  public function rules() {
    return array(
        array('score_new, score_match, score_new_expert, score_new_trusted, score_match_expert, score_match_trusted', 'required'),
        array('score_new, score_match, score_new_expert, score_new_trusted, score_match_expert, score_match_trusted', 'numerical', 'min'=>0, 'max'=>100000000),
    );
  }
  
  public function attributeLabels() {
    return array(
      'score_new' => Yii::t('app', 'Score (new tag)'),
      'score_match' => Yii::t('app', 'Score (matched tag)'),
      'score_new_expert' => Yii::t('app', 'Score (new tag, expert)'),
      'score_new_trusted' => Yii::t('app', 'Score (new tag, trusted)'),
      'score_match_expert' => Yii::t('app', 'Score (matched tag, expert)'),
      'score_match_trusted' => Yii::t('app', 'Score (matched tag, trusted)'),
    );
  }
  
  /*
   * loads values from the settings file using the FBVStorage compontent
   */  
  public function fbvLoad() {
    $plugin_data = Yii::app()->fbvStorage->get("plugins.weighting." . $this->getPluginID(), null);
    if (is_array($plugin_data)) {
      $this->score_new = (int)$plugin_data["score_new"];
      $this->score_match = (int)$plugin_data["score_match"];
      $this->score_new_expert = (int)$plugin_data["score_new_expert"];
      $this->score_new_trusted = (int)$plugin_data["score_new_trusted"];
      $this->score_match_expert = (int)$plugin_data["score_match_expert"];
      $this->score_match_trusted = (int)$plugin_data["score_match_trusted"];
    }
  }
  
  /*
   * saves values from to settings file using the FBVStorage compontent
   */  
  public function fbvSave() {
    $plugin_data = array(
      'score_new' => $this->score_new,
      'score_match' => $this->score_match,
      'score_new_expert' => $this->score_new_expert,
      'score_new_trusted' => $this->score_new_trusted,
      'score_match_expert' => $this->score_match_expert,
      'score_match_trusted' => $this->score_match_trusted,
    );
    Yii::app()->fbvStorage->set("plugins.weighting." . $this->getPluginID(), $plugin_data);
  }
  
  public function getPluginID() {
    return __CLASS__;    
  }
}
