<?php
$this->breadcrumbs=array(
  Yii::t('app', 'Admin')=>array('/admin'),
	UserModule::t('Users')=>array('/admin/user'),
	UserModule::t('Create'),
);
$this->menu = array(
    array('label'=>UserModule::t('Manage') . ' ' . $model->label(2), 'url'=>array('/admin/user')),
  );
?>
<h1><?php echo UserModule::t("Create User"); ?></h1>

<?php 
	echo $this->renderPartial('_form', array('model'=>$model,'profile'=>$profile));
?>