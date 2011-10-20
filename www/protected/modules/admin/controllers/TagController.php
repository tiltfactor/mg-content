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
  				'actions'=>array('view', 'batch', 'admin', 'merge', 'ban', 'weight', 'update', 'delete'),
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


  public function actionBan($id) {
    $model = $this->loadModel($id, 'Tag');
    
    if ($model) {
      
      if (isset($_GET['banTag']) && (int)$_GET['banTag'] === 1) {
        $this->_banTag($id);
        MGHelper::log('tag-banned', 'Banned Tag with ID(' . $id . ')');
        Flash::add('success', Yii::t('app', "Tag banned"));
        $this->redirect(array('admin'));
      } else {
        $tagUseInfo = $model->tagUseInfo();
        $this->render('ban', array(
          'model' => $model,
          'tag_use_count' => $tagUseInfo['use_count'],
        ));
      } 
      
    } else 
      throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
  }
    
  public function actionWeight($id) {
    $formModel = new TagReWeightForm;
    $tagModel = $this->loadModel($id, 'Tag');
    $tagUseModel = new TagUse;    
        
    $this->performAjaxValidation($formModel, 'tag-reweight-form');
    
    if (isset($_POST['TagReWeightForm'])) {
      $formModel->setAttributes($_POST['TagReWeightForm']);
      if ($formModel->validate()) {
        switch ($formModel->applyTo) {
          case 2:
            $user_id = (int)$formModel->user_id;
            if ($user_id !== 0) {
              if ($user_id === -1) {
                $tagUseModel->updateWeightWithTagForGuests($formModel->weight, $tagModel->id);
              } else {
                $tagUseModel->updateWeightWithTagAndUser($formModel->weight, $tagModel->id, $user_id);
              }
              MGHelper::log('tag re-weight', 'Re-weighted tag uses of tag with ID(' . $id . ')');
              Flash::add('success', Yii::t('app', "Re-weighted tag's tag uses"));
              $this->redirect(array('view', 'id' => $tagModel->id));
            } else {
              $formModel->addError('user_id', Yii::t('add', 'Please select the submitting player'));
            }
            break;
            
          case 3:
            $tagUseModel->updateWeightWithTag($formModel->weight, $tagModel->id);
            MGHelper::log('tag re-weight', 'Re-weighted tag uses of tag with ID(' . $id . ')');
            Flash::add('success', Yii::t('app', "Re-weighted tag's tag uses"));
            $this->redirect(array('view', 'id' => $tagModel->id));
            break;
        }  
      }
    }
    
    $choices = array();
    $choices[2] = Yii::t('app', 'all the tag uses of tag') . ' "<b>' . $tagModel->tag . '</b>" ' .  Yii::t('app', 'that have been submitted by guest(s) or player specified above'); 
    $choices[3] = Yii::t('app', 'all the tag uses of tag') . ' "<b>' . $tagModel->tag . '</b>" ' . Yii::t('app', 'submitted by all players and guests');
    
    $users = array();
    $users[-1] = Yii::t('app', 'Guest(s)');
    $submittingUsers = $tagUseModel->getSubmittingUsers($id);
    if ($submittingUsers) {
      foreach ($submittingUsers as $user) {
        $users[$user['id']] = $user['username'];
      }
    }
    
    $this->render('weight', array(
        'tagModel' => $tagModel,
        'formModel' => $formModel,
        'choices' => $choices,
        'users' => $users,
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
        
        MGHelper::log('tag-merged', "Merged tag from '{$tag_from->tag}' to '{$tag_to->tag}'");
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
  
  private function _banTag($tag_id) {
    $model = $this->loadModel($tag_id, 'Tag');
    if ($model) {
      TagUse::model()->banTag($tag_id);
      
      $plugins = PluginsModule::getActivePlugins("dictionary");
      
      if (count($plugins) > 0) {
        $success = true;
        foreach ($plugins as $plugin) {
          if (method_exists($plugin->component, "add")) {
            $success = ($success  && $plugin->component->add($model->tag, 'banned'));
          }
        }
      }
    }
  }
}