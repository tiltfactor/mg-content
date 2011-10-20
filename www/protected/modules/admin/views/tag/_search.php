<div class="wide form">

<?php $form = $this->beginWidget('GxActiveForm', array(
	'action' => Yii::app()->createUrl($this->route),
	'method' => 'get',
)); ?>

	<div class="row">
    <?php echo $form->label($model, 'tag'); ?>
    <?php echo $form->textField($model, 'tag', array('maxlength' => 64)); ?>
  </div>
  
  <div class="row">
    <?php echo CHtml::label(Yii::t('app', "Tag Weight"), "Custom_tagweight") ?>
    <?php echo CHtml::textField('Custom[tagweight]', ((isset($_GET["Custom"]) && isset($_GET["Custom"]["tagweight"]))? $_GET["Custom"]["tagweight"] : ''), array('maxlength' => 64)); ?>
    <div class="description"><?php echo Yii::t('app', " (you can make use of the compare operators mentioned above. E.g to find all tags that have a weight larger than 0 use '> 0', for all with a weight of 0 use '0')"); ?></div>
  </div><!-- row -->
  
  <div class="row">
    <?php echo CHtml::label(Yii::t('app', "Image Sets(s)"), "Custom_imagesets") ?>
    <?php echo CHtml::checkBoxList("Custom[imagesets]", ((isset($_GET["Custom"]) && isset($_GET["Custom"]["imagesets"]))? $_GET["Custom"]["imagesets"] : ''), GxHtml::encodeEx(GxHtml::listDataEx(ImageSet::model()->findAllAttributes(null, true)), false, true), array(
        'template' => '<div class="checkbox">{input} {label}</div>',
        'separator' => '',
        )); ?>
  </div><!-- row -->
  
  <div class="row">
    <?php echo CHtml::label(Yii::t('app', "Player Name"), "Custom_username") ?>
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
    <div class="description"><?php echo Yii::t('app', " (you can enter a full user name or parts of it 'a' will find all users whom's names contain 'a')"); ?></div>
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
