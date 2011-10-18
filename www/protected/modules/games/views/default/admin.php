<?php

$this->breadcrumbs = array(
	Yii::t('app', 'Admin')=>array('/admin'),
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
	$.fn.yiiGridView.update('game-grid', {
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

<?php echo CHtml::beginForm('','post',array('id'=>'game-form'));
$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'game-grid',
	'cssFile' => Yii::app()->request->baseUrl . "/css/yii/gridview/styles.css",
  'pager' => array('cssFile' => Yii::app()->request->baseUrl . "/css/yii/pager.css"),
  'baseScriptUrl' => Yii::app()->request->baseUrl . "/css/yii/gridview",
  'dataProvider' => $model->search(),
	'filter' => $model,
	'columns' => array(
		 array(
        'name' => 'active',
        'type' => 'raw',
        'value' => 'MGHelper::itemAlias(\'active\',$data->active)',
        'filter'=> MGHelper::itemAlias('active'),
      ),
		'number_played',
		'unique_id',
		array(
      'header' => Yii::t('app', 'Used Plugins'),
      'type' => 'html',
      'value' => 'Plugin::listPluginsUsedByGame($data->id)',
    ),
		'created',
		'modified',
    array (
  'class' => 'CButtonColumn',
  'buttons' => 
  array (
   'delete' => array(
     'visible' => 'false',
    ),
    'view' => array(
     'url'=>"Yii::app()->createUrl('games/' . \$data->unique_id . '/view' );"
    ),
    'update' => array(
     'url'=>"Yii::app()->createUrl('games/' . \$data->unique_id . '/update');"
    ),
  ),
)),
)); 
echo CHtml::endForm();

?>