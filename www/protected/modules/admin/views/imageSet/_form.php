<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'image-set-form',
	'enableAjaxValidation' => true,
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

    <div class="row">
    <?php echo $form->labelEx($model,'name'); ?>
    <?php echo $form->textField($model, 'name', array('maxlength' => 64)); ?>
    <?php echo $form->error($model,'name'); ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($model,'locked'); ?>
    <?php echo $form->textField($model, 'locked'); ?>
    <?php echo $form->error($model,'locked'); ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($model,'more_information'); ?>
    <?php echo $form->textArea($model, 'more_information'); ?>
    <?php echo $form->error($model,'more_information'); ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($model,'licence_id'); ?>
    <?php echo $form->dropDownList($model, 'licence_id', GxHtml::listDataEx(Licence::model()->findAllAttributes(null, true))); ?>
    <?php echo $form->error($model,'licence_id'); ?>
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

		<h2><?php echo GxHtml::encode($model->getRelationLabel('games')); ?></h2>
		<?php echo $form->checkBoxList($model, 'games', GxHtml::encodeEx(GxHtml::listDataEx(Game::model()->findAllAttributes(null, true)), false, true)); ?>
		<h2><?php echo GxHtml::encode($model->getRelationLabel('images')); ?></h2>
		<?php echo $form->checkBoxList($model, 'images', GxHtml::encodeEx(GxHtml::listDataEx(Image::model()->findAllAttributes(null, true)), false, true)); ?>
		<h2><?php echo GxHtml::encode($model->getRelationLabel('subjectMatters')); ?></h2>
		<?php echo $form->checkBoxList($model, 'subjectMatters', GxHtml::encodeEx(GxHtml::listDataEx(SubjectMatter::model()->findAllAttributes(null, true)), false, true)); ?>

<?php
echo GxHtml::submitButton($buttons);
$this->endWidget();
?>
</div><!-- form -->