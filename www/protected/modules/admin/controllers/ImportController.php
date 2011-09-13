<?php
Yii::import("ext.xupload.models.XUploadForm");

class ImportController extends GxController {
  /**
   * Full path of the main uploading folder.
   * @var string
   */
  public $path;
  
  /**
   * Subfolder in which files will be stored
   * @var string
   */
  public $subfolder="images";
  
  public function filters() {
  	return array(
  			'accessControl', 
  			);
  }
  
  public function accessRules() {
  	return array(
  			array('allow',
  				'actions'=>array('view'),
  				'roles'=>array('*'),
  				),
  			array('allow', 
  				'actions'=>array('index', 'uploadfromlocal', 'uploadzip', 'uploadftp', 'uploadprocess', 'xuploadimage', 'batch', 'delete'),
  				'roles'=>array('editor', 'xxx'),
  				),
  			array('deny', 
  				'users'=>array('*'),
  				),
  			);
  }

	public function actionIndex() {
	  $this->layout='//layouts/column1';
    
    if (Yii::app()->user->checkAccess('editor')) {
      $tools = array();
      
      $tools["import-local"] = array(
                              "name" => Yii::t('app', "Import images from your computer"),
                              "description" => Yii::t('app', "Some short description"),
                              "url" => $this->createUrl('/admin/import/uploadfromlocal'),
                           );
      
      $tools["import-zip"] = array(
                              "name" => Yii::t('app', "Import images in a ZIP file from your computer"),
                              "description" => Yii::t('app', "Some short description"),
                              "url" => $this->createUrl('/admin/import/uploadzip'),
                           );
      
      $tools["import-ftp"] = array(
                              "name" => Yii::t('app', "Import images that can be found on in the server's '/uploads/ftp' folder"),
                              "description" => Yii::t('app', "Some short description"),
                              "url" => $this->createUrl('/admin/import/uploadftp'),
                           );                           
      
      $tools["process"] = array(
                              "name" => Yii::t('app', "Process uploaded images"),
                              "description" => Yii::t('app', "Some short description"),
                              "url" => $this->createUrl('/admin/import/uploadprocess'),
                           );  
                         
      $this->render('index',
        array (
          'tools' => $tools 
        )
      );  
    } else {
      throw new CHttpException(403, Yii::t('app', 'Access Denied.'));
    }
	}
  
  public function actionImportSettings() {
    $this->layout='//layouts/column1';
    $this->render('processimportedimages', array());
  }
  
  public function actionUploadFromLocal() {
    $this->layout='//layouts/column1';  
    
    $model = new XUploadForm;
    $this->render('uploadfromlocal', array(
      'model' => $model,
    ));
  }
  
  public function actionUploadZip() {
    $this->layout='//layouts/column1';  
    $this->checkUploadFolder();
    
    $model = new ImportZipForm;
    
    if (isset($_POST['ImportZipForm'])) {
      $model->setAttributes($_POST['ImportZipForm']);
      
      if ($model->validate()) {
        $file_image = CUploadedFile::getInstance($model,'zipfile');
      
        if ( (is_object($file_image) && get_class($file_image)==='CUploadedFile')) {
          $pclzip = $this->module->pclzip;  
          
          $tmp_path = sys_get_temp_dir() . "/MG" . date('YmdHis');
          if (!is_dir($tmp_path)) {
            mkdir($tmp_path);
          }
          
          if (is_dir($tmp_path)) {
            $list = $pclzip->extractZip($file_image->tempName , $tmp_path);
            if ($list) {
              $cnt_added = 0;
              $cnt_skipped = 0;
              
              $path = $this->path . "/" . $this->subfolder."/";
              if(!is_dir($path)){
                mkdir($path);
              }
              
              foreach ($list as $file) {
                  
                $file_info = pathinfo($file['stored_filename']);
                
                if (!$file["folder"] && strpos($file['stored_filename'], "__MACOSX") === false) { // we don't want to process folder and MACOSX meta data file mirrors as the mirrored files also return the image/jpg mime type
                  $mime_type = CFileHelper::getMimeType($file['filename']);
                  if ($mime_type == "image/jpeg") {
                    $cnt_added++;
                    
                    $file['stored_filename'] = $this->checkFileName($path, $file_info["basename"]);
                    rename($file['filename'], $path . $file['stored_filename']);
                    $this->createImage($file['stored_filename'], $file['size'], $_POST['ImportZipForm']["batch_id"], $mime_type);
                  
                  } else {
                    $cnt_skipped++;
                  }
                }
              }
              Flash::add("success", Yii::t('app', '{total} files found, {num} images imported, {num_skipped} other files skipped', array("{num}" => $cnt_added, "{total}" => $cnt_added + $cnt_skipped, "{num_skipped}" => $cnt_skipped)));
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
    
    $this->render('uploadzip', array(
      'model' => $model,
    ));
  }
  
  public function actionUploadFtp() {
    
    // add page resubmit if script time out is close. 
    $this->layout='//layouts/column1';  
    $this->checkUploadFolder();
    
    $path = $this->path . "/" . $this->subfolder."/";
    if(!is_dir($path)){
      mkdir($path);
    }
    
    $model = new ImportFtpForm;
    
    if (isset($_POST['ImportFtpForm'])) {
      $model->setAttributes($_POST['ImportFtpForm']);
      
      if ($model->validate()) {
        $cnt_added = 0;
        $cnt_skipped = 0;
        
        $ftp_path = $this->path . "/ftp/";
        if (is_dir($ftp_path)) {
          
          $list = CFileHelper::findFiles($ftp_path);
          if (count($list)) {
            foreach ($list as $file) {
              $file_info = pathinfo($file);
              print $file;
              $mime_type = CFileHelper::getMimeType($file);
              if ($mime_type == "image/jpeg") {
                $cnt_added++;
                
                $file_name = $this->checkFileName($path, $file_info["basename"]);
                rename($file, $path . $file_name);
                $this->createImage($file_name, filesize($path . $file_name), $_POST['ImportFtpForm']["batch_id"], $mime_type);
              
              } else {
                $cnt_skipped++;
              }
            }
          }
        }          
        
        Flash::add("success", Yii::t('app', '{total} files found in \'/uploads/ftp\' folder, {num} images imported, {num_skipped} other files skipped', array("{total}" => $cnt_added + $cnt_skipped, "{num}" => $cnt_added, "{num_skipped}" => $cnt_skipped)));
        
        if ($cnt_skipped > 0)
          Flash::add("warning", Yii::t('app', 'The {num_skipped} files that are still in the \'/uploads/ftp\' folder cannot be imported and should therfore be manually removed!', array("{total}" => $cnt_added + $cnt_skipped, "{num}" => $cnt_added, "{num_skipped}" => $cnt_skipped)), true);
        
        $this->redirect("uploadprocess");
      }
    }
    
    if (!Yii::app()->getRequest()->getIsPostRequest()) 
      $model->batch_id = "B-" . date('Y-m-d-H:i:s');
    
    $this->render('uploadftp', array(
      'model' => $model,
    ));
  }
  
  public function actionUploadProcess() {
    $this->layout='//layouts/column1';  
    
    $model = new Image('search');
    $model->unsetAttributes();

    if (isset($_GET['Image']))
      $model->setAttributes($_GET['Image']);

    $this->render('uploadprocess', array(
      'model' => $model,
    ));
  }
  
  public function actionDelete($id) {
    if (Yii::app()->getRequest()->getIsPostRequest()) {
      $model = $this->loadModel($id, 'Image');
      if ($model->hasAttribute("locked") && $model->locked) {
        throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
      } else {
        $model->delete();
        MGHelper::log('delete', 'Deleted Image with ID(' . $id . ')');
        
        Flash::add('success', Yii::t('app', "Image deleted"));

        if (!Yii::app()->getRequest()->getIsAjaxRequest())
          $this->redirect(array('uploadprocess'));
      }
    } else
      throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
  }
  
  public function actionBatch($op) {
    $this->layout='//layouts/column1';
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
  
  private function _batchDelete() {
    if (isset($_POST['image-ids'])) {
      $images = Image::model()->findAllByPk($_POST['image-ids']);
      
      if ($images) {  
        foreach ($images as $image) {
          $image->delete();
        }
      }
      MGHelper::log('batch-delete', 'Batch deleted Images with IDs(' . implode(',', $_POST['image-ids']) . ')');
    } 
  }
  
  private function _batchProcess() {
    $errors = array();
    
    if (isset($_POST['image-ids'])) {
      $images = Image::model()->findAllByPk($_POST['image-ids']);
      
      if ($images) {
        $firstModel = $images[0];
        
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
                $plugin->component->process($images);
              }
            }  
          }
          
          foreach ($images as $image) {
            $image->locked = 1;
            $image->save(); 
          }
          
          MGHelper::log('batch-import-process', 'Batch processed Image with IDs(' . implode(',', $_POST['image-ids']) . ')');  
          Flash::add('success', Yii::t('app', 'Processed {count} images with the IDs({ids})', array("{count}" => count($_POST['image-ids']), "{ids}" => implode(',', $_POST['image-ids']))));
        }
      }
    } else {
      $errors["noImages"] = array(Yii::t('ui','Please check at least one image you would like to process!'));
    }

    if (count($errors) > 0) {
      if (Yii::app()->getRequest()->getIsAjaxRequest()) {
        $this->jsonResponse($errors);
      } else {
        $model = new Image('search');
        $model->unsetAttributes();
        
        if (isset($_GET['Image']))
          $model->setAttributes($_GET['Image']);
        
        $model->addErrors($errors);
    
        $this->render('uploadprocess', array(
          'model' => $model,
        ));
      }
      Yii::app()->end();
    }
  }
  
  public function actionXUploadImage() {
    $info = array();  
      
    $this->checkUploadFolder();
    
    
    $model = new XUploadForm;
    $model->file = CUploadedFile::getInstance($model, 'file');
    
    if (isset($model->file) && isset($_POST["batch_id"]) && trim($_POST["batch_id"]) != "") {
      $model->mime_type = $model->file->getType();
      $model->size = $model->file->getSize();
      
      // Remove path information and dots around the filename, to prevent uploading
      // into different directories or replacing hidden system files.
      // Also remove control characters and spaces (\x00..\x20) around the filename:
      $model->name = trim(basename(stripslashes($model->file->getName())), ".\x00..\x20");
  
      if ($model->validate()) {
        $path = $this->path . "/" . $this->subfolder."/";
        if(!is_dir($path)){
          mkdir($path);
        }
        
        $model->name = $this->checkFileName($path, $model->name);
        
        $model->file->saveAs($path . $model->name);
        
        $this->createImage($model->name, $model->size, $_POST["batch_id"], $model->mime_type);
        
        $info[] = array(
          'tmp_name' => $model->file->getName(),
          'name' => $model->name,
          'size' => $model->size,
          'type' => $model->mime_type,
          'thumbnail_url' => Yii::app()->getBaseUrl() . Yii::app()->fbvStorage->get('settings.app_upload_url') . "/thumbs/". $model->name,  
          'error' => null
        );
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
  
  private function checkFileName($path, $file_name) {
    $replace="_";
    $pattern="/([[:alnum:]_\.-]*)/";
    $file_name=str_replace(str_split(preg_replace($pattern,$replace,$file_name)),$replace,$file_name);
    
    $path_parts = pathinfo($file_name);
    
    if(file_exists($path.$file_name)) {
      $c = 1;
      $name = $path_parts['filename'] . "_" . $c;
      while(file_exists($path.$name . "." . $path_parts['extension'])) {
        $c++;  
        $name = $path_parts['filename'] . "_" . $c;
      }
      $file_name = $name . "." . $path_parts['extension'];
    }
    
    return $file_name;
  }
  
  private function createImage($file_name, $size, $batch_id, $mime_type) {
    $image = new Image;
    $image->name = $file_name;
    $image->size = $size;
    $image->batch_id = $batch_id;
    $image->mime_type = $mime_type;
    $image->created = date('Y-m-d H:i:s'); 
    $image->modified = date('Y-m-d H:i:s');
    $image->locked = 0; 
    
    $relatedData = array(
      'imageSets' => array(1),
    );
    $image->saveWithRelated($relatedData); 
    
    MGHelper::log('import-uploadfromlocal', 'Created Image with ID(' . $image->id . ')');
    
    $format = Yii::app()->fbvStorage->get("image.formats.thumbnail", 
      array (
        "width" => 70,
        "height" => 50,
        "quality" => FALSE, // set to integer 0 ... 100 to activate quality rendering
        "sharpen" => FALSE, // set to integer 0 ... 100 to activate sharpen
      ));
    
    MGHelper::createScaledImage($file_name, $file_name, 'thumbs', $format["width"], $format["height"], $format["quality"], $format["sharpen"]);        
  }
  
  private function checkUploadFolder() {
    if(!isset($this->path)){
      $this->path = realpath(Yii::app()->getBasePath() . Yii::app()->fbvStorage->get("settings.app_upload_path"));
    }
    
    if(!is_dir($this->path)){
      throw new CHttpException(500, "{$this->path} does not exists.");
    }else if(!is_writable($this->path)){
      throw new CHttpException(500, "{$this->path} is not writable.");
    }
  }

}