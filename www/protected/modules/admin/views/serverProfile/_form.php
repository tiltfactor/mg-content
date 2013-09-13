<div class="form">


    <?php $form = $this->beginWidget('GxActiveForm', array(
    'id' => 'server-profile-form',
    'enableAjaxValidation' => false,
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
));
    ?>

    <p class="note">
        <?php echo Yii::t('app', 'Fields with'); ?> <span
        class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
    </p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'name'); ?>
        <?php echo $form->textField($model, 'name', array('maxlength' => 128)); ?>
        <?php echo $form->error($model, 'name'); ?>
    </div>
    <!-- row -->
    <div class="row">
        <?php echo $form->labelEx($model, 'logo'); ?>
        <?php echo $form->fileField($model, 'logo'); ?>
        <?php echo $form->error($model, 'logo'); ?>
    </div>
    <!-- row -->
    <div class="row">
        <?php echo $form->labelEx($model, 'url'); ?>
        <?php echo $form->textField($model, 'url'); ?>
        <?php echo $form->error($model, 'url'); ?>
    </div>
    <!-- row -->
    <div class="row">
        <?php echo $form->labelEx($model, 'description'); ?>
        <?php echo $form->textArea($model, 'description'); ?>
        <?php echo $form->error($model, 'description'); ?>
    </div>
    <!-- row -->

    <?php
    echo GxHtml::submitButton(Yii::t('app', 'Save'));
    $this->endWidget();
    ?>
</div><!-- form -->