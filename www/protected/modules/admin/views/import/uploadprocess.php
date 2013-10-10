<?php

$this->breadcrumbs = array(
    Yii::t('app', 'Admin') => array('/admin'),
    Yii::t('app', 'Import') => array('index'),
    Yii::t('app', 'Process Imported Media'),
);

?>

<h1><?php echo Yii::t('app', 'Process Imported Media'); ?></h1>
<p>
    You may optionally enter a comparison operator (&lt;, &lt;=, &gt;, &gt;=, &lt;&gt; or =) at the beginning of each of
    your filter values to specify how the comparison should be done.
</p>
<div class="form">
    <?php

    $form = $this->beginWidget('GxActiveForm', array(
        'action' => CHtml::normalizeUrl(array('batch', 'op' => 'process')),
        'id' => 'media-form',
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
        } catch (Exception $e) {
        }
    }

    function generateImage($data)
    {
        $media_type = substr($data->mime_type, 0, 5);

        if ($media_type === 'image') {
            $media = CHtml::image(Yii::app()->getBaseUrl() . UPLOAD_PATH . '/thumbs/' . $data->name, $data->name) . " <span>" . $data->name . "</span>";
        } else if ($media_type === 'video') {
            $media = CHtml::image(Yii::app()->getBaseUrl() . UPLOAD_PATH . '/videos/' . urlencode(substr($data->name, 0, -4)) . 'jpeg', $data->name) . " <span>" . $data->name . "</span>";
        } else {
            $media = CHtml::image(Yii::app()->getBaseUrl() . '/images/audio_ico.png', $data->name) . " <span>Audio File</span>";
        }

        return $media;
    }

    $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'media-grid',
        'dataProvider' => $model->unprocessed(),
        'filter' => $model,
        'cssFile' => Yii::app()->request->baseUrl . "/css/yii/gridview/styles.css",
        'pager' => array('cssFile' => Yii::app()->request->baseUrl . "/css/yii/pager.css"),
        'baseScriptUrl' => Yii::app()->request->baseUrl . "/css/yii/gridview",
        'selectableRows' => 2,
        'columns' => array(
            array(
                'class' => 'CCheckBoxColumn',
                'id' => 'media-ids',
            ),
            array(
                'name' => 'name',
                'cssClassExpression' => '"media"',
                'type' => 'html',
                'value' => 'generateImage($data)',
            ),
            'size',
            'batch_id',
            array(
                'class' => 'CButtonColumn',
                'buttons' =>
                array(
                    'view' => array('visible' => '$data->locked == 1'),
                    'update' => array('visible' => '$data->locked == 1'),
                    'delete' => array('visible' => '$data->locked == 0'),
                ),
            )),
    ));

    echo CHtml::tag('button', array('id' => "import-process"), Yii::t('app', 'Process media')); ?>
    <div style="float: right; margin-right: 15px"
    ?>

    <?php
    echo Yii::t('app', 'Selected media:');
    echo " ";
    echo CHtml::dropDownList('massProcess', 0, array(0 => Yii::t('app', "manually (with the checkboxes in the table above)"), 50 => Yii::t('app', "first ") . 50, 100 => Yii::t('app', "first ") . 100, 150 => Yii::t('app', "first ") . 150, 175 => Yii::t('app', "first ") . 175, 200 => Yii::t('app', "first ") . 200, 225 => Yii::t('app', "first ") . 225, 250 => Yii::t('app', "first ") . 250, 275 => Yii::t('app', "first ") . 275, 300 => Yii::t('app', "first ") . 300));
    ?>
</div>

<?php
$this->endWidget();

$url = CHtml::normalizeUrl(array('batch', 'op' => 'process'));
$select_info = Yii::t('app', 'Please check at least one media you would like to process!');
$process_info = Yii::t('app', 'Process the selected media(s)?');

$javascript = <<<EOD
   jQuery('#import-process').click(function() {
    if(\$("input[name='media-ids\[\]']:checked").length==0 && \$("select#massProcess").val() == 0) {
      alert('{$select_info}');
      return false;
    }
    
    if(confirm('{$process_info}')) {
      \$('#media-form').submit();
      return true;
    } else {
      return false;
    }
  });
EOD;

$cs = Yii::app()->getClientScript();
$cs->registerScript('#import_batch_processs', $javascript, CClientScript::POS_END);

$this->widget('ext.gridbatchaction.GridBatchAction', array(
    'formId' => 'media-form',
    'checkBoxId' => 'media-ids',
    'ajaxGridId' => 'media-grid',
    'items' => array(
        array('label' => Yii::t('ui', 'Delete selected items'), 'url' => array('batch', 'op' => 'delete'))
    ),
    'htmlOptions' => array('class' => 'batchActions'),
));?>



</div>
