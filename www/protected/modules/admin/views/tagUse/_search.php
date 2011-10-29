<div class="wide form">

<?php $form = $this->beginWidget('GxActiveForm', array(
	'action' => Yii::app()->createUrl($this->route),
	'method' => 'get',
)); ?>

	<div class="row">
		<?php echo CHtml::label(Yii::t('app', 'Image ID'), 'image_id'); ?>
		<?php echo $form->textField($model, 'image_id'); ?>
		<div class="description"><?php echo Yii::t('app', " (you can find the ID of an image on its view page's URL. E.g. /admin/image/view/id/233 > ID = 233 )"); ?></div>
	</div>

	<div class="row">
		<?php echo CHtml::label(Yii::t('app', 'Tag ID'), 'tag_id'); ?>
		<?php echo $form->textField($model, 'tag_id'); ?>
		<div class="description"><?php echo Yii::t('app', " (you can find the ID of an image on its view page's URL. E.g. /admin/tag/view/id/321 > ID = 321 )"); ?></div>
	</div>
  
  <div class="row">
    <?php echo CHtml::label(Yii::t('app', "Submitted By (Player Name)"), "TagUse_username") ?>
    <?php
    $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
        'name'=>'TagUse[username]',
        'value'=> ((isset($_GET["TagUse"]) && isset($_GET["TagUse"]["username"]))? $_GET["TagUse"]["username"] : ''),
        'source'=>$this->createUrl('/admin/image/searchUser'),
        'options'=>array(
                'showAnim'=>'fold',
        ),
    ));
    ?>
    <div class="description"><?php echo Yii::t('app', "(you can enter a full user name or parts of it 'a' will find all users whom's names contain 'a')"); ?></div>
  </div>
  
	<div class="row">
		<?php echo $form->label($model, 'weight'); ?>
		<?php echo $form->textField($model, 'weight'); ?>
		<div class="description"><?php echo Yii::t('app', " (you can make use of the compare operators mentioned above. E.g to find all tag uses that have a weight larger than 0 use '> 0', for all with a weight of 0 use '0')"); ?></div>
	</div>
  
  <div class="row">
    <?php echo $form->labelEx($model,'type'); ?>
    <?php echo $form->dropDownList($model,'type', TagUse::getUsedTypes(), array('prompt' => Yii::t('app', 'Please Choose'))); ?>
  </div>
  
  <div class="row">
    <?php echo $form->label($model, 'ip_address'); ?>
    <?php echo $form->textField($model, 'ip_address'); ?>
    <div class="description"><?php echo Yii::t('app', "Please enter a full ip address (e.g 89.56.34.1)"); ?></div>
  </div>
  
	<div class="row">
		<?php echo $form->label($model, 'created'); ?>
		<?php echo $form->textField($model, 'created'); ?>
	</div>

	<div class="row buttons">
		<?php echo GxHtml::submitButton(Yii::t('app', 'Search')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->
