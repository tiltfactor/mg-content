<?php
$this->breadcrumbs=array(
	Yii::t('app', 'Admin')=>array('/admin'),
  UserModule::t('Users')=>array('/admin/user'),
  UserModule::t('Profile Fields')=>array('admin'),
  UserModule::t($model->title),
);


$this->menu=array(
  array('label'=>Yii::t('app', 'Manage') . ' ' . $model->label(2), 'url'=>array('admin')),
  array('label'=>Yii::t('app', 'Create') . ' ' . $model->label(), 'url'=>array('create')),
  array('label'=>Yii::t('app', 'Update') . ' ' . $model->label(), 'url'=>array('update', 'id' => $model->id)),
  array('label'=>Yii::t('app', 'Delete') . ' ' . $model->label(), 'url'=>'#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->id), 'confirm'=>'Are you sure you want to delete this user?')),
);
?>
<h1><?php echo UserModule::t('Profile Field: ').$model->varname; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'varname',
		'title',
		'field_type',
		'field_size',
		'field_size_min',
		'required',
		'match',
		'range',
		'error_message',
		'other_validator',
		'widget',
		'widgetparams',
		'default',
		'position',
		'visible',
	),
)); ?>
