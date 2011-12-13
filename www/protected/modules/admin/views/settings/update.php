<?php

$this->breadcrumbs = array(
  Yii::t('app', 'Admin')=>array('/admin'),
  Yii::t('app', 'Global Settings') => array('view'),
	Yii::t('app', 'Update'),
);

$this->menu = array(
	array('label' => Yii::t('app', 'View') . ' ' . Yii::t('app', 'Global Settings'), 'url'=>array('view')),
	
);
?>

<h1><?php echo Yii::t('app', 'Update') . ' ' . Yii::t('app', 'Global Settings'); ?></h1>

<?php
$this->renderPartial('_form', array(
		'model' => $model, 
		'buttons' => Yii::t('app', 'Save')));
?>