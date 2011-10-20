<?php

$this->breadcrumbs = array(
  Yii::t('app', 'Admin')=>array('/admin'),
  Yii::t('app', 'Tags')=>array('/admin/tag'),
	$tagModel->label(2) => array('admin'),
	GxHtml::valueEx($tagModel) => array('view', 'id' => GxActiveRecord::extractPkValue($tagModel, true)),
	Yii::t('app', 'Re-Weight'),
);

$this->menu = array(
	array('label'=>Yii::t('app', 'Manage') . ' ' . $tagModel->label(2), 'url'=>array('admin')),
	array('label' => Yii::t('app', 'View') . ' ' . $tagModel->label(), 'url'=>array('view', 'id' => GxActiveRecord::extractPkValue($tagModel, true))),
);
?>

<p><b>You can change with the form below the weight of all tag uses of the given tag or the tag's tag uses that have been submitted by the specified player</b></p>

<p><b>Please note: this functionality ignores tag uses with a weight of 0!</b></p>

<div class="form">
<?php $form = $this->beginWidget('GxActiveForm', array(
  'id' => 'tag-reweight-form',
  'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit'=>true),
));
?>
  <p class="note">
    <?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
  </p>

  <?php echo $form->errorSummary($formModel); ?>

    <div class="row">
    <?php echo $form->labelEx($formModel,'weight'); ?>
    <?php echo $form->textField($formModel, 'weight', array('maxlength' => 5)); ?>
    <?php echo $form->error($formModel,'weight'); ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($formModel,'user_id'); ?>
    <?php echo $form->dropDownList($formModel, 'user_id', $users, array('empty' => Yii::t('app', "please choose"))); ?>
    <?php echo $form->error($formModel,'user_id'); ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($formModel,'applyTo'); ?>
    <?php echo $form->radioButtonList($formModel, 'applyTo', $choices, array("template" => '<div class="radio">{input} {label}</div>', "separator" => '')) ?>
    <?php echo $form->error($formModel,'applyTo'); ?>
    </div><!-- row -->
<?php
echo GxHtml::submitButton();
$this->endWidget();
?>
</div><!-- form -->
