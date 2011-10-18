<?php

class TagController extends GxController {
  public $defaultAction = 'admin';
  
  public function filters() {
  	return array(
      'IPBlock',
      'accessControl', 
      );
  }
  
  public function accessRules() {
  	return array(
  			array('allow',
  				'actions'=>array('view'),
  				'roles'=>array('*'),
  				),
  			array('allow', 
  				'actions'=>array('view', 'batch', 'admin', 'merge', 'update', 'delete'),
  				'roles'=>array('editor', 'dbmanager', 'admin', 'xxx'), // ammend after creation
  				),
  			array('deny', 
  				'users'=>array('*'),
  				),
  			);
  }

	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'Tag'),
		));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id, 'Tag');
    $model->modified = date('Y-m-d H:i:s');
		$this->performAjaxValidation($model, 'tag-form');
    
    $current_tag_name = $model->tag;
    
		if (isset($_POST['Tag'])) {
			$model->setAttributes($_POST['Tag']);
      
      $merge_to_tag = Tag::model()->findByAttributes(array("tag" => $model->tag), 'id <> :tagID', array(':tagID' => $model->id)); 
      if ($merge_to_tag) {
        $tagUseInfo = $model->tagUseInfo();
        $model->tag = $current_tag_name;
        
        $this->render('merge', array(
          'tag_from' => $model,
          'tag_to' => $merge_to_tag,
          'tag_use_count' => $tagUseInfo['use_count'],
        ));
      } else {
        
      
        if ($model->save()) {
          MGHelper::log('update', 'Updated Tag with ID(' . $id . ')');
          Flash::add('success', Yii::t('app', "Tag updated"));
          $this->redirect(array('view', 'id' => $model->id));
        }  
      }
		}

		$this->render('update', array(
				'model' => $model,
				));
	}
  
  public function actionMerge($from_id, $to_id) {
    $tag_from = Tag::model()->findByPk($from_id);
    $tag_to = Tag::model()->findByPk($to_id);
    
    if ($tag_from && $tag_to) {
      $tag_uses = TagUse::model()->findAllByAttributes(array('tag_id' => $tag_from->id));
      
      if ($tag_uses) {
        
        foreach ($tag_uses as $tag_use) {
          $tag_ov = new TagOriginalVersion;
          $tag_ov->original_tag = $tag_from->tag;
          $tag_ov->tag_use_id = $tag_use->id;
          $tag_ov->comments = "rename '{$tag_from->tag}' to '{$tag_to->tag}'";
          $tag_ov->user_id = Yii::app()->user->id;
          $tag_ov->created = date('Y-m-d H:i:s');
          
          if (!$tag_ov->save())
            throw new CHttpException(500, Yii::t('app', 'Internal Server Error.'));
          
          $tag_use->tag_id = $tag_to->id;
          if (!$tag_use->save())
            throw new CHttpException(500, Yii::t('app', 'Internal Server Error.'));

        }
        
        MGHelper::log('delete', "Merged tag from '{$tag_from->tag}' to '{$tag_to->tag}'");
        Flash::add('success', Yii::t('app', "Tag '{$tag_from->tag}' merged with '{$tag_to->tag}'"));
        $this->redirect(array('view', 'id' => $to_id));
        
      } else
        throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    } else
      throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    
  }
  
	public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$model = $this->loadModel($id, 'Tag');
			if ($model->hasAttribute("locked") && $model->locked) {
			  throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
			} else {
			  $model->delete();
			  MGHelper::log('delete', 'Deleted Tag with ID(' . $id . ')');
        
        Flash::add('success', Yii::t('app', "Tag deleted"));

			  if (!Yii::app()->getRequest()->getIsAjaxRequest())
				  $this->redirect(array('admin'));
		  }
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionAdmin() {
	  $this->layout = '//layouts/column1';
     
		$model = new Tag('search');
		$model->unsetAttributes();

		if (isset($_GET['Tag']))
			$model->setAttributes($_GET['Tag']);

		$this->render('admin', array(
			'model' => $model,
		));
	}
  
  
  public function actionBatch($op) {
    if (Yii::app()->getRequest()->getIsPostRequest()) {
      switch ($op) {
        case "delete":
          $this->_batchDelete();
          break;
      }
      if (!Yii::app()->getRequest()->getIsAjaxRequest())
        $this->redirect(array('admin'));
    } else
      throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));  
    
  }

  private function _batchDelete() {
    if (isset($_POST['tag-ids'])) {
      $criteria=new CDbCriteria;
      $criteria->addInCondition("id", $_POST['tag-ids']);
            
      MGHelper::log('batch-delete', 'Batch deleted Tag with IDs(' . implode(',', $_POST['tag-ids']) . ')');
        
      $model = new Tag;
      $model->deleteAll($criteria);
        
    } 
  }
}