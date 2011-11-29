<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'badge-form',
    'htmlOptions'=>array('enctype'=>'multipart/form-data'),
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

  <div class="row">
  <?php echo $form->labelEx($model,'title'); ?>
  <?php echo $form->textField($model, 'title', array('maxlength' => 45)); ?>
  <?php echo $form->error($model,'title'); ?>
  </div><!-- row -->
  <div class="row">
  <?php echo $form->labelEx($model,'points'); ?>
  <?php echo $form->textField($model, 'points'); ?>
  <?php echo $form->error($model,'points'); ?>
  </div><!-- row -->
  
  <div class="row">
  <?php echo $form->labelEx($model,'image_inactive'); ?>
  <?php echo $form->fileField($model, 'image_inactive'); ?>
  <?php echo $form->error($model,'image_inactive'); ?>
  </div><!-- row -->
  
  <div class="row">
  <?php echo $form->labelEx($model,'image_active'); ?>
  <?php echo $form->fileField($model, 'image_active'); ?>
  <?php echo $form->error($model,'image_active'); ?>
  </div><!-- row -->
  

<?php
echo GxHtml::submitButton($buttons);
$this->endWidget();
?>
</div><!-- form -->