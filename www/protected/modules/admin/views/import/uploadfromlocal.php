<?php

$this->breadcrumbs = array(
  Yii::t('app', 'Admin')=>array('/admin'),
	Yii::t('app', 'Import') => array('index'),
	Yii::t('app', 'Import Images from your Computer'),
);
?>

<h1><?php echo Yii::t('app', 'Import Images from your Computer'); ?></h1>

<p><?php echo Yii::t('app', 'Hint: You can drag & drop files from your desktop on this webpage with Google Chrome, Mozilla Firefox and Apple Safari.'); ?></p>

<?php
$this->widget('ext.xupload.XUploadWidget', array(
                    'url' => Yii::app()->createUrl("admin/import/xUploadImage"),
                    'model' => $model,
                    'attribute' => 'file',
));
?>
<p><a href="/index.php/admin/import/processimportedimages"><?php echo Yii::t('app', 'Process Uploaded Images'); ?></a></p>