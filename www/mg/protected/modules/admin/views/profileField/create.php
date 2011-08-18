<?php
$this->breadcrumbs=array(
  Yii::t('app', 'Admin')=>array('/admin'),
  UserModule::t('Users')=>array('/admin/user'),
  UserModule::t('Profile Fields')=>array('admin'),
  UserModule::t('Create Profile Field')
);
$this->menu = array(
    array('label'=>UserModule::t('Manage Profile Fields'), 'url'=>array('/admin/profilefield')),
  );
  
?>

<h1><?php echo UserModule::t('Create Profile Field'); ?></h1>

<?php echo $this->renderPartial('_menu',array(
		'list'=> array(),
	)); ?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>