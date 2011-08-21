<?php

$this->breadcrumbs = array(
  Yii::t('app', 'Admin')=>array('/admin'),
	Yii::t('app', 'Import')
);

$this->menu = array(
	array('label'=>Yii::t('app', 'Import'), 'url'=>array('index')),
);
?>

<h1><?php echo Yii::t('app', 'Import'); ?></h1>

<p>This is the Admin Overview. It lists all tools you have got access to.</p>

<?php foreach ($tools as $id => $tool) : ?>
<div class="tool" id="<?php echo $id; ?>">
  <h3><a href="<?php echo $tool["url"]; ?>"><?php echo $tool["name"]; ?></a></h3>
  <p><?php echo $tool["description"]; ?></p>
</div>
<?php endforeach;?>