<?php

$this->breadcrumbs = array(
	Yii::t('app', 'Admin')=>array('/admin'),
	Yii::t('app', 'Plugins')=>array('/plugins'),
  Yii::t('app', 'Dictionary'),
	$model->label(2),
);

$this->menu = array(
		array('label'=>Yii::t('app', 'Create') . ' ' . $model->label(), 'url'=>array('create')),
	);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('stop-word-grid', {
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
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'stop-word-grid',
	'dataProvider' => $model->search(),
	'filter' => $model,
	'columns' => array(
		'id',
		'word',
		'counter',
		array(
      'name' => 'active',
      'type' => 'raw',
      'value' => 'MGHelper::itemAlias("active",$data->active)',
      'filter'=> MGHelper::itemAlias("active"),
    ),
		'created',
		'modified',
		array(
			'class' => 'CButtonColumn',
		),
	),
)); ?>