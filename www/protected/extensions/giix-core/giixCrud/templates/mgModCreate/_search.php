<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<div class="wide form">

<?php echo "<?php \$form = \$this->beginWidget('GxActiveForm', array(
	'action' => Yii::app()->createUrl(\$this->route),
	'method' => 'get',
)); ?>\n"; ?>

<?php foreach($this->tableSchema->columns as $column): 
  switch ($column->name) :
    case "active":
      ?>
  <div class="row">
    <?php echo "<?php echo \$form->label(\$model, '{$column->name}'); ?>\n"; ?>
    <?php echo "<?php echo \$form->dropDownList(\$model,'{$column->name}', MGHelper::itemAlias('active')); ?>\n"; ?>
  </div>
<?php 
      break;
      
    default:
  $field = $this->generateInputField($this->modelClass, $column);
	if (strpos($field, 'password') !== false)
		continue;
?>
	<div class="row">
		<?php echo "<?php echo \$form->label(\$model, '{$column->name}'); ?>\n"; ?>
		<?php echo "<?php " . $this->generateSearchField($this->modelClass, $column)."; ?>\n"; ?>
	</div>

<?php 
  endswitch;
endforeach; ?>
	<div class="row buttons">
		<?php echo "<?php echo GxHtml::submitButton(Yii::t('app', 'Search')); ?>\n"; ?>
	</div>

<?php echo "<?php \$this->endWidget(); ?>\n"; ?>

</div><!-- search-form -->
