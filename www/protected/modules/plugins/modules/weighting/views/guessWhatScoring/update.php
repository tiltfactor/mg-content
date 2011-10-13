<?php

$this->breadcrumbs = array(
  Yii::t('app', 'Admin')=>array('/admin'),
  Yii::t('app', 'Plugins')=>array('/plugins'),
  Yii::t('app', 'Weighting'),
	$model->getPluginID() => array('view'),
	Yii::t('app', 'Update'),
);

$this->menu = array(
	array('label' => Yii::t('app', 'View') . ' ' . $model->getPluginID(), 'url'=>array('view')),
);
?>

<h1><?php echo Yii::t('app', 'Update') . ' ' . GxHtml::encode($model->getPluginID()); ?></h1>

<?php
$this->renderPartial('_form', array(
		'model' => $model, 
		'buttons' => Yii::t('app', 'Save')));
?>