<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'tag-form',
	'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit'=>true),
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

    <div class="row">
    <?php echo $form->labelEx($model,'tag'); ?>
    <?php echo $form->textField($model, 'tag', array('maxlength' => 64)); ?>
    <?php echo $form->error($model,'tag'); ?>
    </div><!-- row -->
    <div class="row">
    <?php if($model->created != 0) : ?>
    <?php echo $form->labelEx($model,'created'); ?>
    <?php echo $model->created; ?>
    <?php endif; ?>
    </div><!-- row -->
    <div class="row">
    <?php if($model->modified != 0) : ?>
    <?php echo $form->labelEx($model,'modified'); ?>
    <?php echo $model->modified; ?>
    <?php endif; ?>
    </div><!-- row -->

		<h2><?php echo GxHtml::encode($model->getRelationLabel('tagUses')); ?></h2>
		<?php echo $form->checkBoxList($model, 'tagUses', GxHtml::encodeEx(GxHtml::listDataEx(TagUse::model()->findAllAttributes(null, true)), false, true)); ?>

<?php
echo GxHtml::submitButton($buttons);
$this->endWidget();
?>
</div><!-- form -->