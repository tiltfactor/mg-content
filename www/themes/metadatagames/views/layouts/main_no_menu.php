<?php $this->beginContent('//layouts/page'); ?>
<div class="container" id="page">
  <?php if(isset($this->breadcrumbs)):?>
    <?php $this->widget('zii.widgets.CBreadcrumbs', array(
      'homeLink' =>CHtml::link(Yii::t('app', 'Home'), "/"),
      'links'=>$this->breadcrumbs,
    )); ?><!-- breadcrumbs -->
  <?php endif?>
  <?php echo $content; ?>
  <div id="footer">
    © <?php echo date('Y'); ?> <a href="http://www.tiltfactor.org/">tiltfactor</a>, all rights reserved
  </div><!-- footer -->
</div><!-- page -->
<?php $this->endContent(); ?>