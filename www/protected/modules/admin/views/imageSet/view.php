<?php

$this->breadcrumbs = array(
  Yii::t('app', 'Admin')=>array('/admin'),
	$model->label(2) => array('index'),
	GxHtml::valueEx($model),
);

$this->menu=array(
	array('label'=>Yii::t('app', 'Manage') . ' ' . $model->label(2), 'url'=>array('admin')),
	array('label'=>Yii::t('app', 'Create') . ' ' . $model->label(), 'url'=>array('create')),
	array('label'=>Yii::t('app', 'Update') . ' ' . $model->label(), 'url'=>array('update', 'id' => $model->id)),
	array('label'=>Yii::t('app', 'Delete') . ' ' . $model->label(), 
    'url'=>'#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->id), 'confirm'=>'Are you sure you want to delete this item?'),
    'visible' => !($model->hasAttribute("locked") && $model->locked)),
);
?>

<h1><?php echo Yii::t('app', 'View') . ' ' . GxHtml::encode($model->label()) . ' ' . GxHtml::encode(GxHtml::valueEx($model)); ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'attributes' => array(
'id',
'name',
		 array(
          'name' => 'locked',
          'type' => 'image',
          'value' => MGHelper::itemAlias('locked',$model->locked),
        ),
'more_information',
array(
			'name' => 'licence',
			'type' => 'raw',
			'value' => $model->licence !== null ? GxHtml::link(GxHtml::encode(GxHtml::valueEx($model->licence)), array('licence/view', 'id' => GxActiveRecord::extractPkValue($model->licence, true))) : null,
			),
'created',
'modified',
	),
)); ?>

<h2><?php echo GxHtml::encode($model->getRelationLabel('games')); ?></h2>
<?php
	echo GxHtml::openTag('ul');
	
	if (count($model->games) == 0) {
    echo "<li>no item(s) assigned</li>";
  }
  
	foreach($model->games as $relatedModel) {
		echo GxHtml::openTag('li');
		echo GxHtml::link(GxHtml::encode(GxHtml::valueEx($relatedModel)), array('game/view', 'id' => GxActiveRecord::extractPkValue($relatedModel, true)));
		echo GxHtml::closeTag('li');
	}
	echo GxHtml::closeTag('ul');
?><h2><?php echo GxHtml::encode($model->getRelationLabel('images')); ?></h2>
<?php
	echo GxHtml::openTag('ul');
	
	if (count($model->images) == 0) {
    echo "<li>no item(s) assigned</li>";
  }
  
	foreach($model->images as $relatedModel) {
		echo GxHtml::openTag('li');
		echo GxHtml::link(GxHtml::encode(GxHtml::valueEx($relatedModel)), array('image/view', 'id' => GxActiveRecord::extractPkValue($relatedModel, true)));
		echo GxHtml::closeTag('li');
	}
	echo GxHtml::closeTag('ul');
?><h2><?php echo GxHtml::encode($model->getRelationLabel('subjectMatters')); ?></h2>
<?php
	echo GxHtml::openTag('ul');
	
	if (count($model->subjectMatters) == 0) {
    echo "<li>no item(s) assigned</li>";
  }
  
	foreach($model->subjectMatters as $relatedModel) {
		echo GxHtml::openTag('li');
		echo GxHtml::link(GxHtml::encode(GxHtml::valueEx($relatedModel)), array('subjectMatter/view', 'id' => GxActiveRecord::extractPkValue($relatedModel, true)));
		echo GxHtml::closeTag('li');
	}
	echo GxHtml::closeTag('ul');
?>