<?php

class ExportController extends GxController {
  public $defaultAction = 'admin';
  
  /**
   * Full path of the export folder.
   * @var string
   */
  public $path;
  
  /**
   * Full path of the temporary folder.
   * @var string
   */
  public $tmp_path;
  
  public function filters() {
  	return array(
      'IPBlock',
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
  				'actions'=>array('admin', 'exported', 'queueprocess', 'remove'),
  				'roles'=>array('editor', 'xxx'),
  				),
  			array('deny', 
  				'users'=>array('*'),
  				),
  			);
  }

	public function actionAdmin() {
	  Yii::app()->clientScript->registerCoreScript('jquery');
    Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/mg.api.js', CClientScript::POS_END);
    Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . '/css/jquery.fancybox-1.3.4.css');
    Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.fancybox-1.3.4.pack.js', CClientScript::POS_END);
   
	  $this->_checkExportFolder();
    $this->_checkFilesInExportFolder();
    $count_affected_images = -1;
    
    $model = new ExportForm;
    
    $this->performAjaxValidation($model, 'export-form');

    if (isset($_POST['ExportForm'])) {
      $model->setAttributes($_POST['ExportForm']);

      if ($model->validate()) {
        // check whether file exists
        
        $list = CFileHelper::findFiles($this->path);
        foreach ($list as $file) {
          $file_info = pathinfo($file);
          if ($file_info['basename'] == $model->filename . '.zip') {
            $model->addError('filename', Yii::t('app', 'A file with that filename already exists. Please choose another name'));
            break;
          }
        }

        if (!$model->hasErrors()) {
          $command = $this->_createCommand($model);
          $model->affected_images = $this->_getAffectedImages($command);
          $count_affected_images = count($model->affected_images);
        }
      }
    }
    
    if ($count_affected_images == 0) {
      $model->addError('all', Yii::t('app', 'No images found. Please change the form settings and try again.'));
    }
    
	  $this->render('admin', array(
	   'model' => $model,
	   'count_affected_images' => $count_affected_images
    ));
	}
  
  public function actionQueueProcess($action) {
    switch ($action) {
      case 'export':
        $this->_processExportQueue();
        break; 
    }
  }
  
  private function _processExportQueue() {
    $data = array();
    $data['status'] = 'ok';
    
    $this->_checkExportFolder();
    $count_affected_images = 0;
    
    $model = new ExportForm;
    
    if (isset($_POST['ExportForm'])) {
      $model->setAttributes($_POST['ExportForm']);

      if ($model->validate()) {
        $file_name = $model->filename . '.zip';
        
        $list = CFileHelper::findFiles($this->path);
        foreach ($list as $file) {
          $file_info = pathinfo($file);
          if ($file_info['basename'] == $file_name) {
            throw new CHttpException(401, Yii::t('app', 'Invalid request.'));
          }
        }
        
        if ((int)$model->active_image != 0) {
          $plugins = PluginsModule::getAccessiblePlugins("export");
          $command = $this->_createCommand($model);
          $tmp_folder = $this->tmp_path  . $model->filename . '/';
          
          if ((int)$model->active_image > 0) {

            if(!is_dir($tmp_folder)){
              mkdir($tmp_folder);
              chmod($tmp_folder, 0777);
              
              if (count($plugins) > 0) {
                try {
                  foreach ($plugins as $plugin) {
                    if (method_exists($plugin->component, "preProcess")) {
                      $plugin->component->preProcess($model, $command, $tmp_folder);
                    }      
                  }
                } catch (Exception $e) {}
              } 
            }
  
            if (count($plugins) > 0) {
              try {
                foreach ($plugins as $plugin) {
                  if (method_exists($plugin->component, "process")) {
                    $plugin->component->process($model, $command, $tmp_folder, (int)$model->active_image);
                  }      
                }
              } catch (Exception $e) {}
            }
            
            $data['status'] = 'retry';
            
          } else {
            
            if (count($plugins) > 0) {
              try {
                foreach ($plugins as $plugin) {
                  if (method_exists($plugin->component, "postProcess")) {
                    $plugin->component->postProcess($model, $command, $tmp_folder);
                  }      
                }
              } catch (Exception $e) {}
            }
            
            // make sure all files are accessible via ftp
            $list = CFileHelper::findFiles($tmp_folder);
            foreach ($list as $file) {
              chmod($file, 0777);
            }
            
            // attempt to create the zip file within the exported folder
            Yii::app()->zip->createZip($tmp_folder, $this->path  . $model->filename . '.zip' );
            
            if (file_exists($this->path  . $model->filename . '.zip')) {
              MGHelper::rrmdir($tmp_folder);
              if (is_dir($tmp_folder)) {
                rmdir($tmp_folder);
              }
              $this->_finishExportQueue($model->filename . '.zip');
            } else {
              $data['status'] = 'error';
              $data['message'] = Yii::t('app', 'The zip file could not be created. You can find all exported files on the server under /uploads/tmp/' . $model->filename . '/');
            }
          }
        } else {
          throw new CHttpException(401, Yii::t('app', 'Invalid request.'));
        }
      } else {
        throw new CHttpException(401, Yii::t('app', 'Invalid request.'));
      }
    } else {
      throw new CHttpException(401, Yii::t('app', 'Invalid request.'));
    }
    
    $this->jsonResponse($data);
  }
  
  private function _finishExportQueue($filename) {
    $data['status'] = 'done';
    $data['redirect'] = Yii::app()->createUrl('admin/export/exported');
    
    Flash::add("success", Yii::t('app', 'Image successfully exported. The export is ready to download as in this {file}', array("{file}" => $filename)));
    $this->jsonResponse($data);
  }

  private function _createCommand($model) {
    $where = array('and');
    $params = array();
    $group = array();
    $having = array('and');
    $having_params = array();
    
    $command = Yii::app()->db->createCommand()
                ->from('{{tag_use}} tu')
                ->join('{{tag}} t', 't.id=tu.tag_id')
                ->join('{{image}} i', 'i.id=tu.image_id')
                ->join('{{game_submission}} gs', 'gs.id=tu.game_submission_id')
                ->join('{{session}} s', 's.id=gs.session_id')
                ->join('{{user}} u', 'u.id=s.user_id');

    if ($model->tags) {
      $parsed_tags = MGTags::parseTags($model->tags);
      if (count($parsed_tags) > 0) {
        $cmd =  Yii::app()->db->createCommand();
        $images = null;
        $subwhere = array('and');
        $subparams = array();
        
        $subwhere[] = array('in', 'tag.tag', array_values($parsed_tags));
        
        if ($model->tag_weight_min && (int)$model->tag_weight_min >= 0) {
          $subwhere[] = 'tu.weight >= :weight';
          $subparams[':weight'] = (int)$model->tag_weight_min;
        } else {
          $subwhere[] = 'tu.weight > 0';
        }
        
        if ($model->tags_search_option == "OR") {
          $images = $cmd->selectDistinct('tu.image_id')
                  ->from('{{tag_use}} tu')
                  ->join('{{tag}} tag', 'tu.tag_id = tag.id')
                  ->where($subwhere, $subparams)
                  ->queryAll();
        } else {
          $images = $cmd->selectDistinct('tu.image_id, COUNT(DISTINCT tu.tag_id) as counted')
                  ->from('{{tag_use}} tu')
                  ->join('{{tag}} tag', 'tu.tag_id = tag.id')
                  ->where($subwhere, $subparams)
                  ->group('tu.image_id')
                  ->having('counted = :counted', array(':counted' => count($parsed_tags)))
                  ->queryAll();
        }
        
        if ($images) {
          $ids = array();
          foreach ($images as $image) {
            $ids[] = $image["image_id"];
          }
          $where[] = array('in', 'tu.image_id', array_values($ids));
        } else {
          $where[] = array('in', 'tu.image_id', array_values(0));
        }
      }
    }
    
    if ($model->players) {
      $parsed_players = MGTags::parseTags($model->players);
      if (count($parsed_players) > 0) {
        $cmd =  Yii::app()->db->createCommand()
                ->from('{{tag_use}} tu')
                ->join('{{game_submission}} gs', 'gs.id=tu.game_submission_id')
                ->join('{{session}} s', 's.id=gs.session_id')
                ->join('{{user}} u', 'u.id=s.user_id');
        $users = null;
        $subwhere = array('and');
        $subparams = array();
        
        $subwhere[] = array('in', 'u.username', array_values($parsed_players));
        
        if ($model->tag_weight_min && (int)$model->tag_weight_min >= 0) {
          $subwhere[] = 'tu.weight >= :weight';
          $subparams[':weight'] = (int)$model->tag_weight_min;
        } else {
          $subwhere[] = 'tu.weight > 0';
        }
        
        if ($model->tags_search_option == "OR") {
          $users = $cmd->selectDistinct('tu.image_id')
                  ->where($subwhere, $subparams)
                  ->queryAll();
        } else {
          $users = $cmd->selectDistinct('tu.image_id, COUNT(DISTINCT u.username) as counted')
                  ->where($subwhere, $subparams)
                  ->group('tu.image_id')
                  ->having('counted = :counted', array(':counted' => count($parsed_tags)))
                  ->queryAll();
        }
        
        if ($users) {
          $ids = array();
          foreach ($users as $user) {
            $ids[] = $user["image_id"];
          }
          $where[] = array('in', 'tu.image_id', array_values($ids));
        } else {
          $where[] = array('in', 'tu.image_id', array_values(0));
        }
      }
    }
    
    if ($model->imageSets) {
      $command->join('{{image_set_to_image}} isi', 'isi.image_id=tu.image_id');
      $where[] = array('in', 'isi.image_set_id', array_values($model->imageSets));
    }
    
    if ($model->tag_weight_min && (int)$model->tag_weight_min >= 0) {
      $where[] = 'tu.weight >= :weight';
      $params[':weight'] = (int)$model->tag_weight_min;
    } else {
      $where[] = 'tu.weight > 0';
    }
    
    if ((int)$model->tag_weight_sum >= 0) {
      $group[] = 'tu.tag_id';
      $having[] = 'SUM(tu.weight) >= :weightSum';
      $having_params[':weightSum'] = (int)$model->tag_weight_sum;
    }
    
    if (trim((string)$model->created_after) != "") {
      $where[] = 'tu.created >= :after';
      $params[':after'] = (int)$model->created_after;
    } 
    
    if (trim((string)$model->created_before) != "") {
      $where[] = 'tu.created <= :before';
      $params[':before'] = (int)$model->created_before;
    } 
    
    $command->where($where, $params);
    
    if (count($group)) {
      $command->group(implode(',', $group));
    }
    
    if (count($having) > 1) {
      
      $command->having($having, $having_params);
    }
    
    return $command;
  }
  
  private function _getAffectedImages(&$command) {
    $ids = array();  
    $image_ids = $command->selectDistinct('tu.image_id as id')->group('tu.tag_id, tu.image_id')->queryAll();
    if ($image_ids) {
      $c= count($image_ids);
      for($i=0;$i<$c;$i++) {
        $ids[] = $image_ids[$i]['id'];
      }
    }
    return $ids;
  }
  
  
  public function actionExported() {
    $this->_checkExportFolder();
    
    $filelist = array();
    $list = CFileHelper::findFiles($this->path);
    foreach ($list as $file) {
      $file_info = pathinfo($file);
      if ($file_info['basename'] != '.gitignore') {
        $filelist[] = array(
          'id' => $file_info['basename'],
          'name' => $file_info['basename'],
          'created' => date ("Y-m-d H:i:s", filemtime($file_info['dirname'] . '/' . $file_info['basename'])),
          'link' => CHtml::link(Yii::t('app', 'download'), Yii::app()->getBaseUrl() . Yii::app()->fbvStorage->get('settings.app_upload_url') . "/export/" . $file_info['basename'])
        );
      }
    }
    
    $filelist_dataprovider = new CArrayDataProvider($filelist, array(
      'id'=>'id',
      'keyField' => 'name',
      'sort'=>array(
        'attributes'=>array(
          'name', 'created'
        ),
      ),
      'pagination'=>array(
          'pageSize'=> Yii::app()->fbvStorage->get("settings.pagination_size")
      ),
    ));
    
    $this->render('exported', array(
      'filelist_dataprovider' => $filelist_dataprovider
    ));
  }
  
  public function actionRemove($id) {
    $this->_checkExportFolder();
    
    $list = CFileHelper::findFiles($this->path);
    foreach ($list as $file) {
      $file_info = pathinfo($file);
      if ($file_info['basename'] == $id) {
        unlink($file_info['dirname'] . '/' . $file_info['basename']);
        MGHelper::log('delete', 'Deleted export file with NAME(' . $id . ')');
        Flash::add('success', Yii::t('app', 'File {name} removed', array("{name}" => $id)));
        break;
      }
    }
    $this->redirect(array('exported'));
  }
  
  private function _checkExportFolder() {
    if(!isset($this->path)){
      $this->path = realpath(Yii::app()->getBasePath() . Yii::app()->fbvStorage->get("settings.app_upload_path"));
    }
    
    if(!is_dir($this->path)){
      throw new CHttpException(500, "{$this->path} does not exists.");
    }else if(!is_writable($this->path)){
      throw new CHttpException(500, "{$this->path} is not writable.");
    }
    
    $this->tmp_path = $this->path . "/tmp/";
    if(!is_dir($this->tmp_path )){
      mkdir($this->tmp_path);
      chmod($this->tmp_path, 0777);
    }
    
    $this->path .= "/export/";
    if(!is_dir($this->path )){
      mkdir($this->path);
      chmod($this->path, 0777);
    }
  }
  
  private function _checkFilesInExportFolder() {
    $list = CFileHelper::findFiles($this->path);
    
    $count_files = 0;
    foreach ($list as $file) {
      $file_info = pathinfo($file);
      if ($file_info['basename'] != '.gitignore') {
        $count_files++;
      }
    }
    
    if ($count_files > 5) {
      $link = CHtml::link(Yii::t('app', 'here'), 'export/exported');
      Flash::add("warning", Yii::t('app', 'There are {count_files} files in the export folder. You can download and remove them {link}', array("{count_files}" => $count_files, "{link}" => $link)), true);
    }
  }
}