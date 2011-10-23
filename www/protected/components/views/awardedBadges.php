<?php if ($badges) : ?>
  <?php foreach ($badges as $badge) : ?>
    <div class="badge active">
      <div id="badge-id-<?php echo $badge->id; ?>"></div>
      <span><?php echo $badge->title; ?><br/>(<?php echo $badge->points; ?>) x <?php echo $badge->counted; ?></span>
    </div>
  <?php endforeach; ?>  
<?php else : ?>
  <p>No awarded basges</p>
<?php endif; ?>