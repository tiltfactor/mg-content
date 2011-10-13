<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'guesswhatscoring-form',
	'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit'=>true),
));
?>
	<p class="note">Fields with <span class="required">*</span> are required.</p>

  <?php echo $form->errorSummary($model); ?>
  <div class="row">
    <?php echo $form->labelEx($model,'score_new'); ?>
    <?php echo $form->textField($model,'score_new'); ?>
    <?php echo $form->error($model,'score_new'); ?>
  </div>
  <div class="row">
    <?php echo $form->labelEx($model,'score_match'); ?>
    <?php echo $form->textField($model,'score_match'); ?>
    <?php echo $form->error($model,'score_match'); ?>
  </div>
  <div class="row">
    <?php echo $form->labelEx($model,'score_first_guess'); ?>
    <?php echo $form->textField($model,'score_first_guess'); ?>
    <?php echo $form->error($model,'score_first_guess'); ?>
  </div>
  <div class="row">
    <?php echo $form->labelEx($model,'score_second_guess'); ?>
    <?php echo $form->textField($model,'score_second_guess'); ?>
    <?php echo $form->error($model,'score_second_guess'); ?>
  </div>
  <div class="row">
    <?php echo $form->labelEx($model,'score_third_guess'); ?>
    <?php echo $form->textField($model,'score_third_guess'); ?>
    <?php echo $form->error($model,'score_third_guess'); ?>
  </div>
  <div class="row">
    <?php echo $form->labelEx($model,'additional_weight_first_guess'); ?>
    <?php echo $form->textField($model,'additional_weight_first_guess'); ?>
    <?php echo $form->error($model,'additional_weight_first_guess'); ?>
  </div>  
<?php
echo GxHtml::submitButton($buttons);
$this->endWidget();
?>
</div><!-- form -->