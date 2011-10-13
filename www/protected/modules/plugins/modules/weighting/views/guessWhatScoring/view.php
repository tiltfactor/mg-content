<?php
$this->breadcrumbs = array(
  Yii::t('app', 'Admin')=>array('/admin'),
  Yii::t('app', 'Plugins')=>array('/plugins'),
  Yii::t('app', 'Weighting'),
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
    'score_new',
    'score_match',
    'score_first_guess',
    'score_second_guess',
    'score_third_guess',
    'additional_weight_first_guess',
	),
)); ?>
