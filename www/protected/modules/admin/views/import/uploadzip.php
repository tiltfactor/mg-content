<?php

$this->breadcrumbs = array(
  Yii::t('app', 'Admin')=>array('/admin'),
	Yii::t('app', 'Import') => array('index'),
	Yii::t('app', 'Import Media in a Zip file'),
);
?>

<h1><?php echo Yii::t('app', 'Import Media in a Zip file'); ?></h1>


<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
  'id' => 'import-form',
  'htmlOptions'=>array('enctype'=>'multipart/form-data'),
));
?>
  <p class="note">
    <?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>. You can upload files upto a size of 
    <?php echo ini_get('upload_max_filesize'); ?>B.
  </p>

  <?php echo $form->errorSummary($model); ?>

  <div class="row">
  <?php echo $form->labelEx($model,'batch_id'); ?>
  <?php echo $form->textField($model, 'batch_id', array('maxlength' => 45)); ?>
  <?php echo CHtml::tag("small", array(), Yii::t('app', 'The batch id will help you to distinguish images on the import process page'), TRUE); ?>
  <?php echo $form->error($model,'batch_id'); ?>
  </div><!-- row -->
  <div class="row">
  <?php echo $form->labelEx($model,'zipfile'); ?>
  <?php echo $form->fileField($model, 'zipfile',array('size'=>62, 'class'=>'file')); ?>
  <?php echo $form->error($model,'zipfile'); ?>
  </div><!-- row -->
    
<?php
echo GxHtml::submitButton(Yii::t('app', 'Import Media'));
$this->endWidget();
?>
</div><!-- form -->
