<?php if ($badges) : ?>
  <?php foreach ($badges as $badge) : ?>
    <div class="badge active">
      <div><?php echo CHtml::image(Yii::app()->getBaseUrl() . Yii::app()->fbvStorage->get('settings.app_upload_url') . '/badges/'. $badge->id. '_a.png'); ?></div>
      <span><?php echo $badge->title; ?><br/>(<?php echo $badge->points; ?>) x <?php echo $badge->counted; ?></span>
    </div>
  <?php endforeach; ?>  
<?php else : ?>
  <p>No awarded basges</p>
<?php endif; ?>