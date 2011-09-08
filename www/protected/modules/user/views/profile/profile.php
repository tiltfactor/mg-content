<?php $this->pageTitle=Yii::app()->fbvStorage->get("settings.app_name") . ' - '.UserModule::t("Profile");
$this->breadcrumbs=array(
	UserModule::t("Profile"),
);

$this->menu = array(
  array('label'=>UserModule::t('Manage Players'), 'url'=>array('/admin/user'), 'visible'=>Yii::app()->user->checkAccess('dbmanager')),
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
<?php
  $profileFields=ProfileField::model()->forOwner()->sort()->findAll();
  $profileFields = FALSE;
  if ($profileFields) {
    foreach($profileFields as $field) { ?>
<tr>
  <th class="label"><?php echo CHtml::encode(UserModule::t($field->title)); ?></th>
  <td><?php echo (($field->widgetView($profile))?$field->widgetView($profile):CHtml::encode((($field->range)?Profile::range($field->range,$profile->getAttribute($field->varname)):$profile->getAttribute($field->varname)))); ?></td>
</tr>
<?php
    }
  }
?>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('created')); ?></th>
    <td><?php echo $model->created; ?></td>
</tr>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('lastvisit')); ?></th>
  <td><?php echo $model->lastvisit; ?></td>
</tr>
</table>

<h2>Subject Matters</h2>
<?php $this->widget('PlayerSubjectMatter', array('user_id' => $model->id)); ?>