<?php
Yii::import("ext.xupload.models.XUploadForm");
Yii::import("ext.runner.BConsoleRunner");
Yii::import("application.commands.MediaParameters");

class ImportController extends GxController
{
    /**
     * Full path of the main uploading folder.
     * @var string
     */
    public $path;

    /**
     * Subfolder in which files will be stored
     * @var string
     */
    public $subfolder = "images";

    public function filters()
    {
        return array(
            /*'IPBlock',*/
            'accessControl',
        );
    }

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('view'),
                'roles' => array('*'),
            ),
            array('allow',
                'actions' => array('index', 'uploadfromlocal', 'queueprocess', 'uploadzip', 'uploadftp', 'transcodingprocess', 'uploadprocess', 'xuploadmedia', 'batch', 'delete'),
                'roles' => array('editor', 'admin'),
            ),
            array('deny',
                'users' => array('*'),
            ),
        );
    }

    public function actionIndex()
    {
        $this->layout = '//layouts/column1';

        if (Yii::app()->user->checkAccess('editor')) {
            $tools = array();

            $tools["import-local"] = array(
                "name" => Yii::t('app', "Import medias from your computer"),
                "description" => Yii::t('app', "Select media(s). This includes the ability to click and drag files to import <strong>(good for small media sets)</strong>."),
                "url" => $this->createUrl('/admin/import/uploadfromlocal'),
            );

            $tools["import-ftp"] = array(
                "name" => Yii::t('app', "Import medias that can be found in the server's '/uploads/ftp' folder"),
                "description" => Yii::t('app', "Place medias in this folder using a SFTP client and let the system do its work <strong>(recommended method for large media sets)</strong>."),
                "url" => $this->createUrl('/admin/import/uploadftp'),
            );

            $tools["import-zip"] = array(
                "name" => Yii::t('app', "Import medias in a ZIP file from your computer"),
                "description" => Yii::t('app', "Import .zip compressed archives of medias. Currently has a filesize limit of " . (int)(ini_get('upload_max_filesize')) . " MB."),
                "url" => $this->createUrl('/admin/import/uploadzip'),
            );

            $tools["transcoding-process"] = array(
                "name" => Yii::t('app', "Transcoding process of imported medias"),
                "description" => Yii::t('app', "Once you have imported upload audio and video files into the system, here you will see the transcoding progress."),
                "url" => $this->createUrl('/admin/import/transcodingprocess'),
            );

            $tools["process"] = array(
                "name" => Yii::t('app', "Process imported medias"),
                "description" => Yii::t('app', "Once you have imported medias into the system, use this to process them."),
                "url" => $this->createUrl('/admin/import/uploadprocess'),
            );

            $this->render('index',
                array(
                    'tools' => $tools
                )
            );
        } else {
            throw new CHttpException(403, Yii::t('app', 'Access Denied.'));
        }
    }

    public function actionImportSettings()
    {
        $this->layout = '//layouts/column1';
        $this->render('processimportedmedias', array());
    }

    public function actionUploadFromLocal()
    {
        $this->layout = '//layouts/column1';

        $model = new XUploadForm;
        $this->render('uploadfromlocal', array(
            'model' => $model,
        ));
    }

    public function actionUploadZip()
    {
        $this->layout = '//layouts/column1';
        $this->checkUploadFolder();

        $model = new ImportZipForm;

        if (isset($_POST['ImportZipForm'])) {
            $model->setAttributes($_POST['ImportZipForm']);

            if ($model->validate()) {

                $file_media = CUploadedFile::getInstance($model, 'zipfile');

                if ((is_object($file_media) && get_class($file_media) === 'CUploadedFile')) {
                    $pclzip = $this->module->zip;

                    $tmp_path = sys_get_temp_dir() . "/MG" . date('YmdHis');
                    if (!is_dir($tmp_path)) {
                        mkdir($tmp_path);
                        chmod($tmp_path, 0777);
                    }

                    if (is_dir($tmp_path)) {
                        $list = $pclzip->extractZip($file_media->tempName, $tmp_path);

                        if ($list) {
                            $cnt_added = 0;
                            $cnt_skipped = 0;

                            $path = $this->path;

                            foreach ($list as $file) {
                                $file_info = pathinfo($file['stored_filename']);


                                if (!$file["folder"] && strpos($file['stored_filename'], "__MACOSX") === false) { // we don't want to process folder and MACOSX meta data file mirrors as the mirrored files also return the image/jpg mime type
                                    $mime_type = CFileHelper::getMimeType($file['filename']);
                                    if (!isset($mime_type)) {
                                        $mime_type = ImportController::getMimeTypeByExtension($file);
                                    }
                                    $file_ok = $this->_checkMedia($file['filename'], $mime_type);

                                    list($media_type, $extention) = explode('/', $mime_type);

                                    if ($media_type == "image") {
                                        $item_path = $path . "/" . $this->subfolder . "/";
                                    } else {
                                        $item_path = $path . "/";
                                    }

                                    if (!is_dir($item_path)) {
                                        mkdir($item_path);
                                        chmod($item_path, 0777);
                                    }

                                    if (($media_type == "image" || $media_type == "video" || $media_type == "audio") && $file_ok) {
                                        $cnt_added++;

                                        $file['stored_filename'] = $this->checkFileName($item_path, $file_info["basename"]);
                                        rename($file['filename'], $item_path . $file['stored_filename']);

                                        if ($media_type == "image") {
                                            $this->createMedia($file['stored_filename'], $file['size'], $_POST['ImportZipForm']["batch_id"], $mime_type);
                                        } elseif ($media_type == "video" || $media_type == "audio") {

                                            $params = new MediaParameters();
                                            $params->chunk = true;
                                            $params->chunkOffset = 20;
                                            $params->filename = $file['stored_filename'];

                                            $cronJob = new CronJob();
                                            $cronJob->execute_after = date('Y-m-d H:i:s');
                                            if ($media_type == "audio") {
                                                $cronJob->action = "audioTranscode";
                                            } elseif ($media_type == "video") {
                                                $cronJob->action = "videoTranscode";
                                            }
                                            $cronJob->parameters = json_encode($params);
                                            $cronJob->save();

                                            $runner = new BConsoleRunner();
                                            $runner->run("media", array("index"));
                                        }


                                    } else {
                                        if (!$file_ok)
                                            Flash::add('error', Yii::t('app', 'The file {file} is corrupt and could therefore not be imported.', array('{file}' => $file['filename'])), true);
                                        $cnt_skipped++;
                                    }
                                }
                            }
                            Flash::add("success", Yii::t('app', '{total} files found, {num} medias imported, {num_skipped} other files skipped', array("{num}" => $cnt_added, "{total}" => $cnt_added + $cnt_skipped, "{num_skipped}" => $cnt_skipped)));
                            $this->redirect("uploadprocess");
                        }
                    }

                } else {
                    $model->addError("zipfile", Yii::t('app', 'Please choose a zip file'));
                }
            }
        }

        if (!Yii::app()->getRequest()->getIsPostRequest())
            $model->batch_id = "B-" . date('Y-m-d-H:i:s');

        if (Yii::app()->getRequest()->getIsPostRequest() && !$model->hasErrors()) {
            $model->addError("zipfile", Yii::t('app', 'Please make sure to keep the file smaller than %dB', array('%d' => ini_get('upload_max_filesize'))));
            $model->batch_id = "B-" . date('Y-m-d-H:i:s');
            $model->addError("batch_id", Yii::t('app', 'Please check your upload batch id'));
        }

        $this->render('uploadzip', array(
            'model' => $model,
        ));
    }

    public function actionUploadFtp()
    {
        Yii::app()->clientScript->registerCoreScript('jquery');
        Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/mg.api.js', CClientScript::POS_END);
        Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . '/css/jquery.fancybox-1.3.4.css');
        Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.fancybox-1.3.4.pack.js', CClientScript::POS_END);

        // add page resubmit if script time out is close.
        $this->layout = '//layouts/column1';
        $this->checkUploadFolder();

        $path = $this->path . "/" . $this->subfolder . "/";
        if (!is_dir($path)) {
            mkdir($path);
            chmod($path, 0777);
        }

        $model = new ImportFtpForm;
        $count_files = 0;

        $ftp_path = $this->path . "/ftp/";
        if (is_dir($ftp_path)) {
            $list = CFileHelper::findFiles($ftp_path);

            foreach ($list as $file) {
                $file_info = pathinfo($file);
                if ($file_info['basename'] != '.gitignore') {
                    $count_files++;
                }
            }

        }
        if (!Yii::app()->getRequest()->getIsPostRequest())
            $model->batch_id = "B-" . date('Y-m-d-H:i:s');

        $this->render('uploadftp', array(
            'model' => $model,
            'count_files' => $count_files,
        ));
    }

    private function _processFTPQueue()
    {
        $data = array();
        $data['status'] = 'ok';

        $this->checkUploadFolder();

        $path = $this->path;

        $model = new ImportFtpForm;
        $count_files = 0;

        $ftp_path = $this->path . "/ftp/";
        if (is_dir($ftp_path)) {
            if (isset($_POST['ImportFtpForm'])) {
                $model->setAttributes($_POST['ImportFtpForm']);

                if ($model->validate()) {
                    $cnt_added = 0;
                    $cnt_skipped = 0;

                    $list = CFileHelper::findFiles($ftp_path);
                    foreach ($list as $file) {
                        $file_info = pathinfo($file);
                        if ($file_info['basename'] != '.gitignore') {
                            $count_files++;
                        }
                    }

                    if ($count_files > 0) {
                        $import_per_request = $model->import_per_request;
                        $model->import_skipped = 0;

                        foreach ($list as $file) {
                            if ($import_per_request > 0) {
                                $file_info = pathinfo($file);

                                if ($file_info['basename'] != '.gitignore') {
                                    $mime_type = CFileHelper::getMimeType($file);

                                    if (!isset($mime_type)) {
                                        $mime_type = ImportController::getMimeTypeByExtension($file);
                                    }

                                    $file_ok = $this->_checkMedia($file, $mime_type);

                                    list($media_type, $extention) = explode('/', $mime_type);

                                    if (($media_type == "image" || $media_type == "video" || $media_type == "audio") && $file_ok) {
                                        if ($media_type == "image") {
                                            $item_path = $path . "/" . $this->subfolder . "/";
                                        } else {
                                            $item_path = $path . "/";
                                        }

                                        if (!is_dir($item_path)) {
                                            mkdir($item_path);
                                            chmod($item_path, 0777);
                                        }

                                        $model->import_processed++;
                                        $file_name = $this->checkFileName($item_path, $file_info["basename"]);
                                        rename(str_replace('//', '/', $file), $item_path . $file_name);

                                        if ($media_type == "image") {
                                            $this->createMedia($file_name, filesize($item_path . $file_name), $_POST['ImportFtpForm']["batch_id"], $mime_type);
                                        } elseif ($media_type == "video" || $media_type == "audio") {

                                            $params = new MediaParameters();
                                            $params->chunk = true;
                                            $params->chunkOffset = 20;
                                            $params->filename = $file_name;

                                            $cronJob = new CronJob();
                                            $cronJob->execute_after = date('Y-m-d H:i:s');
                                            if ($media_type == "audio") {
                                                $cronJob->action = "audioTranscode";
                                            } elseif ($media_type == "video") {
                                                $cronJob->action = "videoTranscode";
                                            }
                                            $cronJob->parameters = json_encode($params);
                                            $cronJob->save();

                                            $runner = new BConsoleRunner();
                                            $runner->run("media", array("index"));
                                        }

                                        $import_per_request--;
                                    } else {
                                        if (!$file_ok)
                                            Flash::add('error', Yii::t('app', 'The file {file} is corrupt and could therefore not be imported.', array('{file}' => $file)), true);
                                        $model->import_skipped++;
                                    }
                                    $count_files--;
                                }
                            }
                        }

                        if ($count_files == 0) {
                            $this->_finishFTPQueue($model->import_processed, $model->import_skipped);
                        } else {
                            $data['status'] = 'retry';
                            $data['files_left'] = $count_files;
                            $data['ImportFtpForm'] = $model;
                        }
                    } else {
                        $this->_finishFTPQueue($model->import_processed, $model->import_skipped);
                    }
                }
            }
        }
        $this->jsonResponse($data);
    }

    private function _finishFTPQueue($added, $skipped)
    {
        $data['status'] = 'done';
        $data['redirect'] = Yii::app()->createUrl('admin/import/uploadprocess');

        Flash::add("success", Yii::t('app', '{total} files found in \'/uploads/ftp\' folder, {num} medias imported, {num_skipped} other files skipped', array("{total}" => $added + $skipped, "{num}" => $added, "{num_skipped}" => $skipped)));
        if ($skipped > 0)
            Flash::add("warning", Yii::t('app', 'The {num_skipped} files that are still in the \'/uploads/ftp\' folder cannot be imported and should therfore be manually removed!', array("{total}" => $added + $skipped, "{num}" => $added, "{num_skipped}" => $skipped)), true);

        $this->jsonResponse($data);
    }

    public function actionQueueProcess($action)
    {
        switch ($action) {
            case 'ftp':
                $this->_processFTPQueue();
                break;
        }
    }


    public static function loadPluginsModule() {
        static $added = false;
        if ($added) return;
        YiiBase::import('application.modules.plugins.*');
        $added = true;
    }

    public function actionUploadProcess()
    {
        self::loadPluginsModule();
        $this->layout = '//layouts/column1';

        $model = new Media('search');
        $model->unsetAttributes();

        if (isset($_GET['Media']))
            $model->setAttributes($_GET['Media']);

        $this->render('uploadprocess', array(
            'model' => $model,
        ));
    }

    public function actionTranscodingProcess()
    {
        $this->layout = '//layouts/column1';

        $criteria = new CDbCriteria;

        if (!Yii::app()->request->isAjaxRequest)
            $criteria->order = 'id DESC';

        $dataProvider = new CActiveDataProvider('CronJob', array(
            'criteria' => $criteria
        ));

        $this->render('transcodingprocess', array(
            'dataProvider' => $dataProvider,
        ));
    }

    public function actionDelete($id)
    {
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            $model = $this->loadModel($id, 'Media');
            if ($model->hasAttribute("locked") && $model->locked) {
                throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
            } else {
                $model->delete();
                MGHelper::log('delete', 'Deleted Media with ID(' . $id . ')');

                Flash::add('success', Yii::t('app', "Media deleted"));

                if (!Yii::app()->getRequest()->getIsAjaxRequest())
                    $this->redirect(array('uploadprocess'));
            }
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }

    public function actionBatch($op)
    {
        $this->layout = '//layouts/column1';
        if (Yii::app()->getRequest()->getIsPostRequest()) {
            switch ($op) {
                case "delete":
                    $this->_batchDelete();
                    break;

                case "process":
                    $this->_batchProcess();
                    break;
            }
            if (!Yii::app()->getRequest()->getIsAjaxRequest())
                $this->actionUploadProcess();
        } else
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));

    }

    private function _batchDelete()
    {
        if (isset($_POST['media-ids'])) {
            $medias = Media::model()->findAllByPk($_POST['media-ids']);

            if ($medias) {
                foreach ($medias as $media) {
                    $media->delete();
                }
            }
            MGHelper::log('batch-delete', 'Batch deleted Medias with IDs(' . implode(',', $_POST['media-ids']) . ')');
        }
    }

    private function _batchProcess()
    {
        $errors = array();
        $processedIDs = array();

        if (isset($_POST['media-ids']) || isset($_POST['massProcess'])) {
            if (isset($_POST['media-ids'])) {
                $medias = Media::model()->findAllByPk($_POST['media-ids']);
            } else {
                $condition = new CDbCriteria;
                $condition->limit = (int)$_POST['massProcess'];
                $condition->order = 'created DESC';
                $medias = Media::model()->findAllByAttributes(array('locked' => 0), $condition);
            }

            if ($medias) {
                $firstModel = $medias[0];

                $plugins = PluginsModule::getAccessiblePlugins("import");
                if (count($plugins) > 0) {
                    foreach ($plugins as $plugin) {
                        if (method_exists($plugin->component, "validate")) {
                            $plugin->component->validate($firstModel, $errors);
                        }
                    }
                }

                if (count($errors) == 0) {
                    if (count($plugins) > 0) {
                        foreach ($plugins as $plugin) {
                            if (method_exists($plugin->component, "process")) {
                                $plugin->component->process($medias);
                            }
                        }
                    }

                    foreach ($medias as $media) {
                        $media->locked = 1;
                        $media->save();
                        $processedIDs[] = $media->id;
                    }
                    MGHelper::log('batch-import-process', 'Batch processed Media with IDs(' . implode(',', $processedIDs) . ')');
                    Flash::add('success', Yii::t('app', 'Processed {count} medias with the IDs({ids})', array("{count}" => count($processedIDs), "{ids}" => implode(',', $processedIDs))));
                }
            }
        } else {
            $errors["noMedias"] = array(Yii::t('ui', 'Please check at least one media you would like to process!'));
        }

        if (count($errors) > 0) {
            if (Yii::app()->getRequest()->getIsAjaxRequest()) {
                $this->jsonResponse($errors);
            } else {
                $model = new Media('search');
                $model->unsetAttributes();

                if (isset($_GET['Media']))
                    $model->setAttributes($_GET['Media']);

                $model->addErrors($errors);

                $this->render('uploadprocess', array(
                    'model' => $model,
                ));
            }
            Yii::app()->end();
        }
    }

    public function actionXUploadMedia()
    {
        $info = array();

        $this->checkUploadFolder();

        $file = $_FILES;

        $model = new XUploadForm;
        $model->file = CUploadedFile::getInstance($model, 'file');

        if (isset($model->file) && isset($_POST["batch_id"]) && trim($_POST["batch_id"]) != "") {
            $model->mime_type = $model->file->getType(); //- this have regular problems with ogg files
            if ($model->mime_type === "video/ogg") {
                $model->mime_type = CFileHelper::getMimeTypeByExtension($model->file);
            }

            $model->size = $model->file->getSize();

            // Remove path information and dots around the filename, to prevent uploading
            // into different directories or replacing hidden system files.
            // Also remove control characters and spaces (\x00..\x20) around the filename:
            $model->name = trim(basename(stripslashes($model->file->getName())), ".\x00..\x20");
            $isMedia = false;
            $thumbUrl = "";
            list($media_type, $extention) = explode('/', $model->mime_type);

//            if ($model->validate()) { for images only
            if (($media_type == 'image' && $model->validate()) or (($media_type == 'audio' || $media_type == 'video') && $this->_checkMedia($model->file, $model->mime_type))) {
                if ($media_type == 'image') {
                    $path = $this->path . "/" . $this->subfolder . "/";
                    if (!is_dir($path)) {
                        mkdir($path);
                        chmod($path, 0777);
                    }

                    $model->name = $this->checkFileName($path, $model->name);
                    $model->file->saveAs($path . $model->name);

                    $media_info = getimagesize($path . $model->name);

                    if (!is_array($media_info) OR count($media_info) < 3)
                        $isMedia = false;
                    else {
                        $this->createMedia($model->name, $model->size, $_POST["batch_id"], $model->mime_type);
                        $thumbUrl = Yii::app()->getBaseUrl() . Yii::app()->fbvStorage->get('settings.app_upload_url') . "/thumbs/" . $model->name;
                        $isMedia = true;
                    }
                } elseif ($media_type == "video" || $media_type == "audio") {
                    $path = $this->path . "/";
                    $model->name = $this->checkFileName($path, $model->name);
                    $model->file->saveAs($path . $model->name);

                    $params = new MediaParameters();
                    $params->chunk = true;
                    $params->chunkOffset = 20;
                    $params->filename = $model->name;

                    $cronJob = new CronJob();
                    $cronJob->execute_after = date('Y-m-d H:i:s');
                    if ($media_type == "audio") {
                        $cronJob->action = "audioTranscode";
                    } elseif ($media_type == "video") {
                        $cronJob->action = "videoTranscode";
                    }
                    $cronJob->parameters = json_encode($params);
                    $cronJob->save();

                    $runner = new BConsoleRunner();
                    $runner->run("media", array("index"));
                    $isMedia = true;
                }

                if ($isMedia) {
                    $info[] = array(
                        'tmp_name' => $model->file->getName(),
                        'name' => $model->name,
                        'size' => $model->size,
                        'type' => $model->mime_type,
                        'thumbnail_url' => $thumbUrl,
                        'error' => null
                    );

                } else {
                    $info[] = array(
                        'tmp_name' => $model->file->getName(),
                        'name' => $model->name,
                        'size' => $model->size,
                        'type' => $model->mime_type,
                        'error' => Yii::t('app', 'I/O erroro. Uploaded media file corrupted.')
                    );
                }
            } else {
                $info[] = array(
                    'tmp_name' => $model->file->getName(),
                    'name' => $model->name,
                    'size' => $model->size,
                    'type' => $model->mime_type,
                    'error' => 'acceptFileTypes'
                );
            }
        } else {
            $error = 4;

            if (!isset($_POST["batch_id"]) || trim($_POST["batch_id"]) == "")
                $error = Yii::t('app', 'Please specify a batch id');

            $info[] = array(
                'tmp_name' => null,
                'name' => null,
                'size' => null,
                'type' => null,
                'error' => $error
            );
        }
        $this->jsonResponse($info);
    }

    /**
     * The method implements a basic functionality to verify if the uploaded media is an media file
     * and not corrupted
     *
     * @param string $path the full path to the media
     * @param string $mime_type the mime type of the media file
     * @return boolean true if the file is a valid media file
     */
    private function _checkMedia($path, $mime_type)
    {
        // Disable error reporting, to prevent PHP warnings
        $ER = error_reporting(0);

        list($media_type, $extention) = explode('/', $mime_type);

        //TODO better audio/video validation
        if ($media_type == 'image') {
            // Fetch the media size and mime type
            $media_info = getimagesize($path);
        } else if ($media_type == 'video') {
            $media_info = array(
                'tmp_name' => $path,
                'name' => $path,
                'type' => $mime_type,
            );
        } else if ($media_type == 'audio') {
            $media_info = array(
                'tmp_name' => $path,
                'name' => $path,
                'type' => $mime_type,
            );
        }

        // Turn on error reporting again
        error_reporting($ER);

        if(!isset($media_info)) return false;
        // Make sure that the media is readable and valid
        if (!is_array($media_info) OR count($media_info) < 3)
            return false;
        else
            return true;
    }

    private function checkFileName($path, $file_name)
    {
        $replace = "_";
        $pattern = "/([[:alnum:]_\.-]*)/";
        $file_name = str_replace(str_split(preg_replace($pattern, $replace, $file_name)), $replace, $file_name);

        $path_parts = pathinfo($file_name);

        $result = glob(str_replace('.' . $path_parts['extension'], '', $path . $file_name) . ".*");

        if (count($result) > 0) {
            $c = 1;
            $name = $path_parts['filename'] . "_" . $c;
            while (count(glob($path . $name . ".*")) > 0) {
                $c++;
                $name = $path_parts['filename'] . "_" . $c;
            }
            $file_name = $name . "." . $path_parts['extension'];
        }

        return $file_name;
    }

    private function createMedia($file_name, $size, $batch_id, $mime_type)
    {
        $media = new Media;
        $media->name = $file_name;
        $media->size = $size;
        $media->batch_id = $batch_id;
        $media->mime_type = $mime_type;
        $media->created = date('Y-m-d H:i:s');
        $media->modified = date('Y-m-d H:i:s');
        $media->locked = 0;

        $relatedData = array(
            'collections' => array(1),
        );
        $media->saveWithRelated($relatedData);

        MGHelper::log('import-uploadfromlocal', 'Created Media with ID(' . $media->id . ')');

        $format = Yii::app()->fbvStorage->get("media.formats.thumbnail",
            array(
                "width" => 70,
                "height" => 50,
                "quality" => FALSE, // set to integer 0 ... 100 to activate quality rendering
                "sharpen" => FALSE, // set to integer 0 ... 100 to activate sharpen
            ));

        list($media_type, $ext_) = explode('/', $mime_type);

        //var_dump("TODO create thumbnail");
        if ($media_type == 'image') {
            MGHelper::createScaledMedia($file_name, $file_name, 'thumbs', $format["width"], $format["height"], $format["quality"], $format["sharpen"]);
        } else if ($media_type == 'video') {

        } else if ($media_type == 'audio') {

        }

    }

    private function checkUploadFolder()
    {
        if (!isset($this->path)) {
            $this->path = realpath(Yii::app()->getBasePath() . Yii::app()->fbvStorage->get("settings.app_upload_path"));
        }

        if (!is_dir($this->path)) {
            throw new CHttpException(500, "{$this->path} does not exists.");
        } else if (!is_writable($this->path)) {
            throw new CHttpException(500, "{$this->path} is not writable.");
        }
    }


    /**
     * @static
     * @param string $file
     * @return string
     */
    public static function getMimeTypeByExtension($file)
    {
        static $extensions = array();

        $extensions = require(Yii::getPathOfAlias('system.utils.mimeTypes') . '.php');
        $extensions['mp4'] = "video/mpeg";
        $extensions['webm'] = "video/webm";
        $extensions['wmv'] = "video/x-ms-wmv";
        $extensions['3gp'] = "video/3gpp";
        $extensions['flac'] = "audio/flac";

        if (($ext = pathinfo($file, PATHINFO_EXTENSION)) !== '') {
            $ext = strtolower($ext);
            if (isset($extensions[$ext]))
                return $extensions[$ext];
        }
        return null;
    }

}