<?php
$this->breadcrumbs=array(
	Yii::t('app', 'Admin')=>array('/admin'),
	UserModule::t('Players'),
);

$this->menu = array(
    array('label'=>UserModule::t('Manage Profile Fields'), 'url'=>array('/admin/profileField')),
    array('label'=>UserModule::t('Create') . ' ' . $model->label(), 'url'=>array('create')),
  );

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
  $('.search-form').toggle();
  return false;
});
$('.search-form form').submit(function(){
  $.fn.yiiGridView.update('users-grid', {
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

<?php

echo CHtml::beginForm('','post',array('id'=>'users-form'));
$this->widget('zii.widgets.grid.CGridView', array(
  'id' => 'users-grid',
  'dataProvider' => $model->search(),
  'filter' => $model,
  'cssFile' => Yii::app()->request->baseUrl . "/css/yii/gridview/styles.css",
  'pager' => array('cssFile' => Yii::app()->request->baseUrl . "/css/yii/pager.css"),
  'baseScriptUrl' => Yii::app()->request->baseUrl . "/css/yii/gridview",
  'selectableRows'=>2,
  'columns' => array(
    array(
      'class'=>'CCheckBoxColumn',
      'id'=>'users-ids',
    ),
    array(
      'name' => 'username',
      'cssClassExpression' => "'un'",
    ),
    'email',
    array(
      'cssClassExpression' => "'tags'",
      'header' => Yii::t('app', 'Top Tags'),
      'type' => 'html',
      'value'=>'$data->getTopTags()',
    ),
    array(
      'name'=>'role',
      'value'=>'$data->role',
      'filter'=>User::listRoles(),
    ),
    array(
      'name' => 'status',
      'type' => 'raw',
      'value' => "User::itemAlias('UserStatus', \$data->status)",
      'filter'=> User::itemAlias('UserStatus'),
    ),
    'edited_count',
    'created',
    array(
      'name' => 'lastvisit',
      'type' => 'raw',
      'value' => "((\$data->lastvisit)?\$data->lastvisit:UserModule::t('Not visited'))" 
    ),
    array (
      'class' => 'CButtonColumn',
      'buttons' => 
      array (
        'delete' => 
        array (
          'visible' => '$data->canDelete()',
        ),
      ),
    ),
  ),
)); 
echo CHtml::endForm();

$this->widget('ext.gridbatchaction.GridBatchAction', array(
      'formId'=>'users-form',
      'checkBoxId'=>'users-ids',
      'ajaxGridId'=>'users-grid', 
      'items'=>array(
          array('label'=>Yii::t('ui','Ban selected players'),'url'=>array('batch', 'op' => 'ban')),
          array('label'=>Yii::t('ui','Delete selected players'),'url'=>array('batch', 'op' => 'delete'))
      ),
      'htmlOptions'=>array('class'=>'batchActions'),
  ));
?>
