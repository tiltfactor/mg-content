<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'media-form',
	'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit'=>true),
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

    <div class="row">
    <?php echo $form->labelEx($model,'name'); ?>
    <?php echo $model->name; ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($model,'size'); ?>
    <?php echo $model->size; ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($model,'mime_type'); ?>
    <?php echo $model->mime_type; ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($model,'batch_id'); ?>
    <?php echo $form->textField($model, 'batch_id', array('maxlength' => 45)); ?>
    <?php echo $form->error($model,'batch_id'); ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($model,'last_access'); ?>
    <?php echo $model->last_access; ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($model,'locked'); ?>
    <b><?php echo MGHelper::itemAlias('locked',$model->locked); ?>
</b>
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

		<h2><?php echo GxHtml::encode($model->getRelationLabel('collections')); ?></h2>
		<div class="row clearfix">
		<?php echo $form->checkBoxList($model, 'collections', GxHtml::encodeEx(GxHtml::listDataEx(Collection::model()->findAllAttributes(null, true)), false, true), array('template' => '<div class="checkbox">{input} {label}</div>', 'separator' => '')); ?>
		</div><!-- row -->
		<?php /*
		<h2><?php echo GxHtml::encode($model->getRelationLabel('tagUses')); ?></h2>
		<div class="row clearfix">
		<?php echo $form->checkBoxList($model, 'tagUses', GxHtml::encodeEx(GxHtml::listDataEx(TagUse::model()->findAllAttributes(null, true)), false, true), array('template' => '<div class="checkbox">{input} {label}</div>', 'separator' => '')); ?>
		</div><!-- row -->
    */
?>
<?php
echo GxHtml::submitButton($buttons);
$this->endWidget();
?>
</div><!-- form -->