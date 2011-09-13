<?php
$this->breadcrumbs = array(
  Yii::t('app', 'Admin')=>array('/admin'),
  Yii::t('app', 'Plugins')=>array('/plugins'),
  Yii::t('app', 'Dictionary'),
  $model->getPluginID(),
);

$this->menu = array(
  array('label' => Yii::t('app', 'Update') . ' ' . $model->getPluginID(), 'url'=>array('update')),
);

?>
<h1><?php echo Yii::t('app', 'View') . ' ' . GxHtml::encode($model->getPluginID()); ?></h1>
<?php $this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'cssFile' => Yii::app()->request->baseUrl . "/css/yii/detailview/styles.css",
  'attributes' => array(
    'words_to_avoid_threshold',
	),
)); ?>
