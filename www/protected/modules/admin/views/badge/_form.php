<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'badge-form',
	'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit'=>true),
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

<?php
echo GxHtml::submitButton($buttons);
$this->endWidget();
?>
</div><!-- form -->