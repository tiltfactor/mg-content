<div class="wide form">

<?php $form = $this->beginWidget('GxActiveForm', array(
	'action' => Yii::app()->createUrl($this->route),
	'method' => 'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model, 'id'); ?>
		<?php echo $form->textField($model, 'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'username'); ?>
		<?php echo $form->textField($model, 'username', array('maxlength' => 32)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'email'); ?>
		<?php echo $form->textField($model, 'email', array('maxlength' => 128)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'lastvisit'); ?>
		<?php echo $form->textField($model, 'lastvisit'); ?>
	</div>

	<div class="row">
    <?php echo $form->labelEx($model,'status'); ?>
    <?php echo $form->dropDownList($model,'status',User::itemAlias('UserStatus'), array('prompt' => Yii::t('app', 'Please Choose'))); ?>
    <?php echo $form->error($model,'status'); ?>
  </div>
  
  <div class="row">
    <?php echo $form->labelEx($model,'role'); ?>
    <?php echo CHtml::dropDownList('User[role]', $model->role, User::listRoles(), array('prompt' => Yii::t('app', 'Please Choose'))); ?>
    <?php echo $form->error($model,'role'); ?>
  </div>

	<div class="row">
		<?php echo $form->label($model, 'edited_count'); ?>
		<?php echo $form->textField($model, 'edited_count'); ?>
	</div>

	<div class="row buttons">
		<?php echo GxHtml::submitButton(Yii::t('app', 'Search')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->
