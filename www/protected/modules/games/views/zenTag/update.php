<?php

$this->breadcrumbs = array(
  Yii::t('app', 'Admin')=>array('/admin'),
	Yii::t('app', 'Games') => array('/games'),
	$model->name => array('view'),
	Yii::t('app', 'Update'),
);

$this->menu = array(
	array('label'=>Yii::t('app', 'Manage') . ' ' . Yii::t('app', 'Games'), 'url'=>array('/games')),
	array('label' => Yii::t('app', 'View') . ' ' . $model->name, 'url'=>array('view')),
);
?>

<h1><?php echo Yii::t('app', 'Update') . ' ' . GxHtml::encode($model->name); ?></h1>

<?php
$this->renderPartial('/zenTag/_form', array(
		'model' => $model, 
		'buttons' => Yii::t('app', 'Save')));

?>