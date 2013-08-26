<div class="form">

<?php $form = $this->beginWidget('GxActiveForm', array(
  'id' => 'user-form',
  'enableAjaxValidation' => false,
    'clientOptions'=>array('validateOnSubmit'=>true),
));
?>

	<p class="note"><?php echo UserModule::t('Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>
  
  <div class="row">
    <?php echo $form->labelEx($model,'edited_count'); ?>
    <?php echo $form->textField($model,'edited_count',array('size'=>60,'maxlength'=>128)); ?>
    <?php echo $form->error($model,'edited_count'); ?>
  </div>
  

	<div class="row">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->dropDownList($model,'status',User::itemAlias('UserStatus')); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>
	
	
  <div class="row">
    <?php echo $form->labelEx($model,'role'); ?>
    <?php echo CHtml::dropDownList('User[role]', $model->role, User::listRoles()); ?>
    <?php echo $form->error($model,'role'); ?>
  </div>
<?php if ($model->id) : ?>  
  <div class="row">
    <?php echo $form->labelEx($model,'created'); ?>
    <?php echo $model->created; ?>
  </div><!-- row -->
  <div class="row">
    <?php echo $form->labelEx($model,'modified'); ?>
    <?php echo $model->modified; ?>
  </div><!-- row -->
<?php endif; ?>
<?php 
?>

<?php if ($model->id) : ?>
<?php endif; ?>
<?php
echo GxHtml::submitButton(Yii::t('app', 'Save'));
$this->endWidget();
?>
</div><!-- form -->