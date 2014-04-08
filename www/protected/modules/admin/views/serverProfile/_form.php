<div class="form" xmlns="http://www.w3.org/1999/html">


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
    <div class="row expanded">
        <?php echo $form->labelEx($model, 'url'); ?>
        <?php echo $form->textField($model, 'url'); ?>
        <?php echo $form->error($model, 'url'); ?>
    </div>
    <!-- row -->
    <div class="row expanded">
        <?php echo $form->labelEx($model, 'website'); ?>
        <?php echo $form->textField($model, 'website'); ?>
        <?php echo $form->error($model, 'website'); ?>
    </div>
    <!-- row -->
    <div class="row">
        <?php echo $form->labelEx($model, 'description'); ?>
        <?php echo $form->textArea($model, 'description'); ?>
        <?php echo $form->error($model, 'description'); ?>
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
    <!-- row -->

    <?php
    echo GxHtml::submitButton(Yii::t('app', 'Save'));
    $this->endWidget();
    ?>
</div><!-- form -->