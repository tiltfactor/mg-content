<?php

$this->breadcrumbs = array(
  Yii::t('app', 'Admin')=>array('/admin'),
  Yii::t('app', 'Import') => array('index'),
  Yii::t('app', 'Process Imported Images'),
);

?>

<h1><?php echo Yii::t('app', 'Process Imported Images'); ?></h1>
<p>
You may optionally enter a comparison operator (&lt;, &lt;=, &gt;, &gt;=, &lt;&gt; or =) at the beginning of each of your filter values to specify how the comparison should be done.
</p>
<div class="form">
<?php 

$form = $this->beginWidget('GxActiveForm', array(
  'action' => CHtml::normalizeUrl(array('batch', 'op' => 'process')),
  'id' => 'image-form',
));

echo $form->errorSummary($model);

$plugins = PluginsModule::getAccessiblePlugins("import");

if (count($plugins) > 0) {
  try {
    foreach ($plugins as $plugin) {
      if (method_exists($plugin->component, "form")) {
        echo $plugin->component->form($form);
      }      
    }
  } catch (Exception $e) {}
}

$this->widget('zii.widgets.grid.CGridView', array(
  'id' => 'image-grid',
  'dataProvider' => $model->unprocessed(),
  'filter' => $model,
  'cssFile' => Yii::app()->request->baseUrl . "/css/yii/gridview/styles.css",
  'pager' => array('cssFile' => Yii::app()->request->baseUrl . "/css/yii/pager.css"),
  'baseScriptUrl' => Yii::app()->request->baseUrl . "/css/yii/gridview",
  'selectableRows'=>2,
  'columns' => array(
    array(
      'class'=>'CCheckBoxColumn',
      'id'=>'image-ids',
    ),
    array(
        'name' => 'name',
        'cssClassExpression' => '"image"',
        'type'=>'html',
        'value'=>'CHtml::image(Yii::app()->getBaseUrl() . Yii::app()->fbvStorage->get(\'settings.app_upload_url\') . \'/thumbs/\'. $data->name, $data->name) . " <span>" . $data->name . "</span>"',
      ),
    'size',
    'batch_id',
    array (
  'class' => 'CButtonColumn',
  'buttons' => 
    array (
      'view' => array('visible' => '$data->locked == 1'),
      'update' => array('visible' => '$data->locked == 1'),
      'delete' => array('visible' => '$data->locked == 0'),
  ),
)  ),
)); 

  echo CHtml::tag('button', array('id' => "import-process"), Yii::t('app', 'Process images')); ?>
  <div style="float: right; margin-right: 15px"?>
    
  <?php 
    echo Yii::t('app','Selected images:');
    echo " ";
    echo CHtml::dropDownList('massProcess', 0, array(0=>Yii::t('app', "manually (with the checkboxes in the table above)"),  50=> Yii::t('app', "first ")  . 50, 100=> Yii::t('app', "first ")  . 100, 150=> Yii::t('app', "first ")  . 150, 175=> Yii::t('app', "first ")  . 175, 200=> Yii::t('app', "first ")  . 200, 225=> Yii::t('app', "first ")  . 225, 250=> Yii::t('app', "first ")  . 250, 275=> Yii::t('app', "first ")  . 275, 300=> Yii::t('app', "first ")  . 300));
    ?>
  </div>

<?php
  $this->endWidget();

  $url = CHtml::normalizeUrl(array('batch', 'op' => 'process'));
  $select_info = Yii::t('app','Please check at least one image you would like to process!');
  $process_info = Yii::t('app','Process the selected image(s)?');
  
  $javascript = <<<EOD
   jQuery('#import-process').click(function() {
    if(\$("input[name='image-ids\[\]']:checked").length==0 && \$("select#massProcess").val() == 0) {
      alert('{$select_info}');
      return false;
    }
    
    if(confirm('{$process_info}')) {
      \$('#image-form').submit();
      return true;
    } else {
      return false;
    }
  });
EOD;

  $cs=Yii::app()->getClientScript();
  $cs->registerScript('#import_batch_processs', $javascript, CClientScript::POS_END);
  
  $this->widget('ext.gridbatchaction.GridBatchAction', array(
    'formId'=>'image-form',
    'checkBoxId'=>'image-ids',
    'ajaxGridId'=>'image-grid', 
    'items'=>array(
        array('label'=>Yii::t('ui','Delete selected items'),'url'=>array('batch', 'op' => 'delete'))
    ),
    'htmlOptions'=>array('class'=>'batchActions'),
  ));?>
  

  
</div>