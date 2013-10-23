<?php

$this->breadcrumbs = array(
    Yii::t('app', 'Admin') => array('/admin'),
    $model->label(2) => array('index'),
    GxHtml::valueEx($model),
);

$this->menu = array(
    array('label' => Yii::t('app', 'Manage') . ' ' . $model->label(2), 'url' => array('admin')),
    array('label' => Yii::t('app', 'Create') . ' ' . $model->label(), 'url' => array('create')),
    array('label' => Yii::t('app', 'Update') . ' ' . $model->label(), 'url' => array('update', 'id' => $model->id)),
    array('label' => Yii::t('app', 'Delete') . ' ' . $model->label(),
        'url' => '#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->id), 'confirm' => 'Are you sure you want to delete this item?'),
        'visible' => !($model->hasAttribute("locked") && $model->locked)),
);
?>

<h1><?php echo Yii::t('app', 'View') . ' ' . GxHtml::encode($model->label()) . ' ' . GxHtml::encode(GxHtml::valueEx($model)); ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
    'data' => $model,
    'cssFile' => Yii::app()->request->baseUrl . "/css/yii/detailview/styles.css",
    'attributes' => array(
        'id',
        'name',
        array(
            'name' => 'locked',
            'type' => 'raw',
            'value' => MGHelper::itemAlias('locked', $model->locked),
        ),
        'more_information',
        array(
            'name' => 'licence',
            'type' => 'raw',
            'value' => $model->licence !== null ? GxHtml::link(GxHtml::encode(GxHtml::valueEx($model->licence)), array('licence/view', 'id' => GxActiveRecord::extractPkValue($model->licence, true))) : null,
        ),
        'created',
        'modified',
        'last_access_interval',
        array(
            'name' => 'ip_restrict',
            'value' => Collection::itemAlias("Ip Restrict", $model->ip_restrict),
        ),
        array(
            'name' => Yii::t('app', 'Media'),
            'type' => 'html',
            'value' => '<b>' . Yii::t('app', 'This collection contains {count} media: ', array("{count}" => count($model->media))) . CHtml::link(Yii::t('app', 'view'), array('/admin/media/?Custom[collections][]=' . $model->id)) . '</b>',
        )
    ),
));

?>
