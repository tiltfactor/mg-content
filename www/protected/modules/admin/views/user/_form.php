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
		$profileFields=$profile->getFields();
		if ($profileFields) {
			foreach($profileFields as $field) {
			?>
	<div class="row">
		<?php echo $form->labelEx($profile,$field->varname); ?>
		<?php 
		if ($field->widgetEdit($profile)) {
			echo $field->widgetEdit($profile);
		} elseif ($field->range) {
			echo $form->dropDownList($profile,$field->varname,Profile::range($field->range));
		} elseif ($field->field_type=="TEXT") {
			echo $form->textArea($profile,$field->varname,array('rows'=>6, 'cols'=>50));
		} else {
			echo $form->textField($profile,$field->varname,array('size'=>60,'maxlength'=>(($field->field_size)?$field->field_size:255)));
		}
		 ?>
		<?php echo $form->error($profile,$field->varname); ?>
	</div>	
			<?php
			}
		}
?>

<?php if ($model->id) : ?>
  <div class="row clearfix">
  <h2><?php echo GxHtml::encode($model->getRelationLabel('subjectMatters')); ?></h2>
  <?php $this->widget('PlayerSubjectMatter', array('user_id' => $model->id, 'update' => true, 'admin' => true)); ?>
  </div><!-- row -->
<?php endif; ?>
<?php
echo GxHtml::submitButton(Yii::t('app', 'Save'));
$this->endWidget();
?>
</div><!-- form -->