<?php
$this->breadcrumbs=array(
	Yii::t('app', 'Admin')=>array('/admin'),
	Yii::t('app', 'Export')=>array('/admin/export'),
	yii::t('app','Exported'),
);

$this->menu = array(
    //array('label'=>UserModule::t('Manage Profile Fields'), 'url'=>array('/admin/profileField')),
    //array('label'=>UserModule::t('Create') . ' ' . $model->label(), 'url'=>array('create')),
  );

Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
  alert('submit');
  return false;
});
");

?>

<h1><?php echo Yii::t('app', 'Export Tags, Tag Uses, or Medias'); ?></h1>

<p>
You may optionally enter a comparison operator (&lt;, &lt;=, &gt;, &gt;=, &lt;&gt; or =) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::beginForm('','post',array('id'=>'export-form'));
$this->widget('zii.widgets.grid.CGridView', array(
  'id' => 'export-grid',
  'dataProvider' => $filelist_dataprovider,
  'cssFile' => Yii::app()->request->baseUrl . "/css/yii/gridview/styles.css",
  'pager' => array('cssFile' => Yii::app()->request->baseUrl . "/css/yii/pager.css"),
  'baseScriptUrl' => Yii::app()->request->baseUrl . "/css/yii/gridview",
  'columns' => array(
    'name',
    'created',
    array(
      'header' => Yii::t('app', 'Download'),
      'type' => 'html',
      'name' => 'link',
    ),
    array (
      'class' => 'CButtonColumn',
      'template' => '{export_delete}',
      'buttons' => array(
        'export_delete' => array (
          'label'=>Yii::t('app', 'remove'),     
          'url'=>'Yii::app()->controller->createUrl("remove",array("id"=>$data["name"]))',
          'imageUrl'=>Yii::app()->request->baseUrl . "/css/yii/gridview/delete.png",
          'click' => 'function() {return confirm("' . Yii::t('app', 'Are you sure you want to delete this item?') . '")}' 
        )
      )
    )  
 ),
)); 
echo CHtml::endForm();

?>
