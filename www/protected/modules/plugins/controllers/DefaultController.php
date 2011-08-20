<?php

class DefaultController extends GxController
{
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
          'actions'=>array('index','view', 'minicreate', 'create','update', 'admin','delete'),
          'roles'=>array('admin'),
          ),
        array('deny', 
          'users'=>array('*'),
          ),
        );
  }

  public function actionView($id) {
    $this->render('view', array(
      'model' => $this->loadModel($id, 'Plugin'),
    ));
  }

  public function actionUpdate($id) {
    $model = $this->loadModel($id, 'Plugin');
    $model->modified = date('Y-m-d H:i:s');
    $this->performAjaxValidation($model, 'plugin-form');

    if (isset($_POST['Plugin'])) {
      $model->setAttributes($_POST['Plugin']);

      if ($model->save()) {
        $this->redirect(array('view', 'id' => $model->id));
      }
    }

    $this->render('update', array(
        'model' => $model,
        ));
  }
  
  public function actionDelete($id) {
    // xxx delete settings
    if (Yii::app()->getRequest()->getIsPostRequest()) {
      $this->loadModel($id, 'Plugin')->delete();

      if (!Yii::app()->getRequest()->getIsAjaxRequest())
        $this->redirect(array('admin'));
    } else
      throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
  }

  public function actionIndex() {
    $this->actionAdmin();
  }

  public function actionAdmin() {
    $this->layout = '//layouts/column1';
    
    $types = array();
    $type_filter = array();
    $path = Yii::getPathOfAlias('application.modules.plugins.modules') . DIRECTORY_SEPARATOR;
    if (is_dir($path)) {
      foreach (glob($path . "*") as $dir) {
        if (array_key_exists(basename($dir), Yii::app()->controller->module->getModules())) {
          if (is_dir($dir)) {
            $types[] = $dir;    
          }  
        }
      }  
      if (count($types) > 0) {
        $this->refreshPlugins($types, $path);
        foreach ($types as $dir) {
          $type_filter[basename($dir)] = basename($dir);
        }
      }
    }
    

    $model = new Plugin('search');
    $model->unsetAttributes();

    if (isset($_GET['Plugin']))
      $model->setAttributes($_GET['Plugin']);

    $this->render('admin', array(
      'model' => $model,
      'type_filter' => $type_filter,
    ));
  }
  
  /**
   * Scans the folder for available plug-ins.
   * If a new plug-in has been added it will add it to the database. 
   * If a plug-in has been removed the system will display an error 
   */
  protected function refreshPlugins($directories, $path) {
    
    $available_plugins = array();
    foreach ($directories as $dir) {
      foreach (glob($dir . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "*Plugin.php") as $file) {
        $available_plugins[] = array("type" => str_replace($path, "", $dir), "uid"=> str_replace($path, "", $dir) . "-" . str_replace(".php", "", basename($file)), "class"=>str_replace(".php", "", basename($file)));
      }  
    }
        
    $listed_plugins = Plugin::model()->findAll();
    foreach ($available_plugins as $available_plugin) {
      if (count($listed_plugins) > 0) {
        $found = FALSE;
        foreach ($listed_plugins as $listed_plugin) {
          if ($listed_plugin->unique_id == $available_plugin["uid"]) {
            $found = TRUE;
            break;
          } 
        }
        if(!$found) {
          if ($this->addPlugin($available_plugin))
            Flash::add("success", Yii::t('app', "New plugin of type {$available_plugin['type']} with the unique id {$available_plugin['uid']} registerd."));
        }
      } else {
        if ($this->addPlugin($available_plugin))
          Flash::add("success", Yii::t('app', "New plugin of type {$available_plugin['type']} with the unique id {$available_plugin['uid']} registerd."));
      }
    }
    
    
    foreach ($listed_plugins as $listed_plugin) {
      $found = FALSE;
      foreach ($available_plugins as $available_plugin) {
        if ($listed_plugin->unique_id == $available_plugin["uid"]) {
          $found = TRUE;
          break;
        } 
      }
      
      if (!$found) {
        $listed_plugin->active = 0;
        $listed_plugin->save();
        Flash::add("error", Yii::t('app', "The plugin of type {$listed_plugin->type} with the unique id {$listed_plugin->unique_id} is registered in the database but its code is either not accessible in the file system or not registered in the plugins module. It has been automatically disabled!"), TRUE);
      }
    }
    
    /*
     * get all plugins as array;
     * 
     * for each folder in modules 
     *  look into components 
     *    each file that has name Plugin.php
     *      take name and look up in database
     *    
     *    if plugin present fine
     * 
     *    if plugin in db but not in file raise erro
     * 
     *    if plugin in folder but not db add new entry  
     * 
     */
  }

  protected function addPlugin($plugin) {
    $model = new Plugin;
    $model->created = date('Y-m-d H:i:s');
    $model->modified = date('Y-m-d H:i:s');
    $model->type = $plugin["type"];
    $model->unique_id = $plugin["uid"];
    $model->active = 0;
    
    
    $component = new $plugin["class"]();
    
        
    return $model->save() && $component->install();  
  }
}