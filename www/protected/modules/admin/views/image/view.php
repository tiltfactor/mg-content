<?php

$this->breadcrumbs = array(
  Yii::t('app', 'Admin')=>array('/admin'),
	$model->label(2) => array('admin'),
	GxHtml::valueEx($model),
);

$this->menu=array(
	array('label'=>Yii::t('app', 'Manage') . ' ' . $model->label(2), 'url'=>array('admin')),
	array('label'=>Yii::t('app', 'Update') . ' ' . $model->label(), 'url'=>array('update', 'id' => $model->id)),
	array('label'=>Yii::t('app', 'View Tag Uses for ') . ' "' . $model->name. '"', 'url'=>array('/admin/tagUse', 'TagUse[image_id]' => $model->id))
  /*
	array('label'=>Yii::t('app', 'Delete') . ' ' . $model->label(), 
    'url'=>'#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->id), 'confirm'=>'Are you sure you want to delete this item?'),
    'visible' => !($model->hasAttribute("locked") && $model->locked)), */
);
?>

<h1><?php echo Yii::t('app', 'View') . ' ' . GxHtml::encode($model->label()) . ' ' . GxHtml::encode(GxHtml::valueEx($model)); ?></h1>

<?php 
  $image_sets = array();
  if (count($model->imageSets) == 0) {
    $image_sets[] = "<li>no item(s) assigned</li>";
  }
  
  foreach($model->imageSets as $relatedModel) {
    $image_sets[] = GxHtml::link(GxHtml::encode(GxHtml::valueEx($relatedModel)), array('imageSet/view', 'id' => GxActiveRecord::extractPkValue($relatedModel, true)));
  }

$this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'cssFile' => Yii::app()->request->baseUrl . "/css/yii/detailview/styles.css",
  'attributes' => array(
  'id',
		 array(
          'name' => 'Image',
          'type' => 'image',
          'value' => Yii::app()->getBaseUrl() . Yii::app()->fbvStorage->get('settings.app_upload_url') . '/thumbs/'. $model->name,
        ),
  'size',
  'mime_type',
  'batch_id',
  'last_access',
	 array(
        'name' => 'locked',
        'type' => 'raw',
        'value' => MGHelper::itemAlias('locked',$model->locked),
      ),
    'created',
    'modified',
    array(
        'name' => Yii::t('app', 'Image Set(s)'),
        'type' => 'raw',
        'value' => join(", ", $image_sets),
      ),
	),
)); ?>
<div class="span-16 clearfix">
  <h2><?php echo Yii::t('app', 'Image is tagged with'); ?></h2>  
  <p><b><?php echo Yii::t('app', 'TAG (COUNTED/AVG WEIGHT)'); ?></b></p>
<?php 
$this->widget('zii.widgets.CListView', array(
    'id' => 'user-tags-listview',
    'dataProvider'=> Tag::model()->searchImageTags($model->id),
    'pager' => array('cssFile' => Yii::app()->request->baseUrl . "/css/yii/pager.css"),
    'itemView'=>'_viewTagListItem',
    'sortableAttributes'=>array(
        'tag' => Yii::t('app', 'Tag name'),
        'counted' => Yii::t('app', 'Counted'),
        'weight' => Yii::t('app', 'Weight'),
    ),
));  ?>

</div>

<div class="span-7 last clearfix">
  <h2><?php echo Yii::t('app', 'Tagged by'); ?></h2>  
  <p><b><?php echo Yii::t('app', 'USER NAME (TIMES TAGGED/# OF DIFFERENT TAGS)'); ?></b></p>
<?php 
$this->widget('zii.widgets.CListView', array(
    'id' => 'image-user-listview',
    'dataProvider'=> User::model()->searchImageUsers($model->id),
    'pager' => array('cssFile' => Yii::app()->request->baseUrl . "/css/yii/pager.css"),
    'itemView'=>'_viewUserListItem',
    'sortableAttributes'=>array(
        'username' => Yii::t('app', 'User name'),
        'counted' => Yii::t('app', 'Tagged Count'),
    ),
));  ?>

</div>
