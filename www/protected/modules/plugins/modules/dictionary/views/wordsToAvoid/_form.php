<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'wordstoavoid-form',
	'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit'=>true),
));
?>
	<p class="note">Fields with <span class="required">*</span> are required.</p>

  <?php echo $form->errorSummary($model); ?>
  <div class="row">
    <?php echo $form->labelEx($model,'words_to_avoid_threshold'); ?>
    <?php echo $form->textField($model,'words_to_avoid_threshold'); ?>
    <?php echo $form->error($model,'words_to_avoid_threshold'); ?>
  </div>
<?php
echo GxHtml::submitButton($buttons);
$this->endWidget();
?>
</div><!-- form -->