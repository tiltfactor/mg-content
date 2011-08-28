<?php
$this->breadcrumbs = array(
  Yii::t('app', 'Admin')=>array('/admin'),
  Yii::t('app', 'Games') => array('/games'),
  $model->name,
);

$this->menu = array(
  array('label'=>Yii::t('app', 'Manage') . ' ' . Yii::t('app', 'Games'), 'url'=>array('/games')),
  array('label' => Yii::t('app', 'Update') . ' ' . $model->name, 'url'=>array('update')),
);

?>
<h1><?php echo Yii::t('app', 'View') . ' ' . GxHtml::encode($model->name); ?></h1>
<?php $this->widget('zii.widgets.CDetailView', array(
  'data' => $game,
  'attributes' => array(
    array(
      'name' => 'active',
      'value' => MGHelper::itemAlias('active', $model->active),
    ),
    'number_played',
  ),
)); ?>
<?php $this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'attributes' => array(
    'name',
    'description',
    'arcade_image',
     array(
      'name' => 'play_once_and_move_on',
      'value' => MGHelper::itemAlias('yes-no',$model->play_once_and_move_on),
    ),
    'play_once_and_move_on_url',
    'turns',
    'score_new',
    'score_match',
    'score_expert',
    'image_width',
    'image_height',
	),
)); ?>

