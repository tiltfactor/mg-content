<?php

$this->breadcrumbs = array(
  Yii::t('app', 'Admin')=>array('/admin'),
	$model->label(2) => array('admin'),
	GxHtml::valueEx($model),
);

$this->menu=array(
	array('label'=>Yii::t('app', 'Manage') . ' ' . $model->label(2), 'url'=>array('admin')),
	array('label'=>Yii::t('app', 'Update') . ' ' . $model->label(), 'url'=>array('update', 'id' => $model->id)),
	array('label'=>Yii::t('app', 'View Tag Uses for ') . ' "' . $model->name. '"', 'url'=>array('/admin/tagUse', 'TagUse[media_id]' => $model->id))
  /*
	array('label'=>Yii::t('app', 'Delete') . ' ' . $model->label(), 
    'url'=>'#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->id), 'confirm'=>'Are you sure you want to delete this item?'),
    'visible' => !($model->hasAttribute("locked") && $model->locked)), */
);
?>

<h1><?php echo Yii::t('app', 'View') . ' ' . GxHtml::encode($model->label()) . ' ' . GxHtml::encode(GxHtml::valueEx($model)); ?></h1>

<?php 
  $collections = array();
  if (count($model->collections) == 0) {
    $collections[] = "<li>no item(s) assigned</li>";
  }
  
  foreach($model->collections as $relatedModel) {
    $collections[] = GxHtml::link(GxHtml::encode(GxHtml::valueEx($relatedModel)), array('collection/view', 'id' => GxActiveRecord::extractPkValue($relatedModel, true)));
  }

$media_type = substr($model->mime_type, 0, 5);

if($media_type === 'image') {
    $media = CHtml::link(
        CHtml::image(Yii::app()->getBaseUrl() . Yii::app()->fbvStorage->get('settings.app_upload_url') . '/thumbs/'. $model->name) . ' [' . Yii::t('app', 'zoom') . ']',
        Yii::app()->getBaseUrl() . Yii::app()->fbvStorage->get('settings.app_upload_url') . '/images/'. $model->name,
        array('rel'=>'zoom', 'media_type'=> $media_type, 'class'=>'zoom'));
} else if($media_type === 'video') {
    $url_webm = Yii::app()->getBaseUrl() . Yii::app()->fbvStorage->get('settings.app_upload_url') . '/videos/'. urlencode($model->name);
    $url_mp4 = Yii::app()->getBaseUrl() . Yii::app()->fbvStorage->get('settings.app_upload_url') . '/videos/'. urlencode(substr($model->name, 0, -4)."mp4");
    $url_poster = Yii::app()->getBaseUrl() . Yii::app()->fbvStorage->get('settings.app_upload_url') . '/videos/'. urlencode(substr($model->name, 0, -4)."jpeg");
    $media = '<video class="video" controls preload poster="'.$url_poster.'">
            <source src="'.$url_webm.'"></source>
            <source src="'.$url_mp4.'"></source>
        </video>';
} else {
    $url_mp3 = Yii::app()->getBaseUrl() . Yii::app()->fbvStorage->get('settings.app_upload_url') . '/audios/'. urlencode($model->name);
    $url_ogg = Yii::app()->getBaseUrl() . Yii::app()->fbvStorage->get('settings.app_upload_url') . '/audios/'. urlencode(substr($model->name, 0, -3)."ogg");
    $media = '<audio class="audio" controls preload>
            <source src="'.$url_mp3.'"></source>
            <source src="'.$url_ogg.'"></source>
        </audio>';
}

//admin/media/update/id/xx
$this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'cssFile' => Yii::app()->request->baseUrl . "/css/yii/detailview/styles.css",
  'attributes' => array(
  'id',
		 array(
          'name' => 'Media',
          'type' => 'raw',
          'value' => $media
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
        'name' => Yii::t('app', 'Media Set(s)'),
        'type' => 'raw',
        'value' => join(", ", $collections),
      ),
	),
));

?>
<div class="span-16 clearfix">
  <h2><?php echo Yii::t('app', 'Media is tagged with'); ?></h2>
  <p><b><?php echo Yii::t('app', 'TAG (COUNTED/AVG WEIGHT)'); ?></b></p>
<?php 
$tagDialog = $this->widget('MGTagJuiDialog');
$this->widget('zii.widgets.CListView', array(
    'id' => 'user-tags-listview',
    'dataProvider'=> Tag::model()->searchMediaTags($model->id),
    'pager' => array('cssFile' => Yii::app()->request->baseUrl . "/css/yii/pager.css"),
    'itemView'=>'_viewTagListItem',
    'afterAjaxUpdate' => $tagDialog->gridViewUpdate(),
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
    'id' => 'media-user-listview',
    'dataProvider'=> User::model()->searchMediaUsers($model->id),
    'pager' => array('cssFile' => Yii::app()->request->baseUrl . "/css/yii/pager.css"),
    'itemView'=>'_viewUserListItem',
    'sortableAttributes'=>array(
        'username' => Yii::t('app', 'User name'),
        'counted' => Yii::t('app', 'Tagged Count'),
    ),
));  ?>

</div>
