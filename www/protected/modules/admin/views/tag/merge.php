<?php

$this->breadcrumbs = array(
  Yii::t('app', 'Admin')=>array('/admin'),
	$tag_from->label(2) => array('admin'),
	GxHtml::valueEx($tag_from),
);

?>

<h1><?php echo Yii::t('app', 'Merge'); ?> Tag <?php echo $tag_from->tag; ?> with <?php echo $tag_to->tag; ?></h1>

<p><b>The new tag name "<?php echo $tag_to->tag; ?>" is already in use please confirm the merge of "<?php echo $tag_from->tag; ?>" (<?php echo $tag_use_count; ?> tag uses) 
  with "<?php echo $tag_to->tag; ?>"?</b></p>

<p>All tag uses of "<?php echo $tag_from->tag; ?>" will replaced by tag uses of "<?php echo $tag_to->tag; ?>" but to each of the tag uses affected
  a tag orinal version record will be added to allow to account for this tag merge. 
</p>

<p><?php echo CHtml::link(Yii::t('app', 'cancel'), array('update', 'id' => $tag_from->id)); ?> / <?php echo CHtml::link(Yii::t('app', 'merge'), array('merge', 'from_id' => $tag_from->id, 'to_id' => $tag_to->id)); ?></p>
