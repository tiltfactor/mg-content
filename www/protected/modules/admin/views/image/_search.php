<div class="wide form">

<?php $form = $this->beginWidget('GxActiveForm', array(
	'action' => Yii::app()->createUrl($this->route),
	'method' => 'get',
)); ?>
  <div class="row">
    <?php echo CHtml::label(Yii::t('app', "Tag(s)"), "Custom_tags") ?>
    <?php echo CHtml::textField("Custom[tags]", ((isset($_GET["Custom"]) && isset($_GET["Custom"]["tags"]))? $_GET["Custom"]["tags"] : '')); ?>
    <?php echo Yii::t('app', "(separate tags or phrases with a ',')"); ?>
  </div>
  <div class="row small">
    <?php echo CHtml::label(Yii::t('app', "&nbsp;"), "") ?>
    <?php echo CHtml::radioButtonList("Custom[tags_search_option]", ((isset($_GET["Custom"]) && isset($_GET["Custom"]["tags_search_option"]))? $_GET["Custom"]["tags_search_option"] : 'OR'), array("OR"=>"OR", "AND" => "AND"), array(
        'template' => '<div class="inline-radio">{input} {label}</div>',
        'separator' => '',
        )) ?><?php echo Yii::t('app', "(show images that have at least one (OR) or all (AND) of the given tags)"); ?>
  </div><!-- row -->
  
  <div class="row">
    <?php echo CHtml::label(Yii::t('app', "Image Sets(s)"), "Custom_imagesets") ?>
    <?php echo CHtml::checkBoxList("Custom[imagesets]", ((isset($_GET["Custom"]) && isset($_GET["Custom"]["imagesets"]))? $_GET["Custom"]["imagesets"] : ''), GxHtml::encodeEx(GxHtml::listDataEx(ImageSet::model()->findAllAttributes(null, true)), false, true), array(
        'template' => '<div class="checkbox">{input} {label}</div>',
        'separator' => '',
        )); ?><?php echo Yii::t('app', "(you can enter a full user name or parts of it 'a' will find all users whom's names contain 'a')"); ?>
  </div><!-- row -->
  
  <div class="row">
    <?php echo CHtml::label(Yii::t('app', "Username"), "Custom_username") ?>
    <?php
    //http://jqueryui.com/demos/autocomplete/#multiple xxx for multiple values
    $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
        'name'=>'Custom[username]',
        'value'=> ((isset($_GET["Custom"]) && isset($_GET["Custom"]["username"]))? $_GET["Custom"]["username"] : ''),
        'source'=>$this->createUrl('/admin/image/searchUser'),
        'options'=>array(
                'showAnim'=>'fold',
        ),
    ));
    ?>
  </div>
  
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
