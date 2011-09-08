<?php
$this->breadcrumbs=array(
  Yii::t('app', 'Admin')=>array('/admin'),
  UserModule::t('Players')=>array('/admin/user'),
  $model->username=>array('view','id'=>$model->id),
  (UserModule::t('Update')),
);


$this->menu = array(
  array('label'=>Yii::t('app', 'Manage') . ' ' . $model->label(2), 'url'=>array('admin')),
  array('label' => Yii::t('app', 'Create') . ' ' . $model->label(), 'url'=>array('create')),
  array('label'=>Yii::t('app', 'View Log for ') . ' ' . $model->username, 'url'=>array('/admin/log', 'Log[user_id]' => $model->id), 'visible' => ($model->role !== "player")),
  array('label' => Yii::t('app', 'View') . ' ' . $model->username, 'url'=>array('view', 'id' => GxActiveRecord::extractPkValue($model, true))),
);


?>
<h1><?php echo  UserModule::t('Update User')." ".$model->id; ?></h1>

<?php 
	echo $this->renderPartial('_form', array('model'=>$model,'profile'=>$profile)); ?>