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
	$.fn.yiiGridView.update('media-grid', {
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

<?php echo CHtml::beginForm('','post',array('id'=>'media-form'));
$tagDialog = $this->widget('MGTagJuiDialog');

// Maximum number of tags to show in the 'Top Tags' column.
$max_toptags = 15;

$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'media-grid',
	'dataProvider' => $model->search(),
	'filter' => $model,
	'cssFile' => Yii::app()->request->baseUrl . "/css/yii/gridview/styles.css",
	'pager' => array('cssFile' => Yii::app()->request->baseUrl . "/css/yii/pager.css"),
	'baseScriptUrl' => Yii::app()->request->baseUrl . "/css/yii/gridview",
	'afterAjaxUpdate' => $tagDialog->gridViewUpdate(),
	'selectableRows'=>2,
  'columns' => array(
    array(
      'class'=>'CCheckBoxColumn',
      'id'=>'media-ids',
    ),
    array(
        'name' => 'name',
        'cssClassExpression' => '"media"',
        'type'=>'html',
        'value'=>'CHtml::image(Yii::app()->getBaseUrl() . Yii::app()->fbvStorage->get(\'settings.app_upload_url\') . \'/thumbs/\'. $data->name, $data->name) . " <span>" . $data->name . "</span>"',
      ),
    'tag_count',
    array(
      'cssClassExpression' => "'tags'",
      'header' => Yii::t('app', "Top $max_toptags Tags"),
      'type' => 'html',
      'value'=>'$data->getTopTags(' . $max_toptags . ')',
    ),
    array(
      'cssClassExpression' => "'tags'",
      'header' => Yii::t('app', 'Collections'),
      'type' => 'html',
      'value'=>'$data->listCollections()',
    ),
		//'size',
		'batch_id',
		'last_access', 
		//'created',
    //'modified',
    array (
  'class' => 'CButtonColumn',
  'buttons' => 
  array (
    'delete' => 
    array (
      'visible' => '$data->locked == 0',
    ),
  ),
)  ),
)); 
echo CHtml::endForm();

$batch_actions = array();
$collections = GxHtml::listDataEx(Collection::model()->findAllAttributes(null, true));

if (count($collections)) {
  foreach ($collections as $id => $name) {
    if ($id != 1)
      $batch_actions[] = array('label'=>Yii::t('ui','Assign to collection: ' . $name),'url'=>array('batch', 'op' => 'collection-add', 'isid' => $id));
  }
  foreach ($collections as $id => $name) {
    if ($id != 1)
      $batch_actions[] = array('label'=>Yii::t('ui','Remove from collection: ' . $name),'url'=>array('batch', 'op' => 'collection-remove', 'isid' => $id));
  }
}

$this->widget('ext.gridbatchaction.GridBatchAction', array(
      'formId'=>'media-form',
      'checkBoxId'=>'media-ids',
      'ajaxGridId'=>'media-grid',
      'items'=> $batch_actions,
      'htmlOptions'=>array('class'=>'batchActions'),
  ));

?>