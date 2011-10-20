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
	$.fn.yiiGridView.update('image-grid', {
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

<?php echo CHtml::beginForm('','post',array('id'=>'image-form'));
$tagDialog = $this->widget('MGTagJuiDialog');
$this->widget('zii.widgets.grid.CGridView', array(
	'id' => 'image-grid',
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
      'id'=>'image-ids',
    ),
    array(
        'name' => 'name',
        'cssClassExpression' => '"image"',
        'type'=>'html',
        'value'=>'CHtml::image(Yii::app()->getBaseUrl() . Yii::app()->fbvStorage->get(\'settings.app_upload_url\') . \'/thumbs/\'. $data->name, $data->name) . " <span>" . $data->name . "</span>"',
      ),
    array(
      'cssClassExpression' => "'tags'",
      'header' => Yii::t('app', 'Top Tags'),
      'type' => 'html',
      'value'=>'$data->getTopTags(15)',
    ),
    array(
      'cssClassExpression' => "'tags'",
      'header' => Yii::t('app', 'Image Sets'),
      'type' => 'html',
      'value'=>'$data->listImageSets()',
    ),
		//'size',
		'batch_id',
		//'last_access', //xxx implement last access
		//'created',
		    // xxx show image sets and allow for dropdown filter
		/*
		 array(
        'name' => 'locked',
        'type' => 'raw',
        'value' => 'MGHelper::itemAlias(\'locked\',$data->locked)',
        'filter'=> MGHelper::itemAlias('locked'),
      ),
		
		'modified',
		*/
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
$image_sets = GxHtml::listDataEx(ImageSet::model()->findAllAttributes(null, true));

if (count($image_sets)) {
  foreach ($image_sets as $id => $name) {
    if ($id != 1)
      $batch_actions[] = array('label'=>Yii::t('ui','Assign to image set: ' . $name),'url'=>array('batch', 'op' => 'image-set-add', 'isid' => $id)); 
  }
  foreach ($image_sets as $id => $name) {
    if ($id != 1)
      $batch_actions[] = array('label'=>Yii::t('ui','Remove from image set: ' . $name),'url'=>array('batch', 'op' => 'image-set-remove', 'isid' => $id)); 
  }
}

$this->widget('ext.gridbatchaction.GridBatchAction', array(
      'formId'=>'image-form',
      'checkBoxId'=>'image-ids',
      'ajaxGridId'=>'image-grid', 
      'items'=> $batch_actions,
      'htmlOptions'=>array('class'=>'batchActions'),
  ));

?>