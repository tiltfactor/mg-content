<?php

$this->breadcrumbs = array(
    Yii::t('app', 'Admin')=>array('/admin'),
    Yii::t('app', 'Import') => array('index'),
    Yii::t('app', 'Transcoding Process of Medias'),
);

?>
    <h1><?php echo Yii::t('app', 'Transcoding Process of Medias'); ?></h1>
    <p>
        You may optionally enter a comparison operator (&lt;, &lt;=, &gt;, &gt;=, &lt;&gt; or =) at the beginning of each of your filter values to specify how the comparison should be done.
    </p>

<?php

function generateImage ($data) {
    if($data->action === 'audioTranscode') {
        $return = CHtml::image(Yii::app()->getBaseUrl() . '/images/audio_ico.png', $data->action) . " <span>Audio File</span>";
    } else if($data->action === 'videoTranscode') {
        $return = CHtml::image(Yii::app()->getBaseUrl() . '/images/video_ico.png', $data->action) . " <span>Video File</span>";
    } else {
        $return = $data->action;
    }
    return $return;
}

function displayParameters ($data) {
    $parameters = json_decode($data->parameters);
    $return = "";
    foreach ($parameters as $key => $value) {
        $return.= '<b>'.stripcslashes($key).'</b>: '. stripcslashes($value).'<br />';
    }
    return $return;
}

function showStatus ($data) {
    if($data->succeeded === '1') {
        $return = CHtml::image(Yii::app()->getBaseUrl() . '/images/ok_ico.png', $data->action);
    } else {
        $return = "";
    }
    return $return;
}

function calcuateResult ($data) {
    if($data->executed_started == '') {
        $return = 'Waiting';
    } else if ($data->executed_started != '' && $data->executed_finished == '') {
        $return = 'Transcoding';
    }
    else {
        $return = $data->execution_result;
    }
    return $return;
}

$this->widget('zii.widgets.grid.CGridView', array(
    'dataProvider'=>$dataProvider,
    'columns' => array(
        array(
            'name' => 'action',
            'type'=>'html',
            'value'=>'generateImage($data)',
        ),
        'executed_started',
        array(
            'name' => 'parameters',
            'type'=>'html',
            'value'=>'displayParameters($data)',
        ),
        array(
            'name' => 'succeeded',
            'type'=>'html',
            'value'=>'showStatus($data)',
        ),
        array(
            'name' => 'execution_result',
            'type'=>'html',
            'value'=>'calcuateResult($data)',
        )
    )
));

?>