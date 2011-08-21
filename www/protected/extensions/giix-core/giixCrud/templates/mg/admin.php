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
	\$model->label(2),
);\n";
?>

$this->menu = array(
		array('label'=>Yii::t('app', 'Create') . ' ' . $model->label(), 'url'=>array('create')),
	);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('<?php echo $this->class2id($this->modelClass); ?>-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo '<?php'; ?> echo Yii::t('app', 'Manage') . ' ' . GxHtml::encode($model->label(2)); ?></h1>

<p>
You may optionally enter a comparison operator (&lt;, &lt;=, &gt;, &gt;=, &lt;&gt; or =) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo "<?php echo GxHtml::link(Yii::t('app', 'Advanced Search'), '#', array('class' => 'search-button')); ?>"; ?>

<div class="search-form">
<?php echo "<?php \$this->renderPartial('_search', array(
	'model' => \$model,
)); ?>\n"; ?>
</div><!-- search-form -->

<?php echo '<?php'; ?> $this->widget('zii.widgets.grid.CGridView', array(
	'id' => '<?php echo $this->class2id($this->modelClass); ?>-grid',
	'dataProvider' => $model->search(),
	'filter' => $model,
	'columns' => array(
<?php
$count = 0;
$arr_buttons = array('class' => 'CButtonColumn', "buttons"=> array());
foreach ($this->tableSchema->columns as $column) {
	if (++$count == 7)
		echo "\t\t/*\n";
  
  switch ($column->name) {
    case "active":
    case "locked":
       echo "\t\t array(
        'name' => '{$column->name}',
        'type' => 'raw',
        'value' => 'MGHelper::itemAlias(\'{$column->name}\',\$data->{$column->name})',
        'filter'=> MGHelper::itemAlias('{$column->name}'),
      ),\n";
      
      if ($column->name == "locked") {
        $arr_buttons["buttons"]['delete'] = array ('visible'=>'$data->locked == 0');
      }
      break;
    default:
      echo "\t\t" . $this->generateGridViewColumn($this->modelClass, $column).",\n";    
      break;
  }
}
if ($count >= 7)
	echo "\t\t*/\n";
?>
    <?php echo var_export($arr_buttons, TRUE) ; ?>
  ),
)); ?>