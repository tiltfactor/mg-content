<?php

$this->breadcrumbs = array(
	Yii::t('app', 'Admin')=>array('/admin'),
	Yii::t('app', 'Tags')=>array('/admin/tag'),
	$model->label(2),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('tag-use-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo Yii::t('app', 'View') . ' ' . GxHtml::encode($model->label(2)); ?></h1>

<p>
You may optionally enter a comparison operator (&lt;, &lt;=, &gt;, &gt;=, &lt;&gt; or =) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo GxHtml::link(Yii::t('app', 'Advanced Search'), '#', array('class' => 'search-button')); ?>
<div class="search-form">
<?php $this->renderPartial('_search', array(
	'model' => $model,
)); ?>
</div><!-- search-form -->

<?php echo CHtml::beginForm('','post',array('id'=>'tag-use-form'));
$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'tag-use-grid',
	'dataProvider' => $model->search(),
	'filter' => $model,
	'cssFile' => Yii::app()->request->baseUrl . "/css/yii/gridview/styles.css",
	'pager' => array('cssFile' => Yii::app()->request->baseUrl . "/css/yii/pager.css"),
	'baseScriptUrl' => "/css/yii/gridview",
	'selectableRows'=>2,
	'columns' => array(
	  array(
      'class'=>'CCheckBoxColumn',
      'id'=>'tag-use-ids',
    ),
    array(
        'header' => Yii::t('app', 'Image ID'),
        'name' => 'image_id',
        'cssClassExpression' => '"image"',
        'type'=>'html',
        'value'=>'GxHtml::link(CHtml::image(Yii::app()->getBaseUrl() . Yii::app()->fbvStorage->get(\'settings.app_upload_url\') . \'/thumbs/\'. GxHtml::valueEx($data->image), GxHtml::valueEx($data->image)) . " <span>" . GxHtml::valueEx($data->image) . "</span>", array(\'image/view\', \'id\' => GxActiveRecord::extractPkValue($data->image, true)))',
      ),
		array(
		    'header' => Yii::t('app', 'Tag ID'),
				'name'=>'tag_id',
				'type'=>'html',
				'value'=>'GxHtml::link(GxHtml::valueEx($data->tag), array(\'tag/view\', \'id\' => GxActiveRecord::extractPkValue($data->tag, true)))',
				),
		'weight',
		array(
      'name' => 'type',
      'filter' => TagUse::getUsedTypes()
    ),
		'created',
    array (
  'class' => 'CButtonColumn',
  'buttons' => 
    array (
    'delete' => 
    array (
      'visible' => 'false',
    ),
  ),
)  ),
)); 
echo CHtml::endForm();

$this->widget('ext.gridbatchaction.GridBatchAction', array(
      'formId'=>'tag-use-form',
      'checkBoxId'=>'tag-use-ids',
      'ajaxGridId'=>'tag-use-grid', 
      'items'=>array(
          array('label'=>Yii::t('ui','Delete selected items'),'url'=>array('batch', 'op' => 'delete'))
      ),
      'htmlOptions'=>array('class'=>'batchActions'),
  ));

?>