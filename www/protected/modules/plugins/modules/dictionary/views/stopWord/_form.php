<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'stop-word-form',
	'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit'=>true),
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

    <div class="row">
    <?php echo $form->labelEx($model,'word'); ?>
    <?php echo $form->textField($model, 'word', array('maxlength' => 64)); ?>
    <?php echo $form->error($model,'word'); ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($model,'counter'); ?>
    <?php echo $form->textField($model, 'counter', array('maxlength' => 10)); ?>
    <?php echo $form->error($model,'counter'); ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($model,'active'); ?>
    <?php echo $form->dropDownList($model,'active', MGHelper::itemAlias('active')); ?>
    <?php echo $form->error($model,'active'); ?>
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