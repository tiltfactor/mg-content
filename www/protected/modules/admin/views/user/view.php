<?php
$this->breadcrumbs=array(
	Yii::t('app', 'Admin')=>array('/admin'),
  UserModule::t('Players')=>array('/admin/user'),
	$model->username,
);

$this->menu=array(
  array('label'=>Yii::t('app', 'Manage') . ' ' . $model->label(2), 'url'=>array('admin')),
  array('label'=>Yii::t('app', 'Create') . ' ' . $model->label(), 'url'=>array('create')),
  array('label'=>Yii::t('app', 'Update') . ' ' . $model->username, 'url'=>array('update', 'id' => $model->id)),
  array('label'=>Yii::t('app', 'View Log for ') . ' ' . $model->username, 'url'=>array('/admin/log', 'Log[user_id]' => $model->id), 'visible' => ($model->role !== "player")),
  array('label'=>Yii::t('app', 'Delete') . ' ' . $model->label(), 'url'=>'#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->id), 'confirm'=>'Are you sure you want to delete this user?')),
);
?>

<?php 

	$attributes = array(
		'id',
		'username',
	);
	
	$profileFields=ProfileField::model()->forOwner()->sort()->findAll();
	if ($profileFields) {
		foreach($profileFields as $field) {
			array_push($attributes,array(
					'label' => UserModule::t($field->title),
					'name' => $field->varname,
					'type'=>'raw',
					'value' => (($field->widgetView($model->profile))?$field->widgetView($model->profile):(($field->range)?Profile::range($field->range,$model->profile->getAttribute($field->varname)):$model->profile->getAttribute($field->varname))),
				));
		}
	}
	// xxx the order of the fields might want to be changed.
	array_push($attributes,
		'password',
		'email',
		'activekey',
		'created',
		'modified',
    array(
      'name' => 'edited_count',
      'value' => $model->edited_count . " (xxx this functionality is not yet active)",
    ),
		array(
      'name' => 'lastvisit',
      'value' => (($model->lastvisit)?$model->lastvisit:UserModule::t('Not visited'))
    ),
		array(
			'name' => 'role',
			'value' => $model->role,
		),
		array(
			'name' => 'status',
			'value' => User::itemAlias("UserStatus",$model->status),
		)
	);
?>

<h1><?php echo Yii::t('app', 'View') . ' ' . GxHtml::encode($model->label()) . ' ' . GxHtml::encode(GxHtml::valueEx($model)); ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
  'data' => $model,
  'attributes' => $attributes
)); ?>

<h2><?php echo GxHtml::encode($model->getRelationLabel('session')); ?></h2>
<?php
  echo GxHtml::openTag('ul');
  
  if (count($model->sessions) == 0) {
    echo "<li>no item(s) assigned</li>";
  }
  
  foreach($model->sessions as $relatedModel) {
    echo GxHtml::openTag('li');
    echo GxHtml::link(GxHtml::encode(GxHtml::valueEx($relatedModel)), array('sessions/view', 'id' => GxActiveRecord::extractPkValue($relatedModel, true)));
    echo GxHtml::closeTag('li');
  }
  echo GxHtml::closeTag('ul');
?><h2><?php echo GxHtml::encode($model->getRelationLabel('game')); ?></h2>
<?php
  echo GxHtml::openTag('ul');
  
  if (count($model->games) == 0) {
    echo "<li>no item(s) assigned</li>";
  }
  
  foreach($model->games as $relatedModel) {
    echo GxHtml::openTag('li');
    echo GxHtml::link(GxHtml::encode(GxHtml::valueEx($relatedModel)), array('games/view', 'id' => GxActiveRecord::extractPkValue($relatedModel, true)));
    echo GxHtml::closeTag('li');
  }
  echo GxHtml::closeTag('ul');
?><h2><?php echo GxHtml::encode($model->getRelationLabel('subjectMatter')); ?></h2>
<?php
  echo GxHtml::openTag('ul');
  
  if (count($model->subjectMatters) == 0) {
    echo "<li>no item(s) assigned</li>";
  }
  
  foreach($model->subjectMatters as $relatedModel) {
    echo GxHtml::openTag('li');
    echo GxHtml::link(GxHtml::encode(GxHtml::valueEx($relatedModel)), array('subjectMatters/view', 'id' => GxActiveRecord::extractPkValue($relatedModel, true)));
    echo GxHtml::closeTag('li');
  }
  echo GxHtml::closeTag('ul');
?>
