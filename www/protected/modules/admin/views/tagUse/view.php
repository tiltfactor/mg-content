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
	array('label'=>Yii::t('app', 'Re-Weight') . ' ' . $model->label(), 'url'=>array('weight', 'id' => $model->id)),
);
?>

<h1><?php echo Yii::t('app', 'View') . ' ' . GxHtml::encode($model->label()) . ' ' . GxHtml::encode(GxHtml::valueEx($model)); ?></h1>

<?php 
if ($model->media) {
  $image_html = CHtml::image(Yii::app()->getBaseUrl() . Yii::app()->fbvStorage->get('settings.app_upload_url') . '/thumbs/'. GxHtml::valueEx($model->media))  . ' <span>' . GxHtml::valueEx($model->media) . '</span>';
  $media = GxHtml::link($image_html, array('image/view', 'id' => GxActiveRecord::extractPkValue($model->media, true)), array('class' => 'image'));
} else {
  $media = null;
}


$this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'cssFile' => Yii::app()->request->baseUrl . "/css/yii/detailview/styles.css",
  'attributes' => array(
'id',
array(
			'name' => 'image',
			'type' => 'raw',
			'value' => $media,
			),
array(
			'name' => 'tag',
			'type' => 'raw',
			'value' => $model->tag !== null ? GxHtml::link(GxHtml::encode(GxHtml::valueEx($model->tag)), array('tag/view', 'id' => GxActiveRecord::extractPkValue($model->tag, true))) : null,
			),
array(
      'name' => Yii::t('app', 'Player Name'),
      'type' => 'raw',
      'value' => $model->getUserName(),
      ),
'weight',
'type',
array(
      'name' => Yii::t('app', 'IP Address'),
      'type' => 'raw',
      'value' => $model->getIpAddress(),
      ),
'created',
array(
			'name' => Yii::t('app', 'Game Submission (This tag use is based on the following game submission [Raw Data])'),
			'type' => 'raw',
			'value' => $model->gameSubmission !== null ? '<pre>' . print_r(json_decode(GxHtml::valueEx($model->gameSubmission)), true) . '</pre>': null,
			),
	),
)); ?>

<?php if (count($model->tagOriginalVersions) > 0) : ?>
  <h1>This Tag Use has been modified (Tag Use Original Versions are)</h1>
<?php 
$this->widget('zii.widgets.grid.CGridView', array(
  'id' => 'tag-use-grid',
  'dataProvider' => TagOriginalVersion::listTagUseOriginalVersions($model->id),
  'cssFile' => Yii::app()->request->baseUrl . "/css/yii/gridview/styles.css",
  'pager' => array('cssFile' => Yii::app()->request->baseUrl . "/css/yii/pager.css"),
  'baseScriptUrl' => Yii::app()->request->baseUrl . "/css/yii/gridview",
  'columns' => array(
    'original_tag',
    'comments',
    array(
      'header' => Yii::t('app', 'By Player Name'),
      'name' => 'username',
      'type'=>'html',
      'value'=>'$data->getUserName()',
    ),
    'created',
 ),
)); 

endif; ?>
