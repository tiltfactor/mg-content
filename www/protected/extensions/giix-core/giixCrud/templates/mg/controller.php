<?php
/**
 * This is the template for generating a controller class file for CRUD feature.
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php echo "<?php\n"; ?>

class <?php echo $this->controllerClass; ?> extends <?php echo $this->baseControllerClass; ?> {

<?php 
	$authpath = 'ext.giix-core.giixCrud.templates.mg.auth.';
	Yii::app()->controller->renderPartial($authpath . $this->authtype);
?>

	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, '<?php echo $this->modelClass; ?>'),
		));
	}

	public function actionCreate() {
		$model = new <?php echo $this->modelClass; ?>;
		<?php echo ($this->tableSchema->getColumn("created") !== null)? "\$model->created = date('Y-m-d H:i:s');" : ""; ?> 
    <?php echo ($this->tableSchema->getColumn("modified") !== null)? "\$model->modified = date('Y-m-d H:i:s');" : ""; ?> 
    
<?php if ($this->enable_ajax_validation): ?>
		$this->performAjaxValidation($model, '<?php echo $this->class2id($this->modelClass)?>-form');
<?php endif; ?>

		if (isset($_POST['<?php echo $this->modelClass; ?>'])) {
			$model->setAttributes($_POST['<?php echo $this->modelClass; ?>']);
<?php if ($this->hasManyManyRelation($this->modelClass)): ?>
			$relatedData = <?php echo $this->generateGetPostRelatedData($this->modelClass, 4); ?>;
<?php endif; ?>

<?php if ($this->hasManyManyRelation($this->modelClass)): ?>
			if ($model->saveWithRelated($relatedData)) {
<?php else: ?>
			if ($model->save()) {
<?php endif; ?>
        MGHelper::log('create', 'Created <?php echo $this->modelClass; ?> with ID(' . $model-><?php echo $this->tableSchema->primaryKey; ?> . ')');
				Flash::add('success', Yii::t('app', "<?php echo $this->modelClass; ?> created"));
        if (Yii::app()->getRequest()->getIsAjaxRequest())
					Yii::app()->end();
				else 
				  $this->redirect(array('view', 'id' => $model-><?php echo $this->tableSchema->primaryKey; ?>));
			}
		}

		$this->render('create', array( 'model' => $model));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id, '<?php echo $this->modelClass; ?>');
    <?php echo ($this->tableSchema->getColumn("modified") !== null)? "\$model->modified = date('Y-m-d H:i:s');\n" : ""; ?>
<?php if ($this->enable_ajax_validation): ?>
		$this->performAjaxValidation($model, '<?php echo $this->class2id($this->modelClass)?>-form');
<?php endif; ?>

		if (isset($_POST['<?php echo $this->modelClass; ?>'])) {
			$model->setAttributes($_POST['<?php echo $this->modelClass; ?>']);
<?php if ($this->hasManyManyRelation($this->modelClass)): ?>
			$relatedData = <?php echo $this->generateGetPostRelatedData($this->modelClass, 4); ?>;
<?php endif; ?>

<?php if ($this->hasManyManyRelation($this->modelClass)): ?>
			if ($model->saveWithRelated($relatedData)) {
<?php else: ?>
			if ($model->save()) {
<?php endif; ?>
        MGHelper::log('update', 'Updated <?php echo $this->modelClass; ?> with ID(' . $id . ')');
        Flash::add('success', Yii::t('app', "<?php echo $this->modelClass; ?> updated"));
				$this->redirect(array('view', 'id' => $model-><?php echo $this->tableSchema->primaryKey; ?>));
			}
		}

		$this->render('update', array(
				'model' => $model,
				));
	}

	public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$model = $this->loadModel($id, '<?php echo $this->modelClass; ?>');
			if ($model->hasAttribute("locked") && $model->locked) {
			  throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
			} else {
			  $model->delete();
			  MGHelper::log('delete', 'Deleted <?php echo $this->modelClass; ?> with ID(' . $id . ')');
        
        Flash::add('success', Yii::t('app', "<?php echo $this->modelClass; ?> deleted"));

			  if (!Yii::app()->getRequest()->getIsAjaxRequest())
				  $this->redirect(array('admin'));
		  }
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$model = new <?php echo $this->modelClass; ?>('search');
    $model->unsetAttributes();

    if (isset($_GET['<?php echo $this->modelClass; ?>']))
      $model->setAttributes($_GET['<?php echo $this->modelClass; ?>']);

    $this->render('admin', array(
      'model' => $model,
    ));
	}

	public function actionAdmin() {
		$model = new <?php echo $this->modelClass; ?>('search');
		$model->unsetAttributes();

		if (isset($_GET['<?php echo $this->modelClass; ?>']))
			$model->setAttributes($_GET['<?php echo $this->modelClass; ?>']);

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
    if (isset($_POST['<?php echo $this->class2id($this->modelClass)?>-ids'])) {
      $criteria=new CDbCriteria;
      $criteria->addInCondition("id", $_POST['<?php echo $this->class2id($this->modelClass)?>-ids']);
      <?php echo ($this->tableSchema->getColumn("locked") !== null)? "\$criteria->addInCondition(\"locked\", array(0));" : ""; ?>
      MGHelper::log('batch-delete', 'Batch deleted <?php echo $this->modelClass; ?> with IDs(' . implode(',', $_POST['<?php echo $this->class2id($this->modelClass)?>-ids']) . ')');
        
      $model = new <?php echo $this->modelClass; ?>;
      $model->deleteAll($criteria);
        
    } 
  }
}