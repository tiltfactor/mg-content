<a class="listview-image" href="<?php echo Yii::app()->createURL("/admin/media/view", array("id" => $data["id"])); ?>">
  <?php echo CHtml::image(Yii::app()->getBaseUrl() . UPLOAD_PATH . '/thumbs/'. $data["name"], $data["name"]);?>
  <span><?php echo $data["name"]; ?> (<?php echo $data["counted"]; ?>/<?php echo $data["tag_counted"]; ?>)</span>
</a>