<?php $this->beginContent('//layouts/main'); ?>
<div class="container" class="clearfix">
  <div class="span-16">
    <div id="content">
      <?php echo $content; ?>
    </div><!-- content -->
  </div>
  <div class="span-8 last">
    <div id="sidebar">
    <?php $this->widget('Top10Players'); ?>
    <?php if (Yii::app()->user->isGuest) :?>
      <?php $this->widget('AwardedBadges'); ?>
    <?php else : ?>
      <?php $this->widget('PlayerScores'); ?>
      <?php $this->widget('PlayerBadges'); ?>
    <?php endif;?>
    </div><!-- sidebar -->
  </div>
</div>
<?php $this->endContent(); ?>