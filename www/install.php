<?php
// change the following paths if necessary
try {
    $now = new DateTime('now');
} catch (Exception $e) {
    echo 'Since PHP 5.1.0 (when the date/time functions were rewritten). Update your php.ini file and restart.';
    die();
}

$yii=dirname(__FILE__).'/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/installer.php';

require_once($yii);
Yii::createWebApplication($config)->run();
