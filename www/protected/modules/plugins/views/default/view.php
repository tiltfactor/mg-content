<?php

$this->breadcrumbs = array(
  Yii::t('app', 'Admin')=>array('/admin'),
	$model->label(2) => array('admin'),
	GxHtml::valueEx($model, "unique_id"),
);

$arr_menu = array();

if (($link = PluginsModule::pluginAdminLink($model->unique_id)) != "") {
  $arr_menu[] = array('label'=>$link, 'visible' => Yii::app()->user->checkAccess('admin'));
} 
$arr_menu[] = array('label'=>Yii::t('app', 'Manage') . ' ' . $model->label(2), 'url'=>array('admin'), 'visible' => Yii::app()->user->checkAccess('editor'));
$arr_menu[] = array('label'=>Yii::t('app', 'Update') . ' ' . $model->label(), 'url'=>array('update', 'id' => $model->id), 'visible' => Yii::app()->user->checkAccess('admin'));
$arr_menu[] = array('label'=>Yii::t('app', 'Delete') . ' ' . $model->label(), 'url'=>'#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->id), 'confirm'=>'Are you sure you want to delete this item?'), 'visible' => Yii::app()->user->checkAccess('admin'));

$this->menu = $arr_menu;
?>

<h1><?php echo Yii::t('app', 'View') . ' ' . GxHtml::encode($model->label()) . ' ' . GxHtml::encode(GxHtml::valueEx($model, "unique_id")); ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'attributes' => array(
'type',
'unique_id',
array(
  'name' => 'active',
  'value' => MGHelper::itemAlias("active",$model->active),
),

'created',
'modified',
array(
      'name' => Yii::t('app', 'Used By Games'),
      'type' => 'html',
      'value'=> Plugin::listGamesUsingPlugin($model->id),
    ),	),
)); ?>

