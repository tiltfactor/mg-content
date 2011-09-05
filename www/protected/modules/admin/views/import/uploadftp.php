<?php

$this->breadcrumbs = array(
  Yii::t('app', 'Admin')=>array('/admin'),
	Yii::t('app', 'Import') => array('index'),
	Yii::t('app', "Import images that can be found on in the server's '/uploads/ftp' folder"),
);
?>

<h1><?php echo Yii::t('app', "Import images that can be found on in the server's '/uploads/ftp' folder"); ?></h1>


<div class="form">

<?php $form = $this->beginWidget('GxActiveForm', array(
  'id' => 'import-form',
));
?>
  <?php echo $form->errorSummary($model); ?>

  <div class="row">
  <?php echo $form->labelEx($model,'batch_id'); ?>
  <?php echo $form->textField($model, 'batch_id', array('maxlength' => 45)); ?>
  <?php echo CHtml::tag("small", array(), Yii::t('app', 'The batch id will help you to distinguish images on the import process page'), TRUE); ?>
  <?php echo $form->error($model,'batch_id'); ?>
  </div><!-- row -->
<?php
echo GxHtml::submitButton(Yii::t('app', 'Import Images'));
$this->endWidget();
?>
</div><!-- form -->