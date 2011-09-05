<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<div class="form">

<?php $ajax = ($this->enable_ajax_validation) ? 'true' : 'false'; ?>

<?php echo '<?php '; ?>
$form = $this->beginWidget('GxActiveForm', array(
	'id' => '<?php echo $this->class2id($this->modelClass); ?>-form',
	'enableAjaxValidation' => <?php echo $ajax; ?>,
    'clientOptions'=>array('validateOnSubmit'=>true),
));
<?php echo '?>'; ?>


	<p class="note">
		<?php echo "<?php echo Yii::t('app', 'Fields with'); ?> <span class=\"required\">*</span> <?php echo Yii::t('app', 'are required'); ?>"; ?>.
	</p>

	<?php echo "<?php echo \$form->errorSummary(\$model); ?>\n"; ?>

<?php foreach ($this->tableSchema->columns as $column): ?>
<?php if (!$column->autoIncrement): 
  switch ($column->name) {
    case "modified":
    case "created":
      ?>
    <div class="row">
    <?php echo "<?php if(\$model->{$column->name} != 0) : ?>\n"; ?>
    <?php echo "<?php echo " . $this->generateActiveLabel($this->modelClass, $column) . "; ?>\n"; ?>
    <?php echo "<?php echo \$model->{$column->name}; ?>\n"; ?>
    <?php echo "<?php endif; ?>\n"; ?>
    </div><!-- row -->
<?php
      break;
    case "active":
      ?>
    <div class="row">
    <?php echo "<?php echo " . $this->generateActiveLabel($this->modelClass, $column) . "; ?>\n"; ?>
    <?php echo "<?php echo \$form->dropDownList(\$model,'{$column->name}', MGHelper::itemAlias({$column->name})); ?>\n"; ?>
    <?php echo "<?php echo \$form->error(\$model,'{$column->name}'); ?>\n"; ?>
    </div><!-- row -->
<?php
      break;
    case "locked":
      ?>
    <div class="row">
    <?php echo "<?php echo " . $this->generateActiveLabel($this->modelClass, $column) . "; ?>\n"; ?>
    <b><?php echo "<?php echo MGHelper::itemAlias('{$column->name}',\$model->locked); ?>\n"; ?></b>
    </div><!-- row -->
<?php
      break;
    default:
      if (strpos($column->dbType, "enum") !== FALSE) { // this is a special handler for mysql enum column types
        $arr_list = array();
        $arr = explode(",", str_replace(array("enum", "ENUM", "(", ")", "'"), "", $column->dbType));
        foreach ($arr as $option) {
          $arr_list[$option] = Yii::t('app', $option); 
        }
        $arr = var_export($arr_list, TRUE);
        ?>
    <div class="row">
    <?php echo "<?php echo " . $this->generateActiveLabel($this->modelClass, $column) . "; ?>\n"; ?>
    <?php echo "<?php echo \$form->dropDownList(\$model,'{$column->name}', $arr); ?>\n"; ?>
    <?php echo "<?php echo \$form->error(\$model,'{$column->name}'); ?>\n"; ?>
    </div><!-- row -->
<?php } else { ?>
    <div class="row">
    <?php echo "<?php echo " . $this->generateActiveLabel($this->modelClass, $column) . "; ?>\n"; ?>
    <?php echo "<?php " . $this->generateActiveField($this->modelClass, $column) . "; ?>\n"; ?>
    <?php echo "<?php echo \$form->error(\$model,'{$column->name}'); ?>\n"; ?>
    </div><!-- row -->
<?php } 
      break;
  }
endif; ?>
<?php endforeach; ?>

<?php foreach ($this->getRelations($this->modelClass) as $relation): ?>
<?php if ($relation[1] == GxActiveRecord::HAS_MANY || $relation[1] == GxActiveRecord::MANY_MANY): ?>
		<h2><?php echo '<?php'; ?> echo GxHtml::encode($model->getRelationLabel('<?php echo $relation[0]; ?>')); ?></h2>
		<div class="row clearfix">
		<?php echo '<?php '  . $this->generateActiveRelationField($this->modelClass, $relation) ."; ?>\n"; ?>
		</div><!-- row -->
<?php endif; ?>
<?php endforeach; ?>

<?php echo "<?php
echo GxHtml::submitButton(\$buttons);
\$this->endWidget();
?>\n"; ?>
</div><!-- form -->