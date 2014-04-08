<?php
$this->pageTitle = Yii::app()->name . ' - Admin Account Setup';
?>

<h1><?php echo CHtml::encode(Yii::app()->name); ?> - Admin Account Setup and MG Game Build Registration</h1>

<div class="form">
    <?php $form = $this->beginWidget('UActiveForm', array(
    'id' => 'installconfiguration-form',
    'enableAjaxValidation' => false,
    'clientOptions' => array('validateOnSubmit' => false),
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

    <?php if($error){?>
    <div class="flash-error"><?php echo $error; ?></div>
    <?php }?>

    <p class="note"><?php echo UserModule::t('Fields with <span class="required">*</span> are required.'); ?></p>

    <?php echo $form->errorSummary(array($model)); ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'app_name'); ?>
        <?php echo $form->textField($model, 'app_name'); ?>
        <?php echo $form->error($model, 'app_name'); ?>
        <p class="hint">
            <?php echo UserModule::t("Name of your organization."); ?>
        </p>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'logo'); ?>
        <?php echo $form->fileField($model, 'logo'); ?>
        <?php echo $form->error($model, 'logo'); ?>
    </div>
    <!-- row -->

    <div class="row expanded">
        <?php echo $form->labelEx($model, 'url'); ?>
        <?php echo $form->textField($model, 'url'); ?>
        <?php echo $form->error($model, 'url'); ?>
        <p class="hint">
            <?php echo UserModule::t("Example: http://yourdomain.com/metadatagames/www/"); ?>
            <br />
            <?php echo UserModule::t("(don't forget to add www/ to your URL!)"); ?>
        </p>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'website'); ?>
        <?php echo $form->textField($model, 'website'); ?>
        <?php echo $form->error($model, 'website'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'description'); ?>
        <?php echo $form->textArea($model, 'description'); ?>
        <?php echo $form->error($model, 'description'); ?>
        <p class="hint">
            <?php echo UserModule::t("Server Description"); ?>
        </p>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'username'); ?>
        <?php echo $form->textField($model, 'username'); ?>
        <?php echo $form->error($model, 'username'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'password'); ?>
        <?php echo $form->passwordField($model, 'password'); ?>
        <?php echo $form->error($model, 'password'); ?>
        <p class="hint">
            <?php echo UserModule::t("Minimal password length 4 symbols."); ?>
        </p>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'verifyPassword'); ?>
        <?php echo $form->passwordField($model, 'verifyPassword'); ?>
        <?php echo $form->error($model, 'verifyPassword'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'email'); ?>
        <?php echo $form->textField($model, 'email'); ?>
        <?php echo $form->error($model, 'email'); ?>
    </div>

    <p>Institution's IP address range.<br>
        You can make use of <b>*</b> as a placeholder for the number range from 0-254 and specify the IP addresses in
        the following way:
    <dl>
        <dt>Single IP Address</dt>
        <dd>123.123.123.123</dd>
        <dt>Multiple IP Addresses</dt>
        <dd>
            <dl>
                <dt>123.123.123.*</dt>
                <dd>For all IP Addresses in the range from 123.123.123.1 to 123.123.123.254</dd>
                <dt>123.123.*</dt>
                <dd>For all IP Addresses between 123.123.0.1 and 123.123.254.254</dd>
                <dt>123.123.123.123,124.124.124.124</dt>
                <dd>You can enter multiple ip addresses separated by comma</dd>
            </dl>
        </dd>
    </dl>
    </p>

    <div class="row">
        <?php echo $form->labelEx($model, 'ip'); ?>
        <?php echo $form->textField($model, 'ip'); ?>
        <?php echo $form->error($model, 'ip'); ?>
    </div>


    <div class="row submit">
        <?php echo CHtml::submitButton(UserModule::t("Save")); ?>
    </div>

    <?php $this->endWidget(); ?>
</div><!-- form -->
