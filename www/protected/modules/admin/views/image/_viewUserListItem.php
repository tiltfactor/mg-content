<a class="listview-tag" href="<?php echo Yii::app()->createURL("/admin/user/view", array("id" => $data["id"])); ?>">
  <?php echo $data["username"]; ?> (<?php echo $data["counted"]; ?>/<?php echo $data["tag_counted"]; ?>)
</a>