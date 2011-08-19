<?php 
if(UserModule::isAssigned('dbmanager')) {
?>
<li><?php echo CHtml::link(UserModule::t('Manage User'),array('/user/admin')); ?></li>
<?php 
}
?>
<li><?php echo CHtml::link(UserModule::t('Edit'),array('edit')); ?></li>