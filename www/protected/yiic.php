<?php

// change the following paths if necessary
$yii=dirname(__FILE__).'/../framework/yii.php';
$config=dirname(__FILE__).'/config/main.php';

// fix for fcgi
// defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));


// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);

// $app = Yii::createConsoleApplication($config)->run();


if(isset($config))
{
        $app=Yii::createConsoleApplication($config);
        $app->commandRunner->addCommands(YII_PATH.'/cli/commands');
}
else
        $app=Yii::createConsoleApplication(array('basePath'=>dirname(__FILE__).'/cli'));

$env=@getenv('YII_CONSOLE_COMMANDS');
if(!empty($env))
        $app->commandRunner->addCommands($env);

$app->run();