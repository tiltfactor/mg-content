<?php

Yii::import('application.models._base.BasePlugin');

class Plugin extends BasePlugin
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
  
  public function attributeLabels() {
    return array(
      'id' => Yii::t('app', 'ID'),
      'type' => Yii::t('app', 'Type'),
      'active' => Yii::t('app', 'Active'),
      'unique_id' => Yii::t('app', 'Unique Name'),
      'created' => Yii::t('app', 'Created'),
      'modified' => Yii::t('app', 'Modified'),
    );
  }
  
  public function search() {
    $criteria = new CDbCriteria;

    $criteria->compare('type', $this->type, true);
    $criteria->compare('active', $this->active);
    $criteria->compare('unique_id', $this->unique_id, true);
    $criteria->compare('created', $this->created, true);
    $criteria->compare('modified', $this->modified, true);

    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination'=>array(
        'pageSize'=>Yii::app()->fbvStorage->get("settings.pagination_size"),
      ),
    ));
  }
  
  public static function listActivePluginsForGames() {
    $criteria = new CDbCriteria;

    $criteria->compare('active', 1);
    $criteria->addInCondition('type', array('dictionary', 'weighting'));
    return Plugin::model()->findAllAttributes(array('unique_id'), true, $criteria);
  }

  public static function getGamesUsingPlugin($id) {
    $games = Yii::app()->db->createCommand()
                  ->select('g.unique_id')
                  ->from('{{game}} g')
                  ->join('{{game_to_plugin}} gp', 'gp.game_id=g.id')
                  ->where(array('and', 'gp.plugin_id = :pluginID'), array(":pluginID" => $id))
                  ->order('unique_id')
                  ->queryAll();
        
    if ($games) {
      $out = array();
      foreach ($games as $game) {
        if (Yii::app()->user->checkAccess('dbmanager')) {
          $out[] = CHtml::link($game["unique_id"], array("/games/" . $game["unique_id"] . "/view")); 
        } else {
          $out[] = $game["unique_id"];
        }
      }
      return implode(", ", $out);
    } else {
      return Yii::t('app', 'none'); 
    }
  }
}