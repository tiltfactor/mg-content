<div class="wide form">

<?php $form = $this->beginWidget('GxActiveForm', array(
  'id' => 'export-form',
	'action' => Yii::app()->createUrl($this->route),
	'method' => 'post',
)); 
?>
  <?php echo $form->errorSummary($model); ?>
  <?php if ($count_affected_medias > 0) : ?>
  <div class="row">
      <h4><span><b><?php print $count_affected_medias; ?></b> medias found for export</span>
      <?php echo CHtml::button(Yii::t('app', 'Export Medias'), array('id' => 'buttonExport')); ?></h4>
  </div>
  <?php endif; ?>
  
  <div class="row">
    <?php echo $form->label($model, 'filename'); ?>
    <?php echo $form->textField($model, 'filename', array('maxlength' => 128)); ?>
    <?php echo $form->error($model,'filename'); ?>
    <div class="description"><?php echo Yii::t('app', "(the file name you'll find your export under)"); ?></div>
  </div>
  
  <div class="row">
    <?php echo $form->label($model, 'collections'); ?>
    <?php echo CHtml::checkBoxList("ExportForm[collections]", ((isset($_POST["ExportForm"]) && isset($_POST["ExportForm"]["collections"]))? $_POST["ExportForm"]["collections"] : ''), GxHtml::encodeEx(GxHtml::listDataEx(Collection::model()->findAllAttributes(null, true)), false, true), array(
        'template' => '<div class="checkbox">{input} {label}</div>',
        'separator' => '',
        )); ?>
    <?php echo $form->error($model,'collections'); ?>
  </div><!-- row -->
  
  <div class="row">
    <?php echo $form->label($model, 'tags'); ?>
    <?php
    $this->widget('MGJuiAutoCompleteMultiple', array(
        'name'=>'ExportForm[tags]',
        'value'=> ((isset($_POST["ExportForm"]) && isset($_POST["ExportForm"]["tags"]))? $_POST["ExportForm"]["tags"] : ''),
        'source'=>$this->createUrl('/admin/tag/searchTags'),
        'options'=>array(
                'showAnim'=>'fold',
        ),
        'htmlOptions'=>array(
           'class'=>'whide'
       ),
    ));
    ?>
    <?php echo $form->error($model,'tags'); ?>
  </div>
  <div class="row small">
    <?php echo CHtml::label(Yii::t('app', "&nbsp;"), "") ?>
    <?php echo $form->radioButtonList($model, "tags_search_option", MGHelper::itemAlias("or-and"), array(
        'template' => '<div class="inline-radio">{input} {label}</div>',
        'separator' => '',
        )) ?>
    <?php echo Yii::t('app', "(export results that have at least one (OR) or all (AND) of the given tags)"); ?>
  </div><!-- row -->
  
  <div class="row">
    <?php echo $form->label($model, 'players'); ?>
    <?php
    $this->widget('MGJuiAutoCompleteMultiple', array(
        'name'=>'ExportForm[players]',
        'value'=> ((isset($_POST["ExportForm"]) && isset($_POST["ExportForm"]["players"]))? $_POST["ExportForm"]["players"] : ''),
        'source'=>$this->createUrl('/admin/media/searchUser'),
        'options'=>array(
                'showAnim'=>'fold',
        ),
        'htmlOptions'=>array(
          'class'=>'whide'
        ),
    ));
    ?>
    <?php echo $form->error($model,'players'); ?>
    <div class="description"><?php echo Yii::t('app', "(you can enter one or several name(s) only submissions by these players will be exported)"); ?></div>
  </div>
  <div class="row small">
    <?php echo CHtml::label(Yii::t('app', "&nbsp;"), "") ?>
    <?php echo $form->radioButtonList($model, "players_search_option", MGHelper::itemAlias("or-and"), array(
        'template' => '<div class="inline-radio">{input} {label}</div>',
        'separator' => '',
        )) ?>
    <?php echo Yii::t('app', "(export results that have been submitted by at least one (OR) or all (AND) of the given players)"); ?>
  </div><!-- row -->
  
  <div class="row">
    <?php echo $form->label($model, 'tag_weight_min'); ?>
    <?php echo $form->textField($model, 'tag_weight_min', array('maxlength' => 10)); ?>
    <?php echo $form->error($model,'tag_weight_min'); ?>
    <div class="description"><?php echo Yii::t('app', "(please enter the minimum tag weight"); ?></div>
  </div>
  <div class="row">
    <?php echo $form->label($model, 'tag_weight_sum'); ?>
    <?php echo $form->textField($model, 'tag_weight_sum', array('maxlength' => 10)); ?>
    <?php echo $form->error($model,'tag_weight_sum'); ?>
    <div class="description"><?php echo Yii::t('app', "(the minum sum of all tag use weights to make a tag elegible for export)"); ?></div>
  </div>
  
  <div class="row">
    <?php echo $form->label($model, 'created_after'); ?>
    <?php $this->widget('zii.widgets.jui.CJuiDatePicker',
        array(
            'model'=>$model, 'attribute'=>'created_after',
            'options' => array('dateFormat'=> 'yy-mm-dd'),
            'htmlOptions' => array('readonly'=>"readonly")
        )
    ); ?>
    <?php echo $form->error($model,'created_after'); ?>
    <div class="description"><?php echo Yii::t('app', "(please note the choosen date will be included)"); ?></div>
  </div>
  
  <div class="row">
    <?php echo $form->label($model, 'created_before'); ?>
    <?php $this->widget('zii.widgets.jui.CJuiDatePicker',
        array(
            'model'=>$model, 'attribute'=>'created_before',
            'options' => array('dateFormat'=> 'yy-mm-dd'),
            'htmlOptions' => array('readonly'=>"readonly")
        )
    ); ?>
    <?php echo $form->error($model,'created_before'); ?>
    <div class="description"><?php echo Yii::t('app', "(please note the choosen date will be included)"); ?></div>
  </div>
  
  <div class="row">
    <?php echo $form->label($model,'option_list_user'); ?>
    <?php echo $form->radioButtonList($model, 'option_list_user', MGHelper::itemAlias("yes-no"), array(
        'template' => '<div class="inline-radio">{input} {label}</div>',
        'separator' => '',
        )) ?>
    <?php echo $form->error($model,'option_list_user'); ?>
    <?php echo Yii::t('app', "Add submitting player names to the listings (this will increase the size of the data provided)"); ?>
  </div><!-- row -->
<?php   
  $plugins = PluginsModule::getAccessiblePlugins("export");
  
  if (count($plugins) > 0) {
    try {
      foreach ($plugins as $plugin) {
        if (method_exists($plugin->component, "form")) {
          echo $plugin->component->form($form, $model);
        }      
      }
    } catch (Exception $e) {}
  }
  ?>
  <?php echo CHtml::hiddenField('ExportForm[affected_medias][]', implode(',', $model->affected_medias)); ?>
  <?php echo CHtml::hiddenField('ExportForm[active_media]', ''); ?>
  <div class="row buttons">
    <?php echo GxHtml::submitButton(Yii::t('app', 'Check')); ?>
  </div>
<?php

 $this->endWidget(); ?>
</div><!-- export-form -->
<script type="text/javascript">
;(function () {
  var active = false;
  var medias_total = <?php echo $count_affected_medias ?>;
  var medias_processed = 0;
  var affected_medias = '';
  
  var onresponse = function (data, textStatus, jqXHR) {
        
      switch (data.status) {
        case "retry":
          medias_processed++;
          
          $('#found').text(medias_total);
          $('#processed').text(medias_processed);
          $('#left').text(medias_total - medias_processed);
          
          if (active) {
            setActiveMedia();
            $.post("<?php echo Yii::app()->createUrl('/admin/export/queueProcess'); ?>/action/export", $("#export-form").serialize(), onresponse);  
          }
          break;
          
        case "error":
          $(window).unbind('beforeunload');
          $('#mg_popup').html('<h1>Error</h1><p>' + data.message + '</p><p><a href="javascript:document.location.reload();">retry</a></p>')
          break;
          
        case "done":
          $(window).unbind('beforeunload');
          document.location.href = data.redirect;
          break;
      }
  };
  
  var setActiveMedia = function () {
    log(affected_medias);
    if (affected_medias.length > 0) {
      $('#ExportForm_active_media').val(affected_medias.pop());
    } else {
      $('#ExportForm_active_media').val(-1);
    }
    log($('#ExportForm_active_media').val());
  }
  
  var onexport = function() {
    if (!active) {
      $(window).bind('beforeunload', function () {return 'Leaving the page might disturb the exporting process.';});
      MG_API.popup('<h1>Processing Export</h1><p><span id="found">' + medias_total + '</span> medias, <span id="processed">0</span> processed, <span id="left">0</span> left</p><p>Please do not leave the page or close the browser\'s tab, or window.', {
        showCloseButton : false,
        onClosed : function () {active = false;$(window).unbind('beforeunload');}
      })
      setActiveMedia();
      active = true;
      $.post("<?php echo Yii::app()->createUrl('/admin/export/queueProcess'); ?>/action/export", $("#export-form").serialize(), onresponse);
    }
    return false;
  }
  
  $(document).ready(function () {
    MG_API.api_init({api_url:'dummy', shared_secret:'dummy'}); // bending MG_API to make it useful in this context;
    MG_API.curtain.hide();
    $('#buttonExport').hide().click(onexport);
  });
  
  $(window).load(function () {
    affected_medias = $.trim($('#ExportForm_affected_medias').val());
    
    if (affected_medias.length > 0) {
      if (affected_medias.indexOf(',') > -1) {
        affected_medias = affected_medias.split(',');
      } else {
        affected_medias = [affected_medias];
      }
    }
    
    if (affected_medias.length > 0) {
      $('#buttonExport').show();
    }
  });
})(jQuery);
</script>
