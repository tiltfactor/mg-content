<?php

$this->breadcrumbs = array(
  Yii::t('app', 'Admin')=>array('/admin'),
	$model->label() => array('admin'),
	GxHtml::valueEx($model),
);

$this->menu=array(
	array('label'=>Yii::t('app', 'Manage') . ' ' . $model->label(), 'url'=>array('admin')),
	array('label'=>Yii::t('app', 'Update') . ' ' . $model->label(), 'url'=>array('update', 'id' => $model->id)),
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
        CHtml::image(Yii::app()->getBaseUrl() . UPLOAD_PATH . '/thumbs/'. $model->name) . ' [' . Yii::t('app', 'zoom') . ']',
        Yii::app()->getBaseUrl() . UPLOAD_PATH . '/images/'. $model->name,
        array('rel'=>'zoom', 'media_type'=> $media_type, 'class'=>'zoom'));
} else if($media_type === 'video') {
    $url_webm = Yii::app()->getBaseUrl() . UPLOAD_PATH . '/videos/'. urlencode($model->name);
    $url_mp4 = Yii::app()->getBaseUrl() . UPLOAD_PATH . '/videos/'. urlencode(substr($model->name, 0, -4)."mp4");
    $url_poster = Yii::app()->getBaseUrl() . UPLOAD_PATH . '/videos/'. urlencode(substr($model->name, 0, -4)."jpeg");
    $media = '<video class="video" controls preload poster="'.$url_poster.'">
            <source src="'.$url_mp4.'"></source>
            <source src="'.$url_webm.'"></source>
        </video>';
} else {
    $url_mp3 = Yii::app()->getBaseUrl() . UPLOAD_PATH . '/audios/'. urlencode($model->name);
    $url_ogg = Yii::app()->getBaseUrl() . UPLOAD_PATH . '/audios/'. urlencode(substr($model->name, 0, -3)."ogg");
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
