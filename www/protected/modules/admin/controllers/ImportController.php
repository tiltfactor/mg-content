<?php
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
  public $subfolder="image";
  
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
  				'actions'=>array('index', 'importsettings', 'uploadfromlocal', 'xuploadimage'),
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
      
      $tools["import-process"] = array(
                              "name" => Yii::t('app', "Process Imported Images"),
                              "description" => Yii::t('app', "Some short description"),
                              "url" => $this->createUrl('/admin/import/processimportedimages'),
                           );
                           
      if (Yii::app()->user->checkAccess('dbmanager')) {
      }
                           
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
    
    $model = new JQueryUiUploadForm;
    $this->render('uploadfromlocal', array(
      'model' => $model,
    ));
  }
  
  public function actionXUploadImage() {
    $info = array();  
      
    $this->checkUploadFolder();
    
    $model = new JQueryUiUploadForm;
    $model->file = CUploadedFile::getInstance($model, 'file');
    
    if (isset($model->file)) {
      $model->mime_type = $model->file->getType();
      $model->size = $model->file->getSize();
      
      // Remove path information and dots around the filename, to prevent uploading
      // into different directories or replacing hidden system files.
      // Also remove control characters and spaces (\x00..\x20) around the filename:
      $model->name = trim(basename(stripslashes($model->file->getName())), ".\x00..\x20");
  
      if ($model->validate()) {
        Yii::log("drei", "error");
        $path = $this->path . "/" . $this->subfolder."/";
        if(!is_dir($path)){
          mkdir($path);
        }

        $path_parts = pathinfo($model->name);
        
        if(file_exists($path.$model->name)) {
        
          $c = 1;
          $name = $path_parts['filename'] . "_" . $c;
          while(file_exists($path.$name . "." . $path_parts['extension'])) {
            $c++;  
            $name = $path_parts['filename'] . "_" . $c;
            
          }
          $model->name = $name . "." . $path_parts['extension'];
        }
        
        $model->file->saveAs($path.$model->name);
        
        $image = new Image;
        $image->name = $model->name;
        $image->size = $model->size;
        $image->mime_type = $model->mime_type;
        $image->created = date('Y-m-d H:i:s'); 
        $image->modified = date('Y-m-d H:i:s');
        $image->locked = 0; 
        $image->save();
        
        // create thumbnail
        $imgCPNT = Yii::app()->image->load($path.$model->name);
        
        $format = Yii::app()->fbvStorage->get("image.formats.thumbnail", 
          array (
            "width" => 70,
            "height" => 50,
            "quality" => FALSE, // set to integer 0 ... 100 to activate quality rendering
            "sharpen" => FALSE, // set to integer 0 ... 100 to activate sharpen
          ));
        
        $imgCPNT->resize($format["width"], $format["height"], KImage::AUTO);
        if ($format["quality"] && (int)$format["quality"] != 0)  
          $imgCPNT->quality((int)$format["quality"]);
        if ($format["sharpen"] && (int)$format["sharpen"] != 0)  
          $imgCPNT->sharpen((int)$format["sharpen"]);
        
        $imgCPNT->save($this->path . '/thumbs/' . $model->name);

        $info[] = array(
          'tmp_name' => $model->file->getName(),
          'name' => $image->name,
          'size' => $image->size,
          'type' => $image->mime_type,
          'thumbnail_url' => Yii::app()->getBaseUrl() . Yii::app()->params['upload_url'] . "/thumbs/". $model->name,  
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
    } else
      $info[] = array(
        'tmp_name' => null,
        'name' => null,
        'size' => null,
        'type' => null,
        'error' => 4
      );
      
    $this->jsonResponse($info);
  }

  private function checkUploadFolder() {
    if(!isset($this->path)){
      $this->path = realpath(Yii::app()->getBasePath() . Yii::app()->params['upload_path']);
    }
    
    if(!is_dir($this->path)){
      throw new CHttpException(500, "{$this->path} does not exists.");
    }else if(!is_writable($this->path)){
      throw new CHttpException(500, "{$this->path} is not writable.");
    }
  }
}