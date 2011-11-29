<?php if ($badges) : ?>
  <?php foreach ($badges as $badge) : ?>
    <div class="badge <?php echo ($badge->points < $user_score)? 'active':'inactive'; ?>">
      <div><?php echo CHtml::image(Yii::app()->getBaseUrl() . Yii::app()->fbvStorage->get('settings.app_upload_url') . '/badges/'. $badge->id. '_' . (($badge->points < $user_score)? 'a':'d') . '.png'); ?></div>
      <span><?php echo $badge->title; ?><br/>(<?php echo $badge->points; ?>)</span>
    </div>
  <?php endforeach; ?>  
<?php else : ?>
  <p>You have not got any badges</p>
<?php endif; ?>