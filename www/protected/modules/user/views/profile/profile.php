<?php $this->pageTitle=Yii::app()->fbvStorage->get("settings.app_name") . ' - '.UserModule::t("Profile");
$this->breadcrumbs=array(
	UserModule::t("Profile"),
);

$this->menu = array(
  array('label'=>UserModule::t('Manage Users'), 'url'=>array('/admin/user'), 'visible'=>Yii::app()->user->checkAccess(ADMIN)),
  array('label' => UserModule::t('Edit Profile'), 'url'=>array('profile/edit')),
);
?><h2><?php echo UserModule::t('Your profile'); ?></h2>

<table class="dataGrid">
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('username')); ?></th>
  <td><?php echo CHtml::encode($model->username); ?></td>
</tr>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('email')); ?></th>
    <td><?php echo CHtml::encode($model->email); ?>
</td>
</tr>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('created')); ?></th>
    <td><?php echo $model->created; ?></td>
</tr>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('lastvisit')); ?></th>
  <td><?php echo $model->lastvisit; ?></td>
</tr>
</table>

