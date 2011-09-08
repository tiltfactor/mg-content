<?php
$this->pageTitle=Yii::app()->fbvStorage->get("settings.app_name") . ' - Error';
$this->breadcrumbs=array(
	'Error',
);
?>

<h2>Error <?php echo $code; ?></h2>

<div class="error">
<?php echo CHtml::encode($message); ?>
</div>