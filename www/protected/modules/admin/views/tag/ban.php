<?php

$this->breadcrumbs = array(
  Yii::t('app', 'Admin')=>array('/admin'),
	$model->label(2) => array('admin'),
	GxHtml::valueEx($model),
);

?>

<h1><?php echo Yii::t('app', 'Ban'); ?> Tag "<?php echo $model->tag; ?>"?</h1>

<p><b>Please confirm that you want to ban the tag "<?php echo $model->tag; ?>". It will set the weight of <b><?php echo $tag_use_count; ?></b> tag uses to 0 and
  put the tag on the stop word list if the plugin is activated.</p>


<p><?php echo CHtml::link(Yii::t('app', 'cancel'), array('view', 'id' => $model->id)); ?> / <?php echo CHtml::link(Yii::t('app', 'ban tag'), array('ban', 'id' => $model->id, 'banTag' => 1)); ?></p>
