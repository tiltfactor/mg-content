<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'settings-form',
	'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit'=>true),
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

  <div class="row">
  <?php echo $form->labelEx($model,'app_name'); ?>
  <?php echo $form->textField($model, 'app_name'); ?>
  <?php echo $form->error($model,'app_name'); ?>
  </div><!-- row -->
  <div class="row">
  <?php echo $form->labelEx($model,'throttle_interval'); ?>
  <?php echo $form->textField($model, 'throttle_interval'); ?>
  <?php echo $form->error($model,'throttle_interval'); ?>
  </div><!-- row -->
  <div class="row">
  <?php echo $form->labelEx($model,'message_queue_interval'); ?>
  <?php echo $form->textField($model, 'message_queue_interval'); ?>
  <?php echo $form->error($model,'message_queue_interval'); ?>
  </div><!-- row -->
  <div class="row">
  <?php echo $form->labelEx($model,'app_email'); ?>
  <?php echo $form->textField($model, 'app_email'); ?>
  <?php echo $form->error($model,'app_email'); ?>
  </div><!-- row -->
  <div class="row">
  <?php echo $form->labelEx($model,'pagination_size'); ?>
  <?php echo $form->textField($model, 'pagination_size'); ?>
  <?php echo $form->error($model,'pagination_size'); ?>
  </div><!-- row -->
  <div class="row">
  <?php echo $form->labelEx($model,'app_upload_path'); ?>
  <?php echo $form->textField($model, 'app_upload_path'); ?>
  <?php echo $form->error($model,'app_upload_path'); ?>
  </div><!-- row -->
  <div class="row">
  <?php echo $form->labelEx($model,'app_upload_url'); ?>
  <?php echo $form->textField($model, 'app_upload_url'); ?>
  <?php echo $form->error($model,'app_upload_url'); ?>
  </div><!-- row -->
<?php
echo GxHtml::submitButton($buttons);
$this->endWidget();
?>
</div><!-- form -->