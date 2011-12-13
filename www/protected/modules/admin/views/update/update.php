<?php
$this->breadcrumbs = array(
  Yii::t('app', 'Admin')=>array('/admin'),
  Yii::t('app', 'Update')=>array('/admin/update'),
  Yii::t('app', 'Updated'),
);

?>

<h1><?php echo Yii::t('app', 'MG Updated'); ?></h1>

<p><b><?php echo Yii::t('app', 'Update Log'); ?></b></p>

<p><small><pre><?php echo $status; ?></pre></small></p>
