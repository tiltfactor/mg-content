<div class="tag-dialog">
<a class="edit ir" href="<?php echo Yii::app()->createURL("/admin/tag/view", array("id" => $data["id"])); ?>"><?php echo $data["tag"]; ?></a> 
<a class="tag" href="<?php echo Yii::app()->createURL("/admin/tag/view", array("id" => $data["id"])); ?>"><?php echo $data["tag"]; ?> <span>(<?php echo $data["counted"]; ?>/<?php echo ($data["weight"] == 1)? 1 : $data["weight"]; ?>)</span></a> 
</div>