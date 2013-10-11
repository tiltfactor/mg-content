<?php $this->beginContent('//layouts/page'); ?>
<div class="container" id="page">
  <?php $this->widget('application.extensions.yii-flash.Flash', array(
    'keys'=>array('success', 'warning','error'), 
    'htmlOptions'=>array('class'=>'flash'),
  )); ?><!-- flashes -->
  
  <?php echo $content; ?>

  <div id="footer">
    Powered by <a href="http://metadatagames.com/" target="_blank">Metadata Games</a> software developed by <a href="http://www.tiltfactor.org/" target="_blank">tiltfactor</a>
  </div><!-- footer -->

</div><!-- page -->
<?php $this->endContent(); ?>
