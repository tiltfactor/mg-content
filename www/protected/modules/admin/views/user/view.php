<?php
$this->breadcrumbs=array(
	Yii::t('app', 'Admin')=>array('/admin'),
  UserModule::t('Players')=>array('/admin/user'),
	$model->username,
);

$this->menu=array(
  array('label'=>Yii::t('app', 'Manage') . ' ' . $model->label(2), 'url'=>array('admin')),
  array('label'=>UserModule::t('Manage Profile Fields'), 'url'=>array('/admin/profileField')),
  array('label'=>Yii::t('app', 'Create') . ' ' . $model->label(), 'url'=>array('create')),
  array('label'=>Yii::t('app', 'Update') . ' ' . $model->username, 'url'=>array('update', 'id' => $model->id)),
  array('label'=>Yii::t('app', 'Delete') . ' ' . $model->label(), 
    'url'=>'#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->id), 'confirm'=>'Are you sure you want to delete this player?'),
    'visible' => $model->canDelete()),
  array('label'=>Yii::t('app', 'View Tag Uses of ') . ' ' . $model->username, 'url'=>array('/admin/tagUse', 'TagUse[username]' => $model->username)),
  array('label'=>Yii::t('app', 'View Log for ') . ' ' . $model->username, 'url'=>array('/admin/log', 'Log[user_id]' => $model->id), 'visible' => ($model->role !== "player")),
);
?>

<?php 

	$attributes = array(
		'username',
		'id',
		'email',
		array(
      'name' => 'status',
      'value' => User::itemAlias("UserStatus",$model->status),
    ),
    array(
      'name' => 'role',
      'value' => $model->role,
    ),
		'created',
		'modified',
		array(
      'name' => 'lastvisit',
      'value' => (($model->lastvisit)?$model->lastvisit:UserModule::t('Not visited'))
    ),
    array(
      'name' => 'edited_count',
      'value' => $model->edited_count,
    ),
		array(
      'label' => Yii::t('app', 'Number Sessions'),
      'type' => 'html',
      'value' => '<b>' . count($model->sessions) . '</b>'
      
    )
	);
  
  $profile_attributes = array();
  
  $profileFields=ProfileField::model()->forOwner()->sort()->findAll();
  if ($profileFields) {
    foreach($profileFields as $field) {
      array_push($profile_attributes,array(
          'label' => UserModule::t($field->title),
          'name' => $field->varname,
          'type'=>'raw',
          'value' => (($field->widgetView($model->profile))?$field->widgetView($model->profile):(($field->range)?Profile::range($field->range,$model->profile->getAttribute($field->varname)):$model->profile->getAttribute($field->varname))),
        ));
    }
  }
?>

<h1><?php echo Yii::t('app', 'View') . ' ' . GxHtml::encode($model->label()) . ' ' . GxHtml::encode(GxHtml::valueEx($model)); ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
  'data' => $model,
  'attributes' => $attributes
)); ?>

<?php if (count($profile_attributes)) : ?>
  <h2><?php echo Yii::t('app', 'Profile Field Entries'); ?></h2>
  <?php $this->widget('zii.widgets.CDetailView', array(
    'data' => $model,
    'attributes' => $profile_attributes
  )); ?>
<?php endif; ?>

<div class="span-7 clearfix">
  <h2><?php echo Yii::t('app', 'Used Tags'); ?></h2>  
  <p><b><?php echo Yii::t('app', 'TAG (COUNTED/AVG WEIGHT)'); ?></b></p>

<?php 
$tagDialog = $this->widget('MGTagJuiDialog');
$this->widget('zii.widgets.CListView', array(
    'id' => 'user-tags-listview',
    'dataProvider'=> Tag::model()->searchUserTags($model->id),
    'pager' => array('cssFile' => Yii::app()->request->baseUrl . "/css/yii/pager.css"),
    'itemView'=>'_viewTagListItem',
    'afterAjaxUpdate' => $tagDialog->gridViewUpdate(),
    'sortableAttributes'=>array(
        'tag' => Yii::t('app', 'Tag name'),
        'counted' => Yii::t('app', 'Use Count'),
    ),
));  ?>

</div>

<div class="span-16 last clearfix">
  <h2><?php echo Yii::t('app', 'Tagged Images'); ?></h2>
  <p><b><?php echo Yii::t('app', 'IMAGE NAME (TIMES TAGGED BY USER/WITH NUMBER OF DIFFERENT TAGS)'); ?></b></p>  
<?php 
$this->widget('zii.widgets.CListView', array(
    'id' => 'user-images-listview',
    'dataProvider'=>Image::model()->searchUserImages($model->id),
    'pager' => array('cssFile' => Yii::app()->request->baseUrl . "/css/yii/pager.css"),
    'itemView'=>'_viewImageListItem',
    'sortableAttributes'=>array(
        'name' => Yii::t('app', 'Image name'),
        'counted' => Yii::t('app', 'Tagged Count'),
    ),
));  ?>
</div>

<h2 style="clear:both;"><?php echo GxHtml::encode($model->getRelationLabel('subjectMatters')); ?></h2>
<?php $this->widget('PlayerSubjectMatter', array('user_id' => $model->id, 'admin' => true)); ?>

<h2><?php echo GxHtml::encode($model->getRelationLabel('games')); ?></h2>
<?php $this->widget('PlayerScores', array('user_id' => $model->id, 'active' => false)); ?>

