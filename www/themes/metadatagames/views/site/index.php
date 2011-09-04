<?php $this->pageTitle=Yii::app()->name; ?>

<h1>Welcome</h1>

<?php if ($games) :?>
  <ul id="arcade-games">
    <?php foreach ($games as $game) :?>
    <li class="clearfix">
      <a href="<?php echo $game->url; ?>" class="image"><?php echo CHtml::image($game->image_url); ?></a>
      <h2><?php echo CHtml::link($game->name, $game->url); ?><?php echo ($game->user_score != "")? ' (' . $game->user_num_played . ' plays scoring ' . $game->user_score . ' Points)' :''; ?></h2>
      <p><?php echo $game->description;?></p>
    </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>