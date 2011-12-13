<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'blocked-ip-form',
	'enableAjaxValidation' => true,
    'clientOptions'=>array('validateOnSubmit'=>true),
));
?>
  
<p>This tool enables you to blacklist (<b><i>deny</i></b> access for users accessing the site from a 
particular IP address or range) or whitelist (<b><i>allow</i></b> access from a particular IP address or range). 
You can make use of <b>*</b> as a placeholder for the number range from 0-254 and specify the IP addresses in the following way:
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
  </dl>
  </dd>
</dl>
<dl>
  <dt>To allow access only from one IP address, add the following rules:</dt>
  <dd>
    <dl>
    <dt>DENY</dt>
    <dd>*</dd>
    <dt>ALLOW</dt>
    <dd>123.123.123.123 (the IP address to allow access from)</dd>
  </dl>
  </dd>
</dl>
</p>

<p>Please make sure to not lock yourself out of the system. It is advisable to add your IP address<?php echo (isset($_SERVER['REMOTE_ADDR']))? ' (' . $_SERVER['REMOTE_ADDR'] . ')' : ''; ?> to the whitelist. 
  If something goes wrong, you must go into the database and delete all rows from the table "blocked_ip". </p>  

<p class="note">
	<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
</p>

	<?php echo $form->errorSummary($model); ?>

    <div class="row">
    <?php echo $form->labelEx($model,'ip'); ?>
    <?php echo $form->textField($model, 'ip', array('maxlength' => 45)); ?>
    <?php echo $form->error($model,'ip'); ?>
    </div><!-- row -->
    <div class="row">
    <?php echo $form->labelEx($model,'type'); ?>
    <?php echo $form->dropDownList($model,'type', array (
  'deny' => 'deny',
  'allow' => 'allow',
)); ?>
    <?php echo $form->error($model,'type'); ?>
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


<?php
echo GxHtml::submitButton($buttons);
$this->endWidget();
?>
</div><!-- form -->