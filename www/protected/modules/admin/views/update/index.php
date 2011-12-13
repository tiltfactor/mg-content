<?php

$this->breadcrumbs = array(
	Yii::t('app', 'Admin')=>array('/admin'),
	Yii::t('app', 'Update'),
);

?>

<h1><?php echo Yii::t('app', 'Update MG'); ?></h1>

<p><b>Please click the following link if you recently updated the code base of this install and want to execute the migration code to make sure the database is updated too.</b></p>

<p><b><a href="<?php echo Yii::app()->createUrl('/admin/update/update'); ?>">UPDATE MG</a></b></p>
