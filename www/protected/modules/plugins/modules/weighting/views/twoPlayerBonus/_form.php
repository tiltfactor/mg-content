<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'twoplayerbonus-form',
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
<?php
echo GxHtml::submitButton($buttons);
$this->endWidget();
?>
</div><!-- form -->