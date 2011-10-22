<?php

$this->breadcrumbs = array(
  Yii::t('app', 'Admin')=>array('/admin'),
	Yii::t('app', 'Import') => array('index'),
	Yii::t('app', "Import images that can be found on in the server's '/uploads/ftp' folder"),
);
?>

<h1><?php echo Yii::t('app', "Import images that can be found on in the server's '/uploads/ftp' folder"); ?></h1>
<p><b><?php echo $count_files ?></b> files have been found in the /uploads/ftp folder</p>

<div class="form">

<?php $form = $this->beginWidget('GxActiveForm', array(
  'id' => 'import-form',
));
?>
  <?php echo $form->errorSummary($model); ?>

  <div class="row">
  <?php echo $form->labelEx($model,'batch_id'); ?>
  <?php echo $form->textField($model, 'batch_id', array('maxlength' => 45)); ?>
  <?php echo CHtml::tag("small", array(), Yii::t('app', 'The batch id will help you to distinguish images on the import process page'), TRUE); ?>
  <?php echo $form->error($model,'batch_id'); ?>
  
  <br/><?php echo $form->hiddenField($model, 'import_per_request', array('maxlength' => 45)); ?>
  <br/><?php echo $form->hiddenField($model, 'import_processed', array('maxlength' => 45)); ?>
  <br/><?php echo $form->hiddenField($model, 'import_skipped', array('maxlength' => 45)); ?>
  </div><!-- row -->
<?php
echo GxHtml::submitButton(Yii::t('app', 'Import Images'));
$this->endWidget();
?>
</div><!-- form -->

<script type="text/javascript">
;(function ($) {
  var onresponse = function (data, textStatus, jqXHR) {
      
      switch (data.status) {
        case "retry":
          if (data.ImportFtpForm !== undefined) {
            $('#ImportFtpForm_import_processed').val(data.ImportFtpForm.import_processed);
            $('#ImportFtpForm_import_skipped').val(data.ImportFtpForm.import_skipped);  
            
            $('#left').text(data.left);
            $('#processed').text(data.ImportFtpForm.import_processed);
            $('#skipped').text(data.ImportFtpForm.import_skipped);
            $.post("<?php echo Yii::app()->createUrl('/admin/import/queueProcess'); ?>/action/ftp", $("#import-form").serialize(), onresponse);
          }
          break;
          
        case "done":
          // xxx close window here 
          document.location.href = data.redirect;
          break;
      }
  };
  
  var onsubmit = function() {
    MG_API.popup('<h1>Processing Images</h1><p><span id="found"><?php echo $count_files ?></span> found, <span id="processed">0</span> processed, <span id="skipped">0</span> skipped, <span id="left">0</span> left</p>')
    $.post("<?php echo Yii::app()->createUrl('/admin/import/queueProcess'); ?>/action/ftp", $("#import-form").serialize(), onresponse);
    return false;
  }
  
  $(document).ready(function () {
    MG_API.api_init({api_url:'dummy', shared_secret:'dummy'}); // bending MG_API to make it useful in this context;
    MG_API.curtain.hide();
    $('#import-form').unbind('submit').submit(onsubmit); 
  });
})(jQuery);
</script>
