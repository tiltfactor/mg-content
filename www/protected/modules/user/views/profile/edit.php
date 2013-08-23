<?php $this->pageTitle=Yii::app()->fbvStorage->get("settings.app_name") . ' - '.UserModule::t("Profile");
$this->breadcrumbs=array(
	UserModule::t("Profile")=>array('profile'),
	UserModule::t("Edit"),
);

$this->menu = array(
  array('label'=>UserModule::t('Manage Users'), 'url'=>array('/admin/user'), 'visible'=>Yii::app()->user->checkAccess('admin')),
  array('label' => UserModule::t('View Profile'), 'url'=>array('/user/profile')),
  array('label' => UserModule::t('Change password'), 'url'=>array('changepassword')),
);

?><h2><?php echo UserModule::t('Edit profile'); ?></h2>

<div class="form">
<?php $form=$this->beginWidget('UActiveForm', array(
	'id'=>'profile-form',
	'enableAjaxValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true),
	'htmlOptions' => array('enctype'=>'multipart/form-data'),
)); ?>

	<p class="note"><?php echo UserModule::t('Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary(array($model,$profile)); ?>
  
  <div class="row">
    <?php echo $form->labelEx($model,'username'); ?>
    <?php echo $form->textField($model,'username',array('size'=>20,'maxlength'=>20)); ?>
    <?php echo $form->error($model,'username'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model,'email'); ?>
    <?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>128)); ?>
    <?php echo $form->error($model,'email'); ?>
  </div>
  
<?php 
		$profileFields=$profile->getFields();
		if ($profileFields) {
			foreach($profileFields as $field) {
			?>
	<div class="row">
		<?php echo $form->labelEx($profile,$field->varname);
		
		if ($field->widgetEdit($profile)) {
			echo $field->widgetEdit($profile);
		} elseif ($field->range) {
			echo $form->dropDownList($profile,$field->varname,Profile::range($field->range));
		} elseif ($field->field_type=="TEXT") {
			echo $form->textArea($profile,$field->varname, array('rows'=>6, 'cols'=>50));
		} else {
			echo $form->textField($profile,$field->varname,array('size'=>60,'maxlength'=>(($field->field_size)?$field->field_size:255)));
		}
		echo $form->error($profile,$field->varname); ?>
	</div>	
			<?php
			}
		}
?>
<div class="row buttons">
	<?php echo CHtml::submitButton($model->isNewRecord ? UserModule::t('Create') : UserModule::t('Save')); ?>
</div>
<?php $this->endWidget(); ?>
</div><!-- form -->
