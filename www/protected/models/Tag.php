<?php

Yii::import('application.models._base.BaseTag');

class Tag extends BaseTag
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
  
  public function search() {
    $criteria = new CDbCriteria;
    $criteria->alias = "t";
    $criteria->distinct = true;
    $criteria->join = "LEFT JOIN {{tag_use}} tu ON tu.tag_id=t.id";
    $criteria->condition = 'tu.weight >= 1';
    $criteria->compare('t.id', $this->id);
    $criteria->compare('t.tag', $this->tag, true);
    $criteria->compare('t.created', $this->created, true);
    $criteria->compare('t.modified', $this->modified, true);
    
    return new CActiveDataProvider($this, array(
      'criteria' => $criteria,
      'pagination'=>array(
        'pageSize'=>Yii::app()->fbvStorage->get("settings.pagination_size"),
      ),
    ));
  }
  
  public function searchUserTags($user_id) {
    $tags = Yii::app()->db->createCommand()
                  ->select('count(t.id) as counted, t.id, t.tag')
                  ->from('{{session}} s')
                  ->join('{{game_submission}} gs', 'gs.session_id=s.id')
                  ->join('{{tag_use}} tu', 'tu.game_submission_id = gs.id')
                  ->join('{{tag}} t', 'tu.tag_id = t.id')
                  ->where(array('and', 'tu.weight >= 1', 's.user_id=:userID'), array(":userID" => $user_id))
                  ->group('t.id, t.tag')
                  ->order('counted DESC')
                  ->queryAll();
    
    return  new CArrayDataProvider($tags, array(
      'id'=>'id',
      'sort'=>array(
          'attributes'=>array(
               'id', 'tag', 'counted',
          ),
      ),
      'pagination'=>array(
          'pageSize'=> Yii::app()->fbvStorage->get("settings.pagination_size") * 2,
       ),
    ));
  }
}