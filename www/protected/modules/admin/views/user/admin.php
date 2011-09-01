<?php
$this->breadcrumbs=array(
	Yii::t('app', 'Admin')=>array('/admin'),
	UserModule::t('Users'),
);

$this->menu = array(
    array('label'=>UserModule::t('Create') . ' ' . $model->label(), 'url'=>array('create')),
    array('label'=>UserModule::t('Manage Profile Fields'), 'url'=>array('/admin/profileField')),
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

<?php $this->widget('zii.widgets.grid.CGridView', array(
  'id' => 'users-grid',
  'dataProvider' => $model->search(),
  'cssFile' => Yii::app()->request->baseUrl . "/css/yii/gridview/styles.css",
  'pager' => array('cssFile' => Yii::app()->request->baseUrl . "/css/yii/pager.css"),
  'filter' => $model,
  'columns' => array(
    'id',
    'username',
    'email',
    array(
      'name' => 'lastvisit',
      'type' => 'raw',
      'value' => "((\$data->lastvisit)?\$data->lastvisit:UserModule::t('Not visited'))" 
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
    'modified',
    array(
      'class' => 'CButtonColumn',
    ),
  ),
)); ?>
