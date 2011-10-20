<?php

$this->breadcrumbs = array(
  Yii::t('app', 'Admin')=>array('/admin'),
	$model->label(2) => array('admin'),
	GxHtml::valueEx($model) => array('view', 'id' => GxActiveRecord::extractPkValue($model, true)),
	Yii::t('app', 'Update'),
);

$this->menu = array(
	array('label'=>Yii::t('app', 'Manage') . ' ' . $model->label(2), 'url'=>array('admin')),
	array('label' => Yii::t('app', 'View') . ' ' . $model->label(), 'url'=>array('view', 'id' => GxActiveRecord::extractPkValue($model, true))),
	array('label'=>Yii::t('app', 'View Tag Uses for ') . ' "' . $model->tag. '"', 'url'=>array('/admin/tagUse', 'TagUse[tag_id]' => $model->id)),
  array('label' => Yii::t('app', 'Re-Weight Tag Uses for') . ' "' . $model->tag. '"', 'url'=>array('weight', 'id' => GxActiveRecord::extractPkValue($model, true))),
	array('label' => Yii::t('app', 'Ban') . ' ' . $model->label(). ' "' . $model->tag. '"', 'url'=>array('ban', 'id' => GxActiveRecord::extractPkValue($model, true))),
);
?>

<h1><?php echo Yii::t('app', 'Update') . ' ' . GxHtml::encode($model->label()) . ' "' . GxHtml::encode(GxHtml::valueEx($model)); ?>"</h1>

<?php
$this->renderPartial('_form', array(
		'model' => $model, 
		'buttons' => Yii::t('app', 'Save')));
?>