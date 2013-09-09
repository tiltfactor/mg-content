<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php
echo "<?php\n
\$this->breadcrumbs = array(
  Yii::t('app', 'Admin')=>array('/admin'),
	\$model->label(2) => array('index'),
	GxHtml::valueEx(\$model),
);\n";
?>

$this->menu=array(
	array('label'=>Yii::t('app', 'Manage') . ' ' . $model->label(2), 'url'=>array('admin')),
	array('label'=>Yii::t('app', 'Create') . ' ' . $model->label(), 'url'=>array('create')),
	array('label'=>Yii::t('app', 'Update') . ' ' . $model->label(), 'url'=>array('update', 'id' => $model-><?php echo $this->tableSchema->primaryKey; ?>)),
	array('label'=>Yii::t('app', 'Delete') . ' ' . $model->label(), 
    'url'=>'#', 'linkOptions' => array('submit' => array('delete', 'id' => $model-><?php echo $this->tableSchema->primaryKey; ?>), 'confirm'=>'Are you sure you want to delete this item?'),
    'visible' => !($model->hasAttribute("locked") && $model->locked)),
);
?>

<h1><?php echo '<?php'; ?> echo Yii::t('app', 'View') . ' ' . GxHtml::encode($model->label()) . ' ' . GxHtml::encode(GxHtml::valueEx($model)); ?></h1>

<?php echo '<?php'; ?> $this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'cssFile' => Yii::app()->request->baseUrl . "/css/yii/detailview/styles.css",
  'attributes' => array(
<?php
foreach ($this->tableSchema->columns as $column) {
  switch ($column->name) {
    case "name":
      if ($this->modelClass == "Image") {
        echo "\t\t array(
          'name' => 'Image',
          'type' => 'image',
          'value' => Yii::app()->getBaseUrl() . UPLOAD_PATH . '/thumbs/'. \$model->{$column->name},
        ),\n";
      } else {
        echo $this->generateDetailViewAttribute($this->modelClass, $column) . ",\n";
      }
      break;
      
    case "active":
    case "locked":
      echo "\t\t array(
          'name' => '{$column->name}',
          'type' => 'raw',
          'value' => MGHelper::itemAlias('{$column->name}',\$model->{$column->name}),
        ),\n";
      break;
       
    default:
      echo $this->generateDetailViewAttribute($this->modelClass, $column) . ",\n";
      break;
  }  
}
?>
	),
)); ?>

<?php foreach (GxActiveRecord::model($this->modelClass)->relations() as $relationName => $relation): ?>
<?php if ($relation[0] == GxActiveRecord::HAS_MANY || $relation[0] == GxActiveRecord::MANY_MANY): ?>
<h2><?php echo '<?php'; ?> echo GxHtml::encode($model->getRelationLabel('<?php echo $relationName; ?>')); ?></h2>
<?php echo "<?php\n"; ?>
	echo GxHtml::openTag('ul');
	
	if (count($model-><?php echo $relationName; ?>) == 0) {
    echo "<li>no item(s) assigned</li>";
  }
  
	foreach($model-><?php echo $relationName; ?> as $relatedModel) {
		echo GxHtml::openTag('li');
		echo GxHtml::link(GxHtml::encode(GxHtml::valueEx($relatedModel)), array('<?php echo strtolower($relation[1][0]) . substr($relation[1], 1); ?>/view', 'id' => GxActiveRecord::extractPkValue($relatedModel, true)));
		echo GxHtml::closeTag('li');
	}
	echo GxHtml::closeTag('ul');
<?php echo '?>'; ?>
<?php endif; ?>
<?php endforeach; ?>