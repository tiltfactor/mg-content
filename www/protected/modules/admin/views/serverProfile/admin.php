<?php

$this->breadcrumbs = array(
	$model->label(2) => array('index'),
	Yii::t('app', 'Manage'),
);

$this->menu = array(
    array('label'=>Yii::t('app', 'Create') . ' ' . $model->label(), 'url'=>array('create'),'visible' => $model->canCreate()),
	);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('server-profile-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo Yii::t('app', 'Manage') . ' ' . GxHtml::encode($model->label(2)); ?></h1>

<p>
You may optionally enter a comparison operator (&lt;, &lt;=, &gt;, &gt;=, &lt;&gt; or =) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo GxHtml::link(Yii::t('app', 'Advanced Search'), '#', array('class' => 'search-button')); ?>
<div class="search-form">
<?php $this->renderPartial('_search', array(
	'model' => $model,
)); ?>
</div><!-- search-form -->


<?php echo CHtml::beginForm('','post',array('id'=>'institution-form'));

function generateImage ($data) {
    $media = CHtml::image(Yii::app()->getBaseUrl() . UPLOAD_PATH . '/images/'. $data->logo, $data->logo,array('width'=>100,'height'=>100)) ;
    return $media;
}

$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'institution-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'cssFile' => Yii::app()->request->baseUrl . "/css/yii/gridview/styles.css",
    'pager' => array('cssFile' => Yii::app()->request->baseUrl . "/css/yii/pager.css"),
    'baseScriptUrl' => Yii::app()->request->baseUrl . "/css/yii/gridview",
    'selectableRows'=>2,
    'columns' => array(
        'name',
        array(
            'name' => 'logo',
            'cssClassExpression' => '"media"',
            'type'=>'html',
            'value'=>'generateImage($data)',
        ),
        'url',
        'website',
        'description',
        'synchronized',


        array (
            'class' => 'CButtonColumn',
            'buttons' =>
            array (
                'delete' =>
                array (
                    'visible' => '$data->canDelete()',
                ),
            ),
        )  ),
));
echo CHtml::endForm();
?>