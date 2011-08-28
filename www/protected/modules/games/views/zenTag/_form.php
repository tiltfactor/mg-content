<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'zentag-form',
	'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit'=>true),
));
?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

  <?php echo $form->errorSummary($model); ?>
  <div class="row">
    <?php echo $form->labelEx($model,'active'); ?>
    <?php echo $form->dropDownList($model,'active', MGHelper::itemAlias('active')); ?>
    <?php echo $form->error($model,'active'); ?>
  </div>
  
  <div class="row">
    <?php echo $form->labelEx($model,'name'); ?>
    <?php echo $form->textField($model,'name'); ?>
    <?php echo $form->error($model,'name'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model,'description'); ?>
    <?php echo $form->textArea($model,'description', array("rows"=>6, "cols"=>40)); ?>
    <?php echo $form->error($model,'description'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model,'more_info_url'); ?>
    <?php echo $form->textField($model,'more_info_url'); ?>
    <?php echo $form->error($model,'more_info_url'); ?>
  </div>
  
  <div class="row">
    <?php echo $form->labelEx($model,'arcade_image'); ?>
    <?php echo $form->textField($model,'arcade_image'); ?>
    <?php echo $form->error($model,'arcade_image'); ?>
  </div>
  
  <div class="row">
    <?php echo $form->labelEx($model,'play_once_and_move_on'); ?>
    <?php echo $form->dropDownList($model,'play_once_and_move_on', MGHelper::itemAlias('yes-no')); ?>
    <?php echo $form->error($model,'play_once_and_move_on'); ?>
  </div>
  
  <div class="row">
    <?php echo $form->labelEx($model,'play_once_and_move_on_url'); ?>
    <?php echo $form->textField($model,'play_once_and_move_on_url'); ?>
    <?php echo $form->error($model,'play_once_and_move_on_url'); ?>
  </div>
  
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
    <?php echo $form->labelEx($model,'score_expert'); ?>
    <?php echo $form->textField($model,'score_expert'); ?>
    <?php echo $form->error($model,'score_expert'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model,'turns'); ?>
    <?php echo $form->textField($model,'turns'); ?>
    <?php echo $form->error($model,'turns'); ?>
  </div>
  
  <div class="row">
    <?php echo $form->labelEx($model,'image_width'); ?>
    <?php echo $form->textField($model,'image_width'); ?>
    <?php echo $form->error($model,'image_width'); ?>
  </div>
  
  <div class="row">
    <?php echo $form->labelEx($model,'image_height'); ?>
    <?php echo $form->textField($model,'image_height'); ?>
    <?php echo $form->error($model,'image_height'); ?>
  </div>
<?php
echo GxHtml::submitButton($buttons);
$this->endWidget();
?>
</div><!-- form -->