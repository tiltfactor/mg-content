<?php $this->beginContent('//layouts/page'); ?>
<div class="container" id="page">
  <?php $this->widget('application.extensions.yii-flash.Flash', array(
    'keys'=>array('success', 'warning','error'), 
    'htmlOptions'=>array('class'=>'flash'),
  )); ?><!-- flashes -->
  
  <?php echo $content; ?>

  <div id="footer">
    © <?php echo date('Y'); ?> <a href="http://www.tiltfactor.org/">tiltfactor</a>, all rights reserved
  </div><!-- footer -->

</div><!-- page -->
<?php $this->endContent(); ?>