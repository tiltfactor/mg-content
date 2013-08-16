<?php
Yii::import("application.commands.MediaParameters");
/**
 *
 * @package
 * @author     Nikolay Kondikov<nikolay.kondikov@sirma.bg>
 */
class MediaCommand extends CConsoleCommand
{

    public $uploadPath;
    public $audioPath;
    public $videoPath;

    public function actionHelp()
    {
        echo <<<EOD


Usage: yiic media index

Note:
Uploaded files must be located in `settings.app_upload_path`
The process will use `settings.app_upload_path` as base path
and will add video files in `videos` subfolder and audio files
in `audios` subfolder

Create record for each audio and video by using CronJob model
The followings are the available columns in table 'cron_jobs':
 * @property integer id
 * @property string execute_after
 * @property string executed_started
 * @property string executed_finished
 * @property string action
 * @property string parameters
 * @property string execution_result

 Set follow fields:
 execute_after - time after the file should be process
 action - available options are audioTranscode and videoTranscode
 parameters - json string of object MediaParameters

 Run the command from yii web application:
 \$runner = new BConsoleRunner();
 \$runner->run("media",array("index"));




EOD;
    }

    /**
     *
     */
    public function actionIndex()
    {
        if ($this->isLocked()) die("Already running.\n");
        $this->initFolders();


        $jobs = $this->getJobs();
        while(count($jobs)>0){
            for ($i = 0; $i < count($jobs); $i++) {
                $job = $jobs[$i];
                echo "Processing Job " . $job->id . "\r\n";

                if (method_exists($this, $job->action)) {
                    $executed_at = date("Y-m-d H:i:s");
                    $job->executed_started = $executed_at;
                    $job->save();
                    try {
                        $params = MediaParameters::createFromJson($job->parameters);
                        $this->{$job->action}($params);
                        $executed_at = date("Y-m-d H:i:s");
                        $job->succeeded = 1;
                        $job->execution_result = "Done";
                        $job->executed_finished = $executed_at;
                        $job->save();
                    } catch (CException $e) {
                        $job->succeeded = 0;
                        $job->executed_finished = $executed_at;
                        $job->execution_result = $e->getMessage();
                        $job->save();
                        echo "CException:".$e->getMessage()."\r\n";
                    }
                } else {
                    $executed_at = date("Y-m-d H:i:s");
                    $job->executed_finished = $executed_at;
                    $job->succeeded = 0;
                    $job->execution_result = 'Action does not exist.';
                    $job->save();
                    echo $job->action.": Action does not exist \r\n";
                }
            }
            $jobs = $this->getJobs();
        }

        echo "Finished at: " . date("Y-m-d H:i:s") . "\r\n";
        $this->releaseLock();
    }

    private function getJobs(){
        $now = date("Y-m-d H:i:s",time()+10); //add 10 seconds in future to prevent race condition
        echo "Get jobs at: " . $now . "\r\n";
        return CronJob::model()->findAll('execute_after <=:now AND executed_finished IS NULL ORDER BY id ASC', array(':now' => $now));
    }

    /**
     *
     * @param MediaParameters $params
     * @throws CException
     */
    public function videoTranscode($params)
    {
        if (!is_file($this->uploadPath . "/" . $params->filename)) {
            throw new CException("File not exists: " . $this->uploadPath . "/" . $params->filename);
        }

        $info = $this->getInfo($params->filename);
        $isWebm = false;
        $isMpeg4 = false;
        $resolution = "640x480";
        $duration = 0;
        $size = 0;
        $ext = pathinfo($this->uploadPath . "/" . $params->filename, PATHINFO_EXTENSION);
        $file = basename($params->filename, "." . $ext);

        if (isset($info) && isset($info->streams[0])) {
            if ($info->streams[0]->codec_name == "vp8" && $info->streams[0]->width == 640) {
                copy($this->uploadPath . "/" . $params->filename, $this->videoPath . "/" . $params->filename);
                $isWebm = true;
            }
            if ($info->streams[0]->codec_name == "h264" && $info->streams[0]->width == 640) {
                copy($this->uploadPath . "/" . $params->filename, $this->videoPath . "/" . $params->filename);
                $isMpeg4 = true;
            }
            if (isset($info->streams[0]->display_aspect_ratio) && $info->streams[0]->display_aspect_ratio == "16:9") {
                $resolution = "640x360";
            } else if (isset($info->streams[0]->display_aspect_ratio) && $info->streams[0]->display_aspect_ratio == "4:3") {
                $resolution = "640x480";
            }
            $duration = $info->format->duration;
            $size = $info->format->size;
        } else {
            throw new CException(" Invalid data found when processing input: " . $this->uploadPath . "/" . $params->filename);
        }

        if (!$isWebm) {
            $this->convertToWebm($this->uploadPath, $params->filename, $this->videoPath, $resolution);
        }

        if (!$isMpeg4) {
            $this->convertToMpeg4($this->uploadPath, $params->filename, $this->videoPath, $resolution);
        }

        //Create thumbs
        $this->createVideoThumb($this->videoPath, $params->filename, 5);

        //Create chunks
        if ($params->chunk) {
            // filename_#__hh-mm-ss_hh-mm-ss.mp4
            $start = 0;
            $i = 1;
            $offset = $params->chunkOffset;
            if ($params->chunkOffset < 20) $offset = 20;
            while ($start < $duration) {
                $ss = sprintf('%02d-%02d-%02d', ($start / 3600), ($start / 60 % 60), $start % 60);
                $s0 = sprintf('%02d-%02d-%02d', (($start + $offset) / 3600), (($start + $offset) / 60 % 60), ($start + $offset) % 60);
                $destFile = $file . "_" . str_pad($i, 3, 0, STR_PAD_LEFT) . "_" . $ss . "_" . $s0;

                $filename = $file . ".mp4";
                $destFilename = $destFile . ".mp4";
                $this->createChunk($this->videoPath, $filename, $destFilename, $start, $offset);

                $filename = $file . ".webm";
                $destFilename = $destFile . ".webm";
                $this->createChunk($this->videoPath, $filename, $destFilename, $start, $offset);

                $this->createVideoThumb($this->videoPath, $destFilename, 5);

                $this->createMedia($destFilename, filesize($this->videoPath . "/" . $destFilename), "video/webm");

                $start += $offset;
                if ($start < $duration && ($duration - $start) < ($offset + ($offset / 2))) {
                    $offset = $duration - $start;
                }
                $i++;
            }
        } else {
            $this->createMedia($file . ".webm", $size, "video/webm");
        }
    }

    public function audioTranscode($params)
    {
        if (!is_file($this->uploadPath . "/" . $params->filename)) {
            throw new CException("File not exists: " . $this->uploadPath . "/" . $params->filename);
        }

        $info = $this->getInfo($params->filename);
        $isMp3 = false;
        $isOgg = false;
        $duration = 0;
        $size = 0;
        $ext = pathinfo($this->uploadPath . "/" . $params->filename, PATHINFO_EXTENSION);
        $file = basename($params->filename, "." . $ext);

        if (isset($info) && isset($info->streams[0])) {
            if (isset($info->streams[0]->codec_name) && $info->streams[0]->codec_name == "mp3") {
                copy($this->uploadPath . "/" . $params->filename, $this->audioPath . "/" . $params->filename);
                $isMp3 = true;
            }
            if (isset($info->streams[0]->codec_name) && $info->streams[0]->codec_name == "vorbis") {
                copy($this->uploadPath . "/" . $params->filename, $this->audioPath . "/" . $params->filename);
                $isOgg = true;
            }
            $duration = $info->format->duration;
            $size = $info->format->size;
        }

        if (!$isMp3) {
            $this->convertToMp3($this->uploadPath, $params->filename, $this->audioPath);
        }

        if (!$isOgg) {
            $this->convertToOgg($this->uploadPath, $params->filename, $this->audioPath);
        }

        //Create chunks
        if ($params->chunk) {
            // filename_#__hh-mm-ss_hh-mm-ss.mp4
            $start = 0;
            $i = 1;
            $offset = $params->chunkOffset;
            if ($params->chunkOffset < 20) $offset = 20;
            while ($start < $duration) {
                $ss = sprintf('%02d-%02d-%02d', ($start / 3600), ($start / 60 % 60), $start % 60);
                $s0 = sprintf('%02d-%02d-%02d', (($start + $offset) / 3600), (($start + $offset) / 60 % 60), ($start + $offset) % 60);
                $destFile = $file . "_" . str_pad($i, 3, 0, STR_PAD_LEFT) . "_" . $ss . "_" . $s0;

                $filename = $file . ".mp3";
                $destFilename = $destFile . ".mp3";
                $this->createChunk($this->audioPath, $filename, $destFilename, $start, $offset);

                $this->createMedia($destFilename, filesize($this->audioPath . "/" . $destFilename), "audio/mpeg");

                $filename = $file . ".ogg";
                $destFilename = $destFile . ".ogg";
                $this->createChunk($this->audioPath, $filename, $destFilename, $start, $offset);


                $start += $offset;
                if ($start < $duration && ($duration - $start) < ($offset + ($offset / 2))) {
                    $offset = $duration - $start;
                }
                $i++;
            }
        } else {
            $this->createMedia($file . ".mp3", $size, "audio/mpeg");
        }
    }

    /**
     * Set paths for audio and video
     *
     */
    private function initFolders()
    {
        $path = realpath(Yii::app()->getBasePath() . Yii::app()->fbvStorage->get("settings.app_upload_path"));

        if (!is_dir($path . "/videos")) {
            mkdir($path . "/videos");
        }
        $this->videoPath = $path . "/videos";

        if (!is_dir($path . "/audios")) {
            mkdir($path . "/audios");
        }
        $this->audioPath = $path . "/audios";

        $this->uploadPath = $path;
    }

    /**
     * @param string $filename
     * @return mixed
     */
    private function getInfo($filename)
    {
        $ffprobe = dirname(__FILE__) . "/ffmpeg/ffprobe";
        exec($ffprobe . " -print_format json -show_format -show_streams " . $this->uploadPath . "/" . $filename, $output);
        $output = implode("", $output);
        $json = json_decode($output);
        return $json;
    }

    /**
     * @param string $path
     * @param string $filename
     * @param string $dest
     * @param string $resolution format WIDTHxHEIGHT
     */
    private function convertToWebm($path, $filename, $dest, $resolution)
    {
        $ext = pathinfo($path . "/" . $filename, PATHINFO_EXTENSION);
        $file = basename($filename, "." . $ext);

        $ffmpeg = dirname(__FILE__) . "/ffmpeg/ffmpeg";
        exec($ffmpeg . " -i " . $path . "/" . $filename . " -b:v 500k -b:a 128k -vcodec libvpx -acodec libvorbis -f webm -g 30 -s " . $resolution . " " . $dest . "/" . $file . ".webm");
    }

    /**
     *
     * @param string $path
     * @param string $filename
     * @param int $offset in seconds
     */
    private function createVideoThumb($path, $filename, $offset)
    {
        $ext = pathinfo($path . "/" . $filename, PATHINFO_EXTENSION);
        $file = basename($filename, "." . $ext);

        $ffmpeg = dirname(__FILE__) . "/ffmpeg/ffmpeg";
        exec($ffmpeg . " -i " . $path . "/" . $filename . " -ss $offset -f image2 -vframes 1 " . $path . "/" . $file . ".jpeg");
    }

    /**
     * @param string $path
     * @param string $filename
     * @param string $dest
     * @param string $resolution format WIDTHxHEIGHT
     */
    private function convertToMpeg4($path, $filename, $dest, $resolution)
    {
        $ext = pathinfo($path . "/" . $filename, PATHINFO_EXTENSION);
        $file = basename($filename, "." . $ext);

        $ffmpeg = dirname(__FILE__) . "/ffmpeg/ffmpeg";
        exec($ffmpeg . " -i " . $path . "/" . $filename . " -b:v 500k -b:a 128k -vcodec libx264 -g 30 -s " . $resolution . " " . $dest . "/" . $file . ".mp4");
    }

    private function convertToMp3($path, $filename, $dest)
    {
        $ext = pathinfo($path . "/" . $filename, PATHINFO_EXTENSION);
        $file = basename($filename, "." . $ext);

        $ffmpeg = dirname(__FILE__) . "/ffmpeg/ffmpeg";
        exec($ffmpeg . " -i " . $path . "/" . $filename . " -b:a 128k " . $dest . "/" . $file . ".mp3");
    }

    private function convertToOgg($path, $filename, $dest)
    {
        $ext = pathinfo($path . "/" . $filename, PATHINFO_EXTENSION);
        $file = basename($filename, "." . $ext);

        $ffmpeg = dirname(__FILE__) . "/ffmpeg/ffmpeg";
        exec($ffmpeg . " -i " . $path . "/" . $filename . " -f ogg -acodec libvorbis -aq 6 " . $dest . "/" . $file . ".ogg");
    }

    /**
     * @param string $path
     * @param string $filename
     * @param string $destFilename
     * @param int $from
     * @param int $offset
     */
    private function createChunk($path, $filename, $destFilename, $from, $offset)
    {
        $ffmpeg = dirname(__FILE__) . "/ffmpeg/ffmpeg";
        exec($ffmpeg . " -i " . $path . "/" . $filename . " -ss $from -t $offset " . $path . "/" . $destFilename);
    }

    /**
     * @return array|mixed
     */
    private function getMicrotime()
    {
        $time = microtime();
        $time = explode(' ', $time);
        $time = $time[1] + $time[0];
        return $time;
    }

    private function totalTime($start)
    {
        $finish = $this->getMicrotime();
        return round(($finish - $start), 4);
    }

    private function createMedia($file_name, $size, $mime_type)
    {
        $media = new Media;
        $media->name = $file_name;
        $media->size = $size;
        $media->batch_id = "B-" . date('Y-m-d-H:i:s');
        $media->mime_type = $mime_type;
        $media->created = date('Y-m-d H:i:s');
        $media->modified = date('Y-m-d H:i:s');
        $media->locked = 0;

        $relatedData = array(
            'collections' => array(1),
        );
        $media->saveWithRelated($relatedData);
    }


    /**
     * @return bool
     */
    private function isLocked()
    {
        $lockFile = Yii::app()->getRuntimePath() . DIRECTORY_SEPARATOR . 'media.lock';
        if (file_exists($lockFile)) {
            $lockingPID = trim(file_get_contents($lockFile));
            $output = array();
            if (strncmp(PHP_OS, 'WIN', 3) === 0) {
                exec('tasklist /FI "PID eq ' . $lockingPID . '"', $output);
                if (count($output) > 1) return true;
            } else {
                if (file_exists('/proc/' . $lockingPID)) return true;

                $pids = explode("\n", trim(`ps -e | awk '{print $1}'`));
                # If PID is still active, return true
                if (in_array($lockingPID, $pids)) return true;
            }
            unlink($lockFile);
        }

        file_put_contents($lockFile, getmypid() . "\n");
        return false;
    }

    private function releaseLock()
    {
        $lockFile = Yii::app()->getRuntimePath() . DIRECTORY_SEPARATOR . 'media.lock';
        unlink($lockFile);
    }
}
