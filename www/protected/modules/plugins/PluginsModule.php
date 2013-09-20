<?php

class PluginsModule extends CWebModule
{
    private static $plugins = array('import-CollectionAtImportPlugin');

    public function init()
    {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application

        // import the module-level models and components
        $this->setImport(array(
            'plugins.models.*',
            'plugins.components.*',
            'plugins.modules.import.components.*',
        ));

        // loop through all active plugins and generate the list
        $this->setModules(array(
            'import',
            'export',
        ));
    }

    public function beforeControllerAction($controller, $action)
    {
        if (parent::beforeControllerAction($controller, $action)) {
            // this method is called before any module controller action is performed
            // you may place customized code here
            return true;
        } else
            return false;
    }

    /**
     * Creates a link to the plugin settings page
     *
     * @param string $uid Unique ID of the plugin
     * @param string $label Optional label for the link
     * @return string Html partail link to plugin settings or empty
     */
    public static function pluginAdminLink($uid, $label = "")
    {
        try {
            $component = Yii::createComponent(self::getPluginClassName($uid));
            if ($component->hasAdmin) {
                if ($component->adminPath != "") {
                    $url = $component->adminPath;
                } else {
                    $info = explode("-", $uid);
                    $type = $info[0];
                    $class = $info[1];
                    $controller = str_replace("Plugin", "", $class);
                    $url = array("/plugins/$type/$controller");
                }
                return CHtml::link(Yii::t('app', 'Manage') . ' ' . (($label != "") ? $label : $controller . " Plugin (Settings)"), $url);
            }
        } catch (Exception $e) {
        }
        return "";
    }

    /**
     * Returns the class name of a plugin extracted from the unique id
     *
     * @param string $uid Unique ID of the plugin
     * @return string The classname
     */
    public static function getPluginClassName($uid)
    {
        $info = explode("-", $uid);
        return $info[1];
    }

    /**
     * This method lists all active plug-ins the current user has got access to.
     *
     * @param string $type Filter list only plugins of this type
     * @param int $active Filter show active inactive plugins. Defaults to active. Set to 0 to show inactive
     * @return Array List of active plugins or empty
     */
    public static function getAccessiblePlugins($type = null, $active = 1)
    {

        Yii::import('application.modules.plugins.models.*');
        Yii::import('application.modules.plugins.components.*');
        Yii::import('application.modules.plugins.modules.import.components.*');
        $list = array();
        foreach (PluginsModule::$plugins as $plugin) {
            try {

                $info = explode("-", $plugin);
                $plugin_type = $info[0];
                $plugin_class = $info[1];

                $component_name = str_replace("Plugin", "", $plugin_class);
                $component = Yii::createComponent($plugin_class);

                if (Yii::app()->user->checkAccess($component->accessRole)) {
                    if (is_null($type) || $type == $plugin_type) {
                        $list[] = (object)array(
                            'type' => $plugin_type,
                            'name' => $component_name,
                            'link' => self::pluginAdminLink($plugin->unique_id),
                            'class' => $plugin_class,
                            'component' => $component
                        );
                    }
                }
            } catch (Exception $e) {
            }
        }

        return $list;
    }
}
