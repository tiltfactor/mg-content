<?php $this->beginContent('//layouts/page'); ?>
<div id="header">
  <a id="page_title" class="ir" href="<?php echo MGHelper::bu("/"); ?>"><?php echo CHtml::encode(Yii::app()->fbvStorage->get("settings.app_name")); ?></a>
</div>
<div class="container" id="page">
  <p>&nbsp;</p>
  <?php echo $content; ?>
</div><!-- page -->
<?php $this->endContent(); ?>