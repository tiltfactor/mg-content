<?php $this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Profile");
$this->breadcrumbs=array(
	UserModule::t("Profile"),
);
?><h2><?php echo UserModule::t('Your profile'); ?></h2>
<ul class="actions">
<?php echo $this->renderPartial('menu'); ?>
<li><?php echo CHtml::link(UserModule::t('Change password'),array('changepassword')); ?></li>
</ul>

<?php if(Yii::app()->user->hasFlash('profileMessage')): ?>
<div class="success">
<?php echo Yii::app()->user->getFlash('profileMessage'); ?>
</div>
<?php endif; ?>
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
