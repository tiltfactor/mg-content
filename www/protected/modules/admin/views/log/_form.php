<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'log-form',
	'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit'=>true),
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

    <div class="row">
    <?php echo $form->labelEx($model,'category'); ?>
    <?php echo $form->textField($model, 'category', array('maxlength' => 128)); ?>
    <?php echo $form->error($model,'category'); ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($model,'message'); ?>
    <?php echo $form->textArea($model, 'message'); ?>
    <?php echo $form->error($model,'message'); ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($model,'user_id'); ?>
    <?php echo $form->dropDownList($model, 'user_id', GxHtml::listDataEx(User::model()->findAllAttributes(null, true))); ?>
    <?php echo $form->error($model,'user_id'); ?>
    </div><!-- row -->
    <div class="row">
    <?php if($model->created != 0) : ?>
    <?php echo $form->labelEx($model,'created'); ?>
    <?php echo $model->created; ?>
    <?php endif; ?>
    </div><!-- row -->


<?php
echo GxHtml::submitButton($buttons);
$this->endWidget();
?>
</div><!-- form -->