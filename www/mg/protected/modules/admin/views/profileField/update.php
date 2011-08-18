<?php
$this->breadcrumbs=array(
	Yii::t('app', 'Admin')=>array('/admin'),
  UserModule::t('Users')=>array('/admin/user'),
  UserModule::t('Profile Fields')=>array('admin'),
  $model->title=>array('view','id'=>$model->id),
	UserModule::t('Update'),
);

$this->menu = array(
  array('label'=>Yii::t('app', 'Manage') . ' ' . $model->label(2), 'url'=>array('admin')),
  array('label' => Yii::t('app', 'Create') . ' ' . $model->label(), 'url'=>array('create')),
  array('label' => Yii::t('app', 'View') . ' ' . $model->label(), 'url'=>array('view', 'id' => GxActiveRecord::extractPkValue($model, true))),
);
?>

<h1><?php echo UserModule::t('Update ProfileField ').$model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>