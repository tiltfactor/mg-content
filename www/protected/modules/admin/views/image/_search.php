<div class="wide form">

<?php $form = $this->beginWidget('GxActiveForm', array(
	'action' => Yii::app()->createUrl($this->route),
	'method' => 'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model, 'name'); ?>
		<?php echo $form->textField($model, 'name', array('maxlength' => 254)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'size'); ?>
		<?php echo $form->textField($model, 'size'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'batch_id'); ?>
		<?php echo $form->textField($model, 'batch_id', array('maxlength' => 45)); ?>
	</div>
  
  <div class="row">
    <?php echo CHtml::label(Yii::t('app', "Tag(s)"), "Image_tags") ?>
    <?php echo CHtml::textField("Image[tags]", (isset($_POST["tags"]))? $_POST["tags"] : ''); ?>
    <?php echo Yii::t('app', "(separate tags or phrases with a ',')"); ?><br/>
    <?php echo CHtml::label(Yii::t('app', "Tag(s)"), "") ?>
    <?php echo CHtml::radioButtonList("Image[tags_search_option]", "OR", array("OR"=>"OR", "AND" => "AND"), array(
        'template' => '<div class="inline-radio">{input} {label}</div>',
        'separator' => '',
        )) ?><?php echo Yii::t('app', "(show images that have at least one (OR) or all (AND) of the given tags)"); ?>
  </div><!-- row -->
  
	<div class="row">
		<?php echo $form->label($model, 'last_access'); ?>
		<?php echo $form->textField($model, 'last_access'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'created'); ?>
		<?php echo $form->textField($model, 'created'); ?>
	</div>
	
	

	<div class="row">
		<?php echo $form->label($model, 'modified'); ?>
		<?php echo $form->textField($model, 'modified'); ?>
	</div>

	<div class="row buttons">
		<?php echo GxHtml::submitButton(Yii::t('app', 'Search')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->
