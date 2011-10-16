<?php

Yii::import('application.modules.plugins.modules.dictionary.models._base.BaseStopWord');

class StopWord extends BaseStopWord
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
  
  public static function getStopWordList() {
    static $stop_words;
    
    if (!is_array($stop_words)) {
      $stop_words = Yii::app()->db->createCommand()
                    ->select('s.id, s.word')
                    ->from('{{stop_word}} s')
                    ->queryAll();
    }
    return $stop_words;
  }
}