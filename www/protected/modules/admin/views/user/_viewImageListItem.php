<a class="listview-image" href="<?php echo Yii::app()->createURL("/admin/image/view", array("id" => $data["id"])); ?>">
  <?php echo CHtml::image(Yii::app()->getBaseUrl() . Yii::app()->fbvStorage->get('settings.app_upload_url') . '/thumbs/'. $data["name"], $data["name"]);?>
  <span><?php echo $data["name"]; ?> (<?php echo $data["counted"]; ?>/<?php echo $data["tag_counted"]; ?>)</span>
</a>