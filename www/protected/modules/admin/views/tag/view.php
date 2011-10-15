<?php

$this->breadcrumbs = array(
  Yii::t('app', 'Admin')=>array('/admin'),
	$model->label(2) => array('admin'),
	GxHtml::valueEx($model),
);

$this->menu=array(
	array('label'=>Yii::t('app', 'Manage') . ' ' . $model->label(2), 'url'=>array('admin')),
	array('label'=>Yii::t('app', 'Update') . ' ' . $model->label(), 'url'=>array('update', 'id' => $model->id)),
	array('label'=>Yii::t('app', 'View Tag Uses for ') . ' "' . $model->tag. '"', 'url'=>array('/admin/tagUse', 'TagUse[tag_id]' => $model->id)),
	/*array('label'=>Yii::t('app', 'Delete') . ' ' . $model->label(), 
    'url'=>'#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->id), 'confirm'=>'Are you sure you want to delete this item?'),
    'visible' => !($model->hasAttribute("locked") && $model->locked)), */
);
?>

<h1><?php echo Yii::t('app', 'View') . ' ' . GxHtml::encode($model->label()) . ' "' . GxHtml::encode(GxHtml::valueEx($model)); ?>"</h1>

<?php 
$tagUseInfo = $model->tagUseInfo();
        
$this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'cssFile' => Yii::app()->request->baseUrl . "/css/yii/detailview/styles.css",
  'attributes' => array(
    'id',
    'tag',
    array(
      'name' => Yii::t('app', 'Times Used'),
      'type' => 'html',
      'value' => $tagUseInfo["use_count"],
    ),
    array(
      'name' => Yii::t('app', 'Tagged Images'),
      'type' => 'html',
      'value' => $tagUseInfo["image_count"],
    ),
    array(
      'name' => Yii::t('app', 'Average Weight'),
      'type' => 'html',
      'value' => $tagUseInfo["average"],
    ),
    array(
      'name' => Yii::t('app', 'Min Weight'),
      'type' => 'html',
      'value' => $tagUseInfo["min_weight"],
    ),
    array(
      'name' => Yii::t('app', 'Max Weight'),
      'type' => 'html',
      'value' => $tagUseInfo["max_weight"],
    ),
    'created',
    'modified',
	),
)); ?>

<div class="span-7 clearfix">
  <h2><?php echo Yii::t('app', 'Used By'); ?></h2>
  <p><b><?php echo Yii::t('app', 'USERNAME (COUNTED/ON NUMBER OF IMAGES)'); ?></b></p>
<?php 
$this->widget('zii.widgets.CListView', array(
    'id' => 'tag-user-listview',
    'dataProvider'=> User::model()->searchTagUsers($model->id),
    'pager' => array('cssFile' => Yii::app()->request->baseUrl . "/css/yii/pager.css"),
    'itemView'=>'_viewUserListItem',
    'sortableAttributes'=>array(
        'username' => Yii::t('app', 'Username'),
        'counted' => Yii::t('app', 'Counted'),
    ),
));  ?>

</div>

<div class="span-16 last clearfix">
  <h2><?php echo Yii::t('app', 'Tagged Images'); ?></h2>  
  <p><b><?php echo Yii::t('app', 'IMAGE NAME (TIMES TAGGED/BY NUMBER OF USERS)'); ?></b></p>
<?php 
$this->widget('zii.widgets.CListView', array(
    'id' => 'user-images-listview',
    'dataProvider'=>Image::model()->searchTagImages($model->id),
    'pager' => array('cssFile' => Yii::app()->request->baseUrl . "/css/yii/pager.css"),
    'itemView'=>'_viewImageListItem',
    'sortableAttributes'=>array(
        'name' => Yii::t('app', 'Image name'),
        'counted' => Yii::t('app', 'Counted')
    ),
));
?>
</div>