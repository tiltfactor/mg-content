<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'guesswhat-form',
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
    <?php echo $form->labelEx($model,'turns'); ?>
    <?php echo $form->textField($model,'turns'); ?>
    <?php echo $form->error($model,'turns'); ?>
  </div>
  
  <div class="row">
    <?php echo $form->labelEx($model,'number_guesses'); ?>
    <?php echo $form->textField($model,'number_guesses'); ?>
    <?php echo $form->error($model,'number_guesses'); ?>
  </div>
  
  <div class="row">
    <?php echo $form->labelEx($model,'number_hints'); ?>
    <?php echo $form->textField($model,'number_hints'); ?>
    <?php echo $form->error($model,'number_hints'); ?>
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
  
  <div class="row">
    <?php echo $form->labelEx($model,'image_grid_width'); ?>
    <?php echo $form->textField($model,'image_grid_width'); ?>
    <?php echo $form->error($model,'image_grid_width'); ?>
  </div>
  
  <div class="row">
    <?php echo $form->labelEx($model,'image_grid_height'); ?>
    <?php echo $form->textField($model,'image_grid_height'); ?>
    <?php echo $form->error($model,'image_grid_height'); ?>
  </div>
  
  <div class="row">
    <?php echo $form->labelEx($model,'partner_wait_threshold'); ?>
    <?php echo $form->textField($model,'partner_wait_threshold'); ?>
    <?php echo $form->error($model,'partner_wait_threshold'); ?>
  </div>
  
  <div class="row">
    <?php echo $form->labelEx($model,'play_against_computer'); ?>
    <?php echo $form->dropDownList($model,'play_against_computer', MGHelper::itemAlias('yes-no')); ?>
    <?php echo $form->error($model,'play_against_computer'); ?>
  </div>
  
  <div class="row clearfix">
  <h2><?php echo GxHtml::encode($model->getRelationLabel('imageSets')); ?></h2>
  <?php echo $form->checkBoxList($model, 'imageSets', GxHtml::encodeEx(GxHtml::listDataEx(ImageSet::model()->findAllAttributes(null, true)), false, true), 
        array("template" => '<div class="checkbox">{input} {label}</div>', "separator" => "")); ?>
  </div> 
  <h2><?php echo GxHtml::encode($model->getRelationLabel('plugins')); ?></h2>
  <div class="row clearfix">
  <?php echo $form->checkBoxList($model, 'plugins', GxHtml::encodeEx(GxHtml::listDataEx(Plugin::listActivePluginsForGames()), false, true), array('template' => '<div class="checkbox">{input} {label}</div>', 'separator' => '')); ?>
  </div><!-- row -->
<?php
echo GxHtml::submitButton($buttons);
$this->endWidget();
?>
</div><!-- form -->