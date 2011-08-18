<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'user-form',
	'enableAjaxValidation' => true,
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

    <div class="row">
    <?php echo $form->labelEx($model,'username'); ?>
    <?php echo $form->textField($model, 'username', array('maxlength' => 32)); ?>
    <?php echo $form->error($model,'username'); ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($model,'password'); ?>
    <?php echo $form->passwordField($model, 'password', array('maxlength' => 128)); ?>
    <?php echo $form->error($model,'password'); ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($model,'email'); ?>
    <?php echo $form->textField($model, 'email', array('maxlength' => 128)); ?>
    <?php echo $form->error($model,'email'); ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($model,'activkey'); ?>
    <?php echo $form->textField($model, 'activkey', array('maxlength' => 128)); ?>
    <?php echo $form->error($model,'activkey'); ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($model,'lastvisit'); ?>
    <?php echo $form->textField($model, 'lastvisit'); ?>
    <?php echo $form->error($model,'lastvisit'); ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($model,'role'); ?>
    <?php echo $form->textField($model, 'role', array('maxlength' => 45)); ?>
    <?php echo $form->error($model,'role'); ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($model,'status'); ?>
    <?php echo $form->textField($model, 'status'); ?>
    <?php echo $form->error($model,'status'); ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($model,'edited_count'); ?>
    <?php echo $form->textField($model, 'edited_count'); ?>
    <?php echo $form->error($model,'edited_count'); ?>
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

		<h2><?php echo GxHtml::encode($model->getRelationLabel('log')); ?></h2>
		<?php echo $form->checkBoxList($model, 'log', GxHtml::encodeEx(GxHtml::listDataEx(Log::model()->findAllAttributes(null, true)), false, true)); ?>
		<h2><?php echo GxHtml::encode($model->getRelationLabel('session')); ?></h2>
		<?php echo $form->checkBoxList($model, 'session', GxHtml::encodeEx(GxHtml::listDataEx(Session::model()->findAllAttributes(null, true)), false, true)); ?>
		<h2><?php echo GxHtml::encode($model->getRelationLabel('game')); ?></h2>
		<?php echo $form->checkBoxList($model, 'game', GxHtml::encodeEx(GxHtml::listDataEx(Game::model()->findAllAttributes(null, true)), false, true)); ?>
		<h2><?php echo GxHtml::encode($model->getRelationLabel('subjectMatter')); ?></h2>
		<?php echo $form->checkBoxList($model, 'subjectMatter', GxHtml::encodeEx(GxHtml::listDataEx(SubjectMatter::model()->findAllAttributes(null, true)), false, true)); ?>

<?php
echo GxHtml::submitButton($buttons);
$this->endWidget();
?>
</div><!-- form -->