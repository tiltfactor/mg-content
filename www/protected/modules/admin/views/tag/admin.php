<?php

$this->breadcrumbs = array(
	Yii::t('app', 'Admin')=>array('/admin'),
	$model->label(2),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('tag-grid', {
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

<?php echo CHtml::beginForm('','post',array('id'=>'tag-form'));
$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'tag-grid',
	'dataProvider' => $model->search(),
	'filter' => $model,
	'cssFile' => Yii::app()->request->baseUrl . "/css/yii/gridview/styles.css",
	'pager' => array('cssFile' => Yii::app()->request->baseUrl . "/css/yii/pager.css"),
	'baseScriptUrl' => "/css/yii/gridview",
	'selectableRows'=>2,
	'columns' => array(
	  array(
      'class'=>'CCheckBoxColumn',
      'id'=>'tag-ids',
    ),
		'tag',
    array(
      'cssClassExpression' => "'tag-info'",
      'header' => Yii::t('app', 'Tag Info'),
      'type' => 'html',
      'value'=>'$data->getTagUseInfo()',
    ),
		array(
      'cssClassExpression' => "'top-images'",
      'header' => Yii::t('app', 'Top Images'),
      'type' => 'html',
      'value'=>'$data->getTopImages(5)',
    ),
    array(
      'cssClassExpression' => "'top-users'",
      'header' => Yii::t('app', 'Top Users'),
      'type' => 'html',
      'value'=>'$data->getTopUsers(10)',
    ),
    'created',
    'modified',
    array (
      'class' => 'CButtonColumn',
      'buttons' => 
      array (
        'delete' => 
        array (
          'visible' => 'false',
        ),
      ),
    )  
 ),
)); 
echo CHtml::endForm();

$this->widget('ext.gridbatchaction.GridBatchAction', array(
      'formId'=>'tag-form',
      'checkBoxId'=>'tag-ids',
      'ajaxGridId'=>'tag-grid', 
      'items'=>array(
      ),
      'htmlOptions'=>array('class'=>'batchActions'),
  ));

?>