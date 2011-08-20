<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'blocked-ip-form',
	'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit'=>true),
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

    <div class="row">
    <?php echo $form->labelEx($model,'ip'); ?>
    <?php echo $form->textField($model, 'ip', array('maxlength' => 45)); ?>
    <?php echo $form->error($model,'ip'); ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($model,'type'); ?>
    <?php echo $form->dropDownList($model,'type', array (
  'deny' => 'deny',
  'allow' => 'allow',
)); ?>
    <?php echo $form->error($model,'type'); ?>
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


<?php
echo GxHtml::submitButton($buttons);
$this->endWidget();
?>
</div><!-- form -->