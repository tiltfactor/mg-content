<?php

$this->breadcrumbs = array(
	Yii::t('app', 'Admin')=>array('/admin'),
	$model->label(2),
);

$arr_menu[] = array('label'=>Yii::t('app', 'List') . ' ' . $model->label(2), 'url'=>array('index'), 'visible' => Yii::app()->user->checkAccess('editor'));
$arr_menu[] = array('label'=>Yii::t('app', 'Manage') . ' ' . $model->label(2), 'url'=>array('admin'), 'visible' => Yii::app()->user->checkAccess('admin'));

$this->menu = $arr_menu;

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('plugin-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

?>

<h1><?php echo Yii::t('app', 'Manage') . ' ' . GxHtml::encode($model->label(2)); ?></h1>

<p>
You may optionally enter a comparison operator (&lt;, &lt;=, &gt;, &gt;=, &lt;&gt; or =) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo GxHtml::link(Yii::t('app', 'Advanced Search'), '#', array('class' => 'search-button')); ?>
<div class="search-form">
<?php $this->renderPartial('_search', array(
	'model' => $model,
	'type_filter' => $type_filter,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'plugin-grid',
	'dataProvider' => $model->search(),
	'cssFile' => Yii::app()->request->baseUrl . "/css/yii/gridview/styles.css",
  'pager' => array('cssFile' => Yii::app()->request->baseUrl . "/css/yii/pager.css"),
  'baseScriptUrl' => Yii::app()->request->baseUrl . "/css/yii/gridview",
  'filter' => $model,
	'columns' => array(
		array(
      'name' => 'type',
      'value' => '$data->type',
      'filter'=> $type_filter,
    ),
		'unique_id',
		array(
      'name' => 'active',
      'type' => 'raw',
      'value' => 'MGHelper::itemAlias("active",$data->active)',
      'filter'=> MGHelper::itemAlias("active"),
    ),
    array(
      'header' => Yii::t('app', 'Used By Games'),
      'type' => 'html',
      'value'=>'Plugin::listGamesUsingPlugin($data->id)',
    ),
    array(
      'header' => Yii::t('app', 'Manage'),
      'type' => 'raw',
      'value' => 'PluginsModule::pluginAdminLink($data->unique_id)',
    ),
		array(
			'class' => 'CButtonColumn',
		),
	),
)); ?>