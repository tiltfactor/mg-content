<?php

Yii::import('application.models._base.BaseTag');

class Tag extends BaseTag
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
  
  public function searchUserTags($user_id) {
    $tags = Yii::app()->db->createCommand()
                  ->select('count(t.id) as counted, t.id, t.tag')
                  ->from('{{session}} s')
                  ->join('{{game_submission}} gs', 'gs.session_id=s.id')
                  ->join('{{tag_use}} tu', 'tu.game_submission_id = gs.id')
                  ->join('{{tag}} t', 'tu.tag_id = t.id')
                  ->where('s.user_id=:userID', array(":userID" => $user_id))
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