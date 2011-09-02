<?php $this->beginContent('//layouts/main'); ?>
<div class="container arcade">
  <div class="span-16">
    <div id="content">
      <?php echo $content; ?>
    </div><!-- content -->
  </div>
  <div class="span-8 last">
    <div id="sidebar">
    <?php $this->widget('Top10Players'); ?>
    <?php if (Yii::app()->user->checkAccess('player')) :?>
    <?php $this->widget('PlayerScores'); ?>
    <h2>Your Badges</h2>
    <p>Here comes the user badges widget</p>
    <?php endif;?>
    </div><!-- sidebar -->
  </div>
</div>
<?php $this->endContent(); ?>