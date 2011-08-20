<?php

$this->breadcrumbs = array(
	Yii::t('app', 'Admin')=>array('/admin'),
	$model->label(2),
);

$arr_menu[] = array('label'=>Yii::t('app', 'List') . ' ' . $model->label(2), 'url'=>array('index'), 'visible' => Yii::app()->user->checkAccess('editor'));
$arr_menu[] = array('label'=>Yii::t('app', 'Manage') . ' ' . $model->label(2), 'url'=>array('admin'), 'visible' => Yii::app()->user->checkAccess('admin'));

$this->menu = $arr_menu;
?>
<h1><?php echo Yii::t('app', 'Manage') . ' ' . GxHtml::encode($model->label(2)); ?></h1>

<?php  $this->widget('zii.widgets.grid.CGridView', array(
  'id' => 'plugin-grid',
  'dataProvider' => $dataProvider,
  'columns' => array(
    'name',
    array(
      'name' => 'link',
      'type' => 'html',
    )
  ),
));?>