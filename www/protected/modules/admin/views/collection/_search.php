<div class="wide form">

<?php $form = $this->beginWidget('GxActiveForm', array(
	'action' => Yii::app()->createUrl($this->route),
	'method' => 'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model, 'name'); ?>
		<?php echo $form->textField($model, 'name', array('maxlength' => 64)); ?>
	</div>

  <div class="row">
    <?php echo $form->label($model, 'locked'); ?>
    <?php echo $form->dropDownList($model,'locked', array_merge(array(''=>Yii::t('app','All')), MGHelper::itemAlias('locked'))); ?>
  </div>
	<div class="row">
		<?php echo $form->label($model, 'more_information'); ?>
		<?php echo $form->textArea($model, 'more_information'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'licence_id'); ?>
		<?php echo $form->dropDownList($model, 'licence_id', GxHtml::listDataEx(Licence::model()->findAllAttributes(null, true)), array('prompt' => Yii::t('app', 'All'))); ?>
	</div>
  <div class="row">
    <?php echo $form->label($model, 'last_access_interval'); ?>
    <?php echo $form->textArea($model, 'last_access_interval'); ?>
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
