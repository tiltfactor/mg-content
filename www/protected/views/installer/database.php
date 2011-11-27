<?php
$this->pageTitle=  Yii::app()->name . ' - Database Setup';
?>

<h1><?php echo CHtml::encode(Yii::app()->name); ?> - Database Setup</h1>

<p>
Please fill in the following database information to allow the installer to setup the database.
</p>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'database-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>
	<?php if ($error != "") : ?>
  <div id="database-form_es_" class="errorSummary" style="">
    <h2>Database Error</h2>
    <p><b>Please check your given data.</b></p>
    <p> The database responed: <?php echo $error; ?></p>
  </div>

	<?php endif; ?>
  <fieldset>
    <legend>Required Settings</legend>
  <div class="row">
    <?php echo $form->labelEx($model,'database'); ?>
    <?php echo $form->textField($model,'database'); ?>
    <?php echo $form->error($model,'database'); ?>
  </div>
  
  <div class="row">
    <?php echo $form->labelEx($model,'user'); ?>
    <?php echo $form->textField($model,'user'); ?>
    <?php echo $form->error($model,'user'); ?>
  </div>
  
  <div class="row">
    <?php echo $form->labelEx($model,'password'); ?>
    <?php echo $form->passwordField($model,'password'); ?>
    <?php echo $form->error($model,'password'); ?>
  </div>
  </fieldset>
  
  <fieldset>
    <legend>Advanced Settings</legend>
    
    <div class="row">
      <?php echo $form->labelEx($model,'tablePrefix'); ?>
      <?php echo $form->textField($model,'tablePrefix'); ?>
      <?php echo $form->error($model,'tablePrefix'); ?>
    </div>
    
    <div class="row">
      <?php echo $form->labelEx($model,'host'); ?>
      <?php echo $form->textField($model,'host'); ?>
      <?php echo $form->error($model,'host'); ?>
    </div>
  
    <div class="row">
      <?php echo $form->labelEx($model,'port'); ?>
      <?php echo $form->textField($model,'port'); ?>
      <?php echo $form->error($model,'port'); ?>
    </div>
  </fieldset>
	

	


	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

