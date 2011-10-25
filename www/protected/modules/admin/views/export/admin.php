<?php
$this->breadcrumbs=array(
	Yii::t('app', 'Admin')=>array('/admin'),
	yii::t('app','Export'),
);

$this->menu = array(
    array('label'=>Yii::t('app', 'View exported files'), 'url'=>array('export/exported')),
    //array('label'=>UserModule::t('Create') . ' ' . $model->label(), 'url'=>array('create')),
  );
?>

<h1><?php echo Yii::t('app', 'Export Tags, Tag Uses, or Images'); ?></h1>

<p>Please define export file name and the images and their information that should be exported with help of the form below</p>

<div class="export-form show">
<?php $this->renderPartial('_form', array(
  'model' => $model,
  'count_affected_images' => $count_affected_images
)); ?>
</div><!-- search-form -->
