<?php
/**
 * A component for execute console command of Yii console application in background
 *
 * @package
 * @author
 */
class BConsoleRunner extends CComponent
{
    /**
     * Running console command on background
     * @param string $cmd argument that passed to console application
     * @param array $args refers to the extra parameters given in the command line
     * @return boolean
     */
    public function run($cmd, $args)
    {
        $logFile = Yii::app()->basePath . '/runtime/media_' . $now = date("Y-m-d_H-i-s") . '.log';
        $consoleFile = Yii::app()->basePath . '/yiic.php';
        $cmd = 'php ' . $consoleFile . ' ' . $cmd;
        if (is_array($args)) {
            foreach ($args as $arg) {
                $cmd .= ' ' . $arg;
            }
        }

        $cmd .= ' > "' . $logFile . '" 2>&1';

        if (substr(php_uname(), 0, 7) == "Windows") {
            $WshShell = new COM("WScript.Shell");
            $oExec = $WshShell->Run("%comspec% /c " . $cmd, 0, false);
        } else {
            //exec($cmd . " > /dev/null &");
            exec($cmd . " &");
        }

        return true;
    }
}
