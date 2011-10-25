<div class="wide form">

<?php $form = $this->beginWidget('GxActiveForm', array(
	'action' => Yii::app()->createUrl($this->route),
	'method' => 'get',
)); ?>

  <div class="row">
    <?php echo CHtml::label(Yii::t('app', "Tag(s)"), "Custom_tags") ?>
    <?php echo CHtml::textField("Custom[tags]",  ((isset($_GET["Custom"]) && isset($_GET["Custom"]["tags"]))? $_GET["Custom"]["tags"] : '')); ?>
    <?php echo Yii::t('app', "(separate tags or phrases with a ',')"); ?>
  </div>
  <div class="row small">
    <?php echo CHtml::label(Yii::t('app', "&nbsp;"), "") ?>
    <?php echo CHtml::radioButtonList("Custom[tags_search_option]", ((isset($_GET["Custom"]) && isset($_GET["Custom"]["tags_search_option"]))? $_GET["Custom"]["tags_search_option"] : 'OR'), array("OR"=>"OR", "AND" => "AND"), array(
        'template' => '<div class="inline-radio">{input} {label}</div>',
        'separator' => '',
        )) ?><?php echo Yii::t('app', "(show players that have submitted at least one (OR) or all (AND) of the given tags)"); ?>
  </div><!-- row -->

	<div class="row">
		<?php echo $form->label($model, 'username'); ?>
		<?php echo $form->textField($model, 'username', array('maxlength' => 32)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'email'); ?>
		<?php echo $form->textField($model, 'email', array('maxlength' => 128)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'lastvisit'); ?>
		<?php echo $form->textField($model, 'lastvisit'); ?>
	</div>

	<div class="row">
    <?php echo $form->labelEx($model,'status'); ?>
    <?php echo $form->dropDownList($model,'status',User::itemAlias('UserStatus'), array('prompt' => Yii::t('app', 'Please Choose'))); ?>
    <?php echo $form->error($model,'status'); ?>
  </div>
  
  <div class="row">
    <?php echo $form->labelEx($model,'role'); ?>
    <?php echo CHtml::dropDownList('User[role]', $model->role, User::listRoles(), array('prompt' => Yii::t('app', 'Please Choose'))); ?>
    <?php echo $form->error($model,'role'); ?>
  </div>
  
	<div class="row">
		<?php echo $form->label($model, 'edited_count'); ?>
		<?php echo $form->textField($model, 'edited_count'); ?>
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
