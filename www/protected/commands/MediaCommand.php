<?php
/**
 *
 * @package
 * @author     Nikolay Kondikov<nikolay.kondikov@sirma.bg>
 */
class MediaCommand  extends CConsoleCommand
{
    public function actionHelp(){}

    public function actionIndex(){
        if($this->isLocked()) die("Already running.\n");
        //$start = $this->getMicrotime();

        $now = new DateTime('now', new DateTimeZone('GMT'));
        $now = $now->format("Y-m-d H:i:s");

        $jobs = CronJob::model()->findAll('execute_after <:now AND executed_started IS NULL ORDER BY id ASC', array(':now'=>$now));


        for($i=0;$i<count($jobs); $i++){
            $job = $jobs[$i];
            echo "Processing Job " . $job->id . "\r\n";

            if(method_exists($this, $job->action)){
                $executed_at = new DateTime('now', new DateTimeZone('GMT'));
                $job->executed_started = $executed_at->format('Y-m-d H:i:s');
                $job->save();
                $result = $this->{$job->action}($job->parameters);

                if($result === false){
                    // do nothing, let the next cycle pick it up
                    continue;
                }else{
                    $executed_at = new DateTime('now', new DateTimeZone('GMT'));
                    $job->succeeded = $result['succeeded'] ? 1 : 0;
                    $job->execution_result = array_key_exists('execution_result', $result) ? $result['execution_result'] : "";
                    $job->executed_finished = $executed_at->format('Y-m-d H:i:s');
                    $job->save();
                }
            }else{
                $executed_at = new DateTime('now', new DateTimeZone('GMT'));
                $job->executed_finished = $executed_at->format('Y-m-d H:i:s');
                $job->succeeded = 0;
                $job->execution_result = 'Action does not exist.';
                $job->save();
            }
        }

        $this->releaseLock();
    }


    public function videoTranscode($params){

    }

    public function audioTranscode($params){
        ob_start();
        var_dump($params ." ".time());
        $data = ob_get_clean();
        $fp = fopen("d:/MediaCommand.txt", "a");
        fwrite($fp, $data);
        fclose($fp);

        sleep(10);

        ob_start();
        var_dump(time());
        $data = ob_get_clean();
        $fp = fopen("d:/MediaCommand.txt", "a");
        fwrite($fp, $data);
        fclose($fp);
    }

    private function getMicrotime(){
        $time = microtime();
        $time = explode(' ', $time);
        $time = $time[1] + $time[0];
        return $time;
    }

    private function totalTime($start){
        $finish = $this->getMicrotime();
        return round(($finish - $start), 4);
    }



    private function isLocked(){
        $lockFile=Yii::app()->getRuntimePath().DIRECTORY_SEPARATOR.'media.lock';
        if(file_exists( $lockFile ))
        {
            $lockingPID = trim( file_get_contents( $lockFile ) );
            $output=array();
            if(strncmp(PHP_OS,'WIN',3)===0)
            {
                exec('tasklist /FI "PID eq ' . $lockingPID . '"',$output);
                if(count($output)>1) return true;
            }
            else
            {
                if(file_exists('/proc/'.$lockingPID)) return true;

                $pids = explode( "\n", trim( `ps -e | awk '{print $1}'` ) );
                # If PID is still active, return true
                if( in_array( $lockingPID, $pids ) )  return true;
            }
            unlink( $lockFile );
        }

        file_put_contents( $lockFile, getmypid() . "\n" );
        return false;
    }

    private function releaseLock(){
        $lockFile=Yii::app()->getRuntimePath().DIRECTORY_SEPARATOR.'media.lock';
        unlink( $lockFile );
    }
}
