<?php

$this->breadcrumbs = array(
  Yii::t('app', 'Admin')=>array('/admin'),
  Yii::t('app', 'Tags')=>array('/admin/tag'),
	$model->label(2) => array('admin'),
	GxHtml::valueEx($model),
);

$this->menu=array(
	array('label'=>Yii::t('app', 'Manage') . ' ' . $model->label(2), 'url'=>array('admin')),
	array('label'=>Yii::t('app', 'Update') . ' ' . $model->label(), 'url'=>array('update', 'id' => $model->id)),
);
?>

<h1><?php echo Yii::t('app', 'View') . ' ' . GxHtml::encode($model->label()) . ' ' . GxHtml::encode(GxHtml::valueEx($model)); ?></h1>

<?php 
if ($model->image) {
  $image_html = CHtml::image(Yii::app()->getBaseUrl() . Yii::app()->fbvStorage->get('settings.app_upload_url') . '/thumbs/'. GxHtml::valueEx($model->image));
  $image = GxHtml::link($image_html, array('image/view', 'id' => GxActiveRecord::extractPkValue($model->image, true)));
} else {
  $image = null;
}


$this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'cssFile' => Yii::app()->request->baseUrl . "/css/yii/detailview/styles.css",
  'attributes' => array(
'id',
array(
			'name' => 'image',
			'type' => 'raw',
			'value' => $image,
			),
array(
			'name' => 'tag',
			'type' => 'raw',
			'value' => $model->tag !== null ? GxHtml::link(GxHtml::encode(GxHtml::valueEx($model->tag)), array('tag/view', 'id' => GxActiveRecord::extractPkValue($model->tag, true))) : null,
			),
'weight',
'type',
'created',
array(
			'name' => Yii::t('app', 'Game Submission (This tag use is based on the following game submission [Raw Data])'),
			'type' => 'raw',
			'value' => $model->gameSubmission !== null ? '<pre>' . print_r(json_decode(GxHtml::valueEx($model->gameSubmission)), true) . '</pre>': null,
			),
	),
)); ?>

<h2><?php echo GxHtml::encode($model->getRelationLabel('tagOriginalVersions')); ?></h2>
<?php
	echo GxHtml::openTag('ul');
	
	if (count($model->tagOriginalVersions) == 0) {
    echo "<li>no item(s) assigned</li>";
  }
  
	foreach($model->tagOriginalVersions as $relatedModel) {
		echo GxHtml::openTag('li');
		echo GxHtml::link(GxHtml::encode(GxHtml::valueEx($relatedModel)), array('tagOriginalVersion/view', 'id' => GxActiveRecord::extractPkValue($relatedModel, true)));
		echo GxHtml::closeTag('li');
	}
	echo GxHtml::closeTag('ul');
?>