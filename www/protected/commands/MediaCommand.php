<?php
/**
 *
 * @package
 * @author     Nikolay Kondikov<nikolay.kondikov@sirma.bg>
 */
class MediaCommand extends CConsoleCommand
{

    private $uploadPath;
    private $audioPath;
    /**
     * @var
     */
    private $videoPath;

    public function actionHelp()
    {
    }

    public function actionIndex()
    {
        if ($this->isLocked()) die("Already running.\n");
        //$start = $this->getMicrotime();

        $now = new DateTime('now', new DateTimeZone('GMT'));
        $now = $now->format("Y-m-d H:i:s");

        $jobs = CronJob::model()->findAll('execute_after <:now AND executed_started IS NULL ORDER BY id ASC', array(':now' => $now));

        $this->initFolders();

        for ($i = 0; $i < count($jobs); $i++) {
            $job = $jobs[$i];
            echo "Processing Job " . $job->id . "\r\n";

            if (method_exists($this, $job->action)) {
                $executed_at = new DateTime('now', new DateTimeZone('GMT'));
                $job->executed_started = $executed_at->format('Y-m-d H:i:s');
                $job->save();
                $params = MediaParameters::createFromJson($job->parameters);
                $result = $this->{$job->action}($params);

                if ($result === false) {
                    // do nothing, let the next cycle pick it up
                    continue;
                } else {
                    $executed_at = new DateTime('now', new DateTimeZone('GMT'));
                    $job->succeeded = $result['succeeded'] ? 1 : 0;
                    $job->execution_result = array_key_exists('execution_result', $result) ? $result['execution_result'] : "";
                    $job->executed_finished = $executed_at->format('Y-m-d H:i:s');
                    $job->save();
                }
            } else {
                $executed_at = new DateTime('now', new DateTimeZone('GMT'));
                $job->executed_finished = $executed_at->format('Y-m-d H:i:s');
                $job->succeeded = 0;
                $job->execution_result = 'Action does not exist.';
                $job->save();
            }
        }

        $this->releaseLock();
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

        if (isset($info) && isset($info->streams[0])) {
            if ($info->streams[0]["codec_name"] == "vp8" && $info->streams[0]["width"] == 640) {
                copy($this->uploadPath . "/" . $params->filename, $this->videoPath . "/" . $params->filename);
                $isWebm = true;
            }
            if ($info->streams[0]["codec_name"] == "h264" && $info->streams[0]["width"] == 640) {
                copy($this->uploadPath . "/" . $params->filename, $this->videoPath . "/" . $params->filename);
                $isMpeg4 = true;
            }
            if ($info->streams[0]["display_aspect_ratio"] == "16:9") {
                $resolution = "640x360";
            } else if ($info->streams[0]["display_aspect_ratio"] == "4:3") {
                $resolution = "640x480";
            }
            $duration = $info->streams[0]["duration"];
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
            $ext = pathinfo($this->uploadPath . "/" . $params->filename, PATHINFO_EXTENSION);
            $file = basename($params->filename, "." . $ext);
            $i = 1;
            if ($params->chunkOffset < 6) $params->chunkOffset = 20;
            while ($start < $duration) {
                $ss = sprintf('%02d-%02d-%02d', ($start / 3600), ($start / 60 % 60), $start % 60);
                $s0 = sprintf('%02d-%02d-%02d', (($start + $params->chunkOffset) / 3600), (($start + $params->chunkOffset) / 60 % 60), ($start + $params->chunkOffset) % 60);
                $destFile = $file . "_#" . $i . "__" . $ss . "_" . $s0;

                $filename = $file . ".mp4";
                $destFilename = $destFile . ".mp4";
                $this->createChunk($this->videoPath, $filename, $destFilename, $start, $params->chunkOffset);

                $filename = $file . ".webm";
                $destFilename = $destFile . ".webm";
                $this->createChunk($this->videoPath, $filename, $destFilename, $start, $params->chunkOffset);

                $this->createVideoThumb($this->videoPath, $destFilename, 5);

                $start += $params->chunkOffset;
                $i++;
            }
        }

    }

    public function audioTranscode($params)
    {

    }

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
        exec($ffprobe . " -print_format json -show_format -show_streams " . $filename, $output);
        $output = implode("", $output);
        $json = json_decode($output);
        return $json;
    }

    private function convertToWebm($path, $filename, $dest, $resolution)
    {
        $path = $path . "/" . $filename;
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

    private function convertToMpeg4($path, $filename, $dest, $resolution)
    {
        $ext = pathinfo($path . "/" . $filename, PATHINFO_EXTENSION);
        $file = basename($filename, "." . $ext);

        $ffmpeg = dirname(__FILE__) . "/ffmpeg/ffmpeg";
        exec($ffmpeg . " -i " . $path . "/" . $filename . " -b:v 500k -b:a 128k -vcodec libx264 -g 30 -s " . $resolution . " " . $dest . "/" . $file . ".mp4");
    }

    private function createChunk($path, $filename, $destFilename, $from, $offset)
    {
        $ffmpeg = dirname(__FILE__) . "/ffmpeg/ffmpeg";
        exec($ffmpeg . " -i " . $path . "/" . $filename . " -ss $from -t $offset " . $path . "/" . $destFilename);
    }

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
