<?php $this->beginContent('//layouts/main'); ?>
<div class="container" class="clearfix">
  <div class="span-16">
    <div id="content">
      <?php echo $content; ?>
    </div><!-- content -->
  </div>
  <div class="span-8 last">
    <div id="sidebar">
    <h2>Top User</h2>
    <p>Here comes the top user score widget</p>
    <?php if (Yii::app()->user->checkAccess('player')) :?>
    <h2>Your Scores</h2>
    <p>Here comes the user score widget</p>
    <h2>Your Badges</h2>
    <p>Here comes the user badges widget</p>
    <?php endif;?>
    </div><!-- sidebar -->
  </div>
</div>
<?php $this->endContent(); ?>