<?php

$this->breadcrumbs = array(
    Yii::t('app', 'Admin') => array('/admin'),
    $model->label(2),
);

$this->menu = array(
    array('label' => Yii::t('app', 'Create') . ' ' . $model->label(), 'url' => array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('collection-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo Yii::t('app', 'Manage') . ' ' . GxHtml::encode($model->label(2)); ?></h1>

<p>
    You may optionally enter a comparison operator (&lt;, &lt;=, &gt;, &gt;=, &lt;&gt; or =) at the beginning of each of
    your search values to specify how the comparison should be done.
</p>

<?php echo GxHtml::link(Yii::t('app', 'Advanced Search'), '#', array('class' => 'search-button')); ?>
<div class="search-form">
    <?php $this->renderPartial('_search', array(
    'model' => $model,
)); ?>
</div><!-- search-form -->

<?php echo CHtml::beginForm('', 'post', array('id' => 'collection-form'));
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'collection-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'cssFile' => Yii::app()->request->baseUrl . "/css/yii/gridview/styles.css",
    'pager' => array('cssFile' => Yii::app()->request->baseUrl . "/css/yii/pager.css"),
    'baseScriptUrl' => Yii::app()->request->baseUrl . "/css/yii/gridview",
    'selectableRows' => 2,
    'columns' => array(
        array(
            'class' => 'CCheckBoxColumn',
            'id' => 'collection-ids',
        ),
        'name',
        array(
            'name' => 'locked',
            'type' => 'raw',
            'value' => 'MGHelper::itemAlias(\'locked\',$data->locked)',
            'filter' => MGHelper::itemAlias('locked'),
        ),
        'more_information',
        array(
            'name' => 'licence_id',
            'value' => 'GxHtml::valueEx($data->licence)',
            'filter' => GxHtml::listDataEx(Licence::model()->findAllAttributes(null, true)),
        ),
        array(
            'header' => Yii::t('app', 'Medias'),
            'type' => 'raw',
            'value' => "'<b>' . Yii::t('app', '{count}&nbsp;Medias&nbsp;', array(\"{count}\" => count(\$data->medias))) . ((count(\$data->medias))? '(' . CHtml::link(Yii::t('app', 'view'), array('/admin/media/?Custom[medias][]=' . \$data->id)) . ')' : '') . '</b>'",
        ),
        //'created',
        /*
          'modified',
          */
        array(
            'name' => 'ip_restrict',
            'type' => 'raw',
            'value' => "Collection::itemAlias('Ip Restrict', \$data->ip_restrict)",
            'filter' => Collection::itemAlias('Ip Restrict'),
        ),
        array(
            'class' => 'CButtonColumn',
            'buttons' =>
            array(
                'delete' =>
                array(
                    'visible' => '$data->locked == 0',
                ),
            ),
        )),
));
echo CHtml::endForm();

$this->widget('ext.gridbatchaction.GridBatchAction', array(
    'formId' => 'collection-form',
    'checkBoxId' => 'collection-ids',
    'ajaxGridId' => 'collection-grid',
    'items' => array(
        array('label' => Yii::t('ui', 'Delete selected items'), 'url' => array('batch', 'op' => 'delete'))
    ),
    'htmlOptions' => array('class' => 'batchActions'),
));

?>