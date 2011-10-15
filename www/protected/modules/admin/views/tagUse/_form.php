<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'tag-use-form',
	'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit'=>true),
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

    <div class="row">
    <?php echo $form->labelEx($model,'image_id'); ?>
    <?php echo $form->dropDownList($model, 'image_id', GxHtml::listDataEx(Image::model()->findAllAttributes(null, true))); ?>
    <?php echo $form->error($model,'image_id'); ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($model,'tag_id'); ?>
    <?php echo $form->dropDownList($model, 'tag_id', GxHtml::listDataEx(Tag::model()->findAllAttributes(null, true))); ?>
    <?php echo $form->error($model,'tag_id'); ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($model,'weight'); ?>
    <?php echo $form->textField($model, 'weight'); ?>
    <?php echo $form->error($model,'weight'); ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($model,'type'); ?>
    <?php echo $form->textField($model, 'type', array('maxlength' => 64)); ?>
    <?php echo $form->error($model,'type'); ?>
    </div><!-- row -->
    <div class="row">
    <?php if($model->created != 0) : ?>
    <?php echo $form->labelEx($model,'created'); ?>
    <?php echo $model->created; ?>
    <?php endif; ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($model,'game_submission_id'); ?>
    <?php echo $form->dropDownList($model, 'game_submission_id', GxHtml::listDataEx(GameSubmission::model()->findAllAttributes(null, true))); ?>
    <?php echo $form->error($model,'game_submission_id'); ?>
    </div><!-- row -->

		<h2><?php echo GxHtml::encode($model->getRelationLabel('tagOriginalVersions')); ?></h2>
		<div class="row clearfix">
		<?php echo $form->checkBoxList($model, 'tagOriginalVersions', GxHtml::encodeEx(GxHtml::listDataEx(TagOriginalVersion::model()->findAllAttributes(null, true)), false, true), array('template' => '<div class="checkbox">{input} {label}</div>', 'separator' => '')); ?>
		</div><!-- row -->

<?php
echo GxHtml::submitButton($buttons);
$this->endWidget();
?>
</div><!-- form -->