<?php $this->pageTitle=Yii::app()->fbvStorage->get("settings.app_name") . ' - '.UserModule::t("Registration");
$this->breadcrumbs=array(
  UserModule::t("Registration"),
);
?>

<h1><?php echo UserModule::t("Registration"); ?></h1>

<p>You've successfully registered as player. <?php echo $message; ?></p>