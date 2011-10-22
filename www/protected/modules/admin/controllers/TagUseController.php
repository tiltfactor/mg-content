<?php

class TagUseController extends GxController {
  public $defaultAction = 'admin';
  
  public function filters() {
  	return array(
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
  				'actions'=>array('view', 'batch', 'update', 'weight', 'admin'),
  				'roles'=>array('editor', 'dbmanager', 'admin', 'xxx'), // ammend after creation
  				),
  			array('deny', 
  				'users'=>array('*'),
  				),
  			);
  }

	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'TagUse'),
		));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id, 'TagUse');
    		$this->performAjaxValidation($model, 'tag-use-form');

		if (isset($_POST['TagUse'])) {
			$model->setAttributes($_POST['TagUse']);

			if ($model->save()) {
        MGHelper::log('update', 'Updated TagUse with ID(' . $id . ')');
        Flash::add('success', Yii::t('app', "TagUse updated"));
				$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('update', array(
				'model' => $model,
				));
	}
  
  public function actionWeight($id) {
    $formModel = new TagUseReWeightForm;
    $tagUseModel = $this->loadModel($id, 'TagUse');
    if ($tagUseModel) {
      $tagModel = $this->loadModel($tagUseModel->tag_id, 'Tag');
      
      $this->performAjaxValidation($formModel, 'tag-use-reweight-form');
      
      if (isset($_POST['TagUseReWeightForm'])) {
        $formModel->setAttributes($_POST['TagUseReWeightForm']);
        if ($formModel->validate()) {
          switch ($formModel->applyTo) {
            case 1:
              $tagUseModel->weight = $formModel->weight;
              $tagUseModel->save();
              MGHelper::log('tag use re-weight', 'Updated TagUse with ID(' . $id . ')');
              Flash::add('success', Yii::t('app', "TagUse weight changed updated"));
              $this->redirect(array('view', 'id' => $tagUseModel->id));  
              break;
              
            case 2:
              $submittingUser = $tagUseModel->getSubmittingUser();
              if ($submittingUser) {
                $tagUseModel->updateWeightWithTagAndUser($formModel->weight, $tagModel->id, $submittingUser['id']);
              } else {
                $tagUseModel->updateWeightWithTagForGuests($formModel->weight, $tagModel->id);
              }
              MGHelper::log('tag use re-weight', 'Re-weighted tag uses of tag with ID(' . $id . ')');
              Flash::add('success', Yii::t('app', "Re-weighted tag's tag uses"));
              $this->redirect(array('view', 'id' => $tagUseModel->id));
              break;
              
            case 3:
              $tagUseModel->updateWeightWithTag($formModel->weight, $tagModel->id);
              MGHelper::log('tag use re-weight', 'Re-weighted tag uses of tag with ID(' . $id . ')');
              Flash::add('success', Yii::t('app', "Re-weighted tag's tag uses"));
              $this->redirect(array('view', 'id' => $tagUseModel->id));
              break;
          }  
        }
      } else {
        $formModel->weight = $tagUseModel->weight;
      }
      
      $choices = array();
      $choices[1] = Yii::t('app', 'the current tag use');
      $choices[2] = Yii::t('app', 'all the tag uses of tag') . ' "<b>' . $tagModel->tag . '</b>" ' .  Yii::t('app', 'that have been submitted by "' . $tagUseModel->getUserName() . '"'); 
      $choices[3] = Yii::t('app', 'all the tag uses of tag') . ' "<b>' . $tagModel->tag . '</b>" ' . Yii::t('app', 'submitted by all players and guests');
      
      $this->render('weight', array(
          'tagUseModel' => $tagModel,
          'tagModel' => $tagModel,
          'formModel' => $formModel,
          'choices' => $choices,
          ));
    } else
      throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));  
    
  }
  
	public function actionAdmin() {
	  $this->layout = '//layouts/column1';
    
		$model = new TagUse('search');
		$model->unsetAttributes();

		if (isset($_GET['TagUse']))
			$model->setAttributes($_GET['TagUse']);

		$this->render('admin', array(
			'model' => $model,
		));
	}
}