<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'plugin-form',
	'enableAjaxValidation' => true,
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

    <div class="row">
    <?php echo $form->labelEx($model,'type'); ?>
    <?php echo $model->type; ?>
    <?php echo $form->error($model,'type'); ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($model,'unique_id'); ?>
    <?php echo $model->unique_id; ?>
    <?php echo $form->error($model,'unique_id'); ?>
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
    <div class="row">
    <?php echo CHtml::label(Yii::t('app', 'Used By Games'), 'used_by'); ?>
    <?php echo Plugin::getGamesUsingPlugin($model->id); ?>
    </div><!-- row -->
<?php
echo GxHtml::submitButton($buttons);
$this->endWidget();
?>
</div><!-- form -->