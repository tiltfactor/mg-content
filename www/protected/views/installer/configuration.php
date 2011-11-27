<?php
$this->pageTitle = Yii::app()->name . ' - Configuration';
?>

<h1><?php echo CHtml::encode(Yii::app()->name); ?> - Configuration</h1>

<div class="form">
<?php $form=$this->beginWidget('UActiveForm', array(
  'id'=>'installconfiguration-form',
  'enableAjaxValidation'=>false,
    'clientOptions'=>array('validateOnSubmit'=>false),
)); ?>

  <p class="note"><?php echo UserModule::t('Fields with <span class="required">*</span> are required.'); ?></p>
  
  <?php echo $form->errorSummary(array($model)); ?>
  
  <div class="row">
  <?php echo $form->labelEx($model,'app_name'); ?>
  <?php echo $form->textField($model,'app_name'); ?>
  <?php echo $form->error($model,'app_name'); ?>
  </div>
  
  <div class="row">
  <?php echo $form->labelEx($model,'username'); ?>
  <?php echo $form->textField($model,'username'); ?>
  <?php echo $form->error($model,'username'); ?>
  </div>
  
  <div class="row">
  <?php echo $form->labelEx($model,'password'); ?>
  <?php echo $form->passwordField($model,'password'); ?>
  <?php echo $form->error($model,'password'); ?>
  <p class="hint">
  <?php echo UserModule::t("Minimal password length 4 symbols."); ?>
  </p>
  </div>
  
  <div class="row">
  <?php echo $form->labelEx($model,'verifyPassword'); ?>
  <?php echo $form->passwordField($model,'verifyPassword'); ?>
  <?php echo $form->error($model,'verifyPassword'); ?>
  </div>
  
  <div class="row">
  <?php echo $form->labelEx($model,'email'); ?>
  <?php echo $form->textField($model,'email'); ?>
  <?php echo $form->error($model,'email'); ?>
  </div>
  
  <div class="row submit">
    <?php echo CHtml::submitButton(UserModule::t("Save")); ?>
  </div>

<?php $this->endWidget(); ?>
</div><!-- form -->
