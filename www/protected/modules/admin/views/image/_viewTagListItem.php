<a class="listview-tag" href="<?php echo Yii::app()->createURL("/admin/tag/view", array("id" => $data["id"])); ?>">
  <?php echo $data["tag"]; ?> <span>(<?php echo $data["counted"]; ?>/<?php echo ($data["weight"] == 1)? 1 : $data["weight"]; ?>)</span>
</a>