<?php

$this->breadcrumbs = array(
	Yii::t('app', 'Admin')=>array('/admin'),
	$model->label(2),
);

$this->menu = array();

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
      'value' => 'Plugin::itemAlias("active",$data->active)',
      'filter'=> Plugin::itemAlias("active"),
    ),
		'created',
		'modified',
		array(
			'class' => 'CButtonColumn',
		),
	),
)); ?>