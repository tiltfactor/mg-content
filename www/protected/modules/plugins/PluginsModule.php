<?php

class PluginsModule extends CWebModule
{
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'plugins.models.*',
			'plugins.components.*',
			'plugins.modules.dictionary.components.*',
			'plugins.modules.dictionary.models.*',
			'plugins.modules.import.components.*',
			'plugins.modules.import.models.*',
			'plugins.modules.export.components.*',
			'plugins.modules.export.models.*',
			'plugins.modules.weighting.components.*',
			'plugins.modules.weighting.models.*',
		));
    
    // loop through all active plugins and generate the list
    $this->setModules(array(
      'dictionary',
      'import',
      'export',
      'weighting',
    ));
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
  
  public static function pluginAdminLink($uid, $label="") {
    try {
      $component = Yii::createComponent(self::getPluginClassName($uid));
      if ($component->hasAdmin) {
        if ($component->adminPath != "") {
          print "here";
          $url = $component->adminPath;
        } else {
          $info = split("-", $uid);
          $type = $info[0];
          $class = $info[1];
          $controller = str_replace("Plugin", "", $class);
          $url = array("/plugins/$type/$controller");  
        }
        return CHtml::link(Yii::t('app','Manage') . ' '  . (($label != "")? $label : $controller . " Plugin"), $url);          
      }
    } catch (Exception $e) {}
    return "";
  }
  
  public static function getPluginClassName($uid) {
    $info = split("-", $uid);
    return $info[1];
  }
  
  /**
   * This method lists all active plug-ins the current user has got access to.
   */
  public static function getAccessiblePlugins($active=1) {
    $plugins = Plugin::model()->findAll('active=:a', array(':a'=>$active));
    $list = array();
    foreach ($plugins as $plugin) {
      try {
        $info = split("-", $plugin->unique_id);
        $type = $info[0];
        $class = $info[1];
        $component_name = str_replace("Plugin", "", $class);
        $component = Yii::createComponent($class);
        
        if (Yii::app()->user->checkAccess($component->accessRole)) {
          $list[] = (object) array('id' => null, 'name' => $component_name, 'link' => self::pluginAdminLink($plugin->unique_id));    
        }
      } catch (Exception $e) {}
    }
    return $list;
  }
}
