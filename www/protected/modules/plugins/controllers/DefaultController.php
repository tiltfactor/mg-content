<?php
/**
 * The controler used for the plugin management. It allows to list all 
 * registered plugins and register new plugins by scanning the file system 
 * for new plugins in the modules 'modules' folder. It also renders all needed
 * interfaces to allow to update the plugins settings.   
 */
class DefaultController extends GxController
{
	public $defaultAction = 'admin';
  
	public function filters() {
    return array(
      'IPBlock',
      'accessControl', 
     );
  }
  
  public function accessRules() {
    return array(
        array('allow',
          'actions'=>array('view', 'admin'),
          'roles'=>array('editor', 'dbmanager', 'admin'),
          ),
        array('allow', 
          'actions'=>array('create', 'update','delete'),
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
        Flash::add('success', Yii::t('app', "Plugin updated"));
        $this->redirect(array('view', 'id' => $model->id));
      }
    }

    $this->render('update', array(
        'model' => $model,
        ));
  }
  
  public function actionDelete($id) {
    if (Yii::app()->getRequest()->getIsPostRequest()) {
      $model = $this->loadModel($id, 'Plugin');
      $class = PluginsModule::getPluginClassName($model->unique_id);
      $model->delete();
      try {
        $component = Yii::createComponent($class);
        $component->uninstall();
        Flash::add('success', Yii::t('app', "Plugin uninstalled")); 
      } catch (Exception $e) {
        Flash::add("error", Yii::t('app', "The uninstall method of the plugin of type {$listed_plugin->type} with the unique id {$listed_plugin->unique_id} could not be called!"), TRUE);
      }
      if (!Yii::app()->getRequest()->getIsAjaxRequest())
        $this->redirect(array('admin'));
    } else
      throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
  }

  public function actionAdmin() {
    $this->layout = '//layouts/column1';
    
    $model = new Plugin();
    
    if (Yii::app()->user->checkAccess('admin')) {
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
  
      $this->render('admin-admin', array(
        'model' => $model,
        'type_filter' => $type_filter,
      ));
    } else {
      $dataProvider=new CArrayDataProvider(PluginsModule::getAccessiblePlugins(), array(
        'id'=>'user',
        'sort'=>array(
            'attributes'=>array(
                 'name',
            ),
        ),
        'pagination'=>array(
            'pageSize'=>10,
        ),
      ));
      
      $this->render('admin-editor', array(
        'model' => $model,
        'dataProvider' => $dataProvider,
      ));
    }
  }
  
  /**
   * Scans the folder for available plug-ins.
   * If a new plug-in has been added it will add it to the database. 
   * If a plug-in has been removed the system will display an error 
   */
  protected function refreshPlugins($directories, $path) {
    
    $available_plugins = array();
    foreach ($directories as $dir) {
      $arr_files = glob($dir . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "*Plugin.php");
      if (is_array($arr_files) && count($arr_files)) {
        foreach ($arr_files as $file) {
          $available_plugins[] = array("type" => str_replace($path, "", $dir), "uid"=> str_replace($path, "", $dir) . "-" . str_replace(".php", "", basename($file)), "class"=>str_replace(".php", "", basename($file)));
        }  
      }
    }
        
    $listed_plugins = Plugin::model()->findAll();
    foreach ($available_plugins as $available_plugin) {
      $found = FALSE;
      foreach ($listed_plugins as $listed_plugin) {
      	if ($listed_plugin->unique_id == $available_plugin["uid"]) {
      	  $found = TRUE;
      	  break;
      	} 
      }
      if(!$found &&
	     $this->addPlugin($available_plugin)) {
        	Flash::add("success",
        		   Yii::t('app',
        			  "New plugin of type {$available_plugin['type']} " .
        			  "with the unique id {$available_plugin['uid']} " .
        			  "registered."));
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
  }

  protected function addPlugin($plugin) {
    $model = new Plugin;
    $model->created = date('Y-m-d H:i:s');
    $model->modified = date('Y-m-d H:i:s');
    $model->type = $plugin["type"];
    $model->unique_id = $plugin["uid"];
    $model->active = 0;
    
    $installed = FALSE;
    try {
      $component = Yii::createComponent($plugin["class"]);
      $installed = $component->install(); 
      
      if ($component->enableOnInstall)
        $model->active = 1;
      
    } catch (Exception $e) {
      var_dump($e);
      Flash::add("error", Yii::t('app', "The install method of the plugin of type {$model->type} with the unique id {$model->unique_id} could not be called!"), TRUE);
    }
        
    return $model->save() && $installed;  
  }
}