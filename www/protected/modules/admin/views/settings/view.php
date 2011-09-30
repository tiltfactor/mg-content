<?php

$this->breadcrumbs = array(
  Yii::t('app', 'Admin')=>array('/admin'),
	Yii::t('app', 'Global Settings')
);

$this->menu=array(
	array('label'=>Yii::t('app', 'Update') . ' ' . Yii::t('app', 'Global Settings'), 'url'=>array('update')),
);
?>

<h1><?php echo Yii::t('app', 'View') . ' ' . Yii::t('app', 'Global Settings'); ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'cssFile' => Yii::app()->request->baseUrl . "/css/yii/detailview/styles.css",
  'attributes' => array(
    'app_name',
    'app_email',
    'throttle_interval',
    'message_queue_interval',
    'pagination_size',
    'app_upload_path',
    'app_upload_url',
	),
)); ?>

