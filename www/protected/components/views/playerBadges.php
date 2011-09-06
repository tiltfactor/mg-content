<?php if ($badges) : ?>
  <?php foreach ($badges as $badge) : ?>
    <div class="badge <?php echo ($badge->points < $user_score)? 'active':'inactive'; ?>">
      <div id="badge-id-<?php echo $badge->id; ?>"></div>
      <span><?php echo $badge->title; ?><br/>(<?php echo $badge->points; ?>)</span>
    </div>
  <?php endforeach; ?>  
<?php else : ?>
  <p>You have not got any badges</p>
<?php endif; ?>