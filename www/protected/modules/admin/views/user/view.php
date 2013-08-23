<?php
$this->breadcrumbs=array(
	Yii::t('app', 'Admin')=>array('/admin'),
  UserModule::t('Users')=>array('/admin/user'),
	$model->username,
);

$this->menu=array(
  array('label'=>Yii::t('app', 'Manage') . ' ' . $model->label(2), 'url'=>array('admin')),
  array('label'=>Yii::t('app', 'Create') . ' ' . $model->label(), 'url'=>array('create')),
  array('label'=>Yii::t('app', 'Update') . ' ' . $model->username, 'url'=>array('update', 'id' => $model->id)),
  array('label'=>Yii::t('app', 'Delete') . ' ' . $model->label(), 
    'url'=>'#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->id), 'confirm'=>'Are you sure you want to delete this player?'),
    'visible' => $model->canDelete()),
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

