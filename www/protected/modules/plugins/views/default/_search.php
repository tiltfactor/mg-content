<div class="wide form">

<?php $form = $this->beginWidget('GxActiveForm', array(
	'action' => Yii::app()->createUrl($this->route),
	'method' => 'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model, 'type'); ?>
		<?php echo $form->dropDownList($model,'type', $type_filter); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'active'); ?>
		<?php echo $form->dropDownList($model,'active', Plugin::itemAlias('active')); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'unique_id'); ?>
		<?php echo $form->textField($model, 'unique_id', array('maxlength' => 254)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'created'); ?>
		<?php echo $form->textField($model, 'created'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'modified'); ?>
		<?php echo $form->textField($model, 'modified'); ?>
	</div>

	<div class="row buttons">
		<?php echo GxHtml::submitButton(Yii::t('app', 'Search')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->
