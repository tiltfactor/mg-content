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

<p><b>You can change with the form below the weight of tag uses of the tag "<?php echo $tagModel->tag;?>".</b></p>

<p><b>Please note: this functionality ignores in the second and third option all tag uses with a weight of 0!</b></p>

<div class="form">
<?php $form = $this->beginWidget('GxActiveForm', array(
  'id' => 'tag-use-reweight-form',
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
    <?php echo $form->labelEx($formModel,'applyTo'); ?>
    <?php echo $form->radioButtonList($formModel, 'applyTo', $choices, array("template" => '<div class="radio">{input} {label}</div>', "separator" => '')) ?>
    <?php echo $form->error($formModel,'applyTo'); ?>
    </div><!-- row -->
<?php
echo GxHtml::submitButton();
$this->endWidget();
?>
</div><!-- form -->
