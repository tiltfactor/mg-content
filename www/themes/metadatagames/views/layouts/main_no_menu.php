<?php $this->beginContent('//layouts/page'); ?>
<div class="container" id="page">
  <?php if(isset($this->breadcrumbs)):?>
    <?php $this->widget('zii.widgets.CBreadcrumbs', array(
      /*'homeLink' =>CHtml::link(Yii::t('app', 'Home'), "/"),*/
      'links'=>$this->breadcrumbs,
    )); ?><!-- breadcrumbs -->
  <?php endif?>
  <?php echo $content; ?>
  <div id="footer">
    Powered by <a href="http://metadatagames.org/" target="_blank">Metadata Games</a> software developed by <a href="http://www.tiltfactor.org/" target="_blank">tiltfactor</a>
  </div><!-- footer -->
</div><!-- page -->
<?php $this->endContent(); ?>
