<?php
/**
 * Implements a file base persistent settings/variable storage. This allows to separate effectively 
 * configuration items from user generated data. This class can also be used to manipulate config files in 
 * application.config such as main.php
 * 
 * @author Vincent Van Uffelen <novazembla@gmail.com>
 * @link http://tiltfactor.org
 * @license AGPL 3.0 
 */

/**
 * By default, FBVStorage stores data in a file named 'fbvsettings.php' that is located
 * under the application in protected/data. All data is stored in an associative array.
 * 
 * You may change the location by setting the $settingsFile property.
 *
 */

class FBVStorage extends CApplicationComponent {
  /**
   * @var string the file path storing the state data. Make sure the directory containing
   * the file exists and is writable by the Web server process. If using relative path, also
   * make sure the path is correct.
   */
  public $settingsFile;
  
  /**
   * @var array this array holds the data .
   */
  protected static $data = array();
  
  /**
   * Initializes the component.
   * This method overrides the parent implementation by making sure {@link stateFile}
   * contains valid value.
   */
  public function init()
  {
    parent::init();
      
    if($this->settingsFile===null) 
      $this->settingsFile=Yii::getPathOfAlias('application.data.fbvsettings') . '.php';
      
    $dir=dirname($this->settingsFile);
    if(!is_dir($dir) || !is_writable($dir) || !is_writable($this->settingsFile))
      throw new CException(Yii::t('yii','Unable to create/write FBVStorage settings file "{file}". Make sure the directory containing the file exists and is writable by the Web server process.',
        array('{file}'=>$this->settingsFile)));
    
    $this->load();    
  }
  
  /**
   * Retrieves a value from the setting file. You can address several levels of the storage array by 
   * using a application path like syntax.
   * 
   * E.g. 'plugins.import.import-image.active' will return TRUE based on the following array
   * 
   * array (
   * 'plugins' => 
   * array(
   *   'import' =>
   *     array(
   *       array(
   *         'name' => 'import-image',
   *         'description' => 'import-image',
   *         'active' => TRUE,
   *         'settings' =>
   *         array(
   *         ),
   *       ),
   *     ),
   *   ),
   * );
   * 
   * @param string $scope the scope of the setting.
   * @param mixed $default the default value in case the setting is not set.
   * @return mixed the settings value
   */
  public function get($scope, $default=null) {
    if (!isset($scope) || is_null($scope)) 
      return $default;
    
    $value = $default;
    $scopes = explode(".", (string)$scope);
    $data = FBVStorage::$data;
    
    $c = count($scopes);
    $s = array();
    for ($i=0; $i<$c;$i++) {
      if (is_array($data)) {
        if ($i < $c-1 && array_key_exists($scopes[$i], $data)) {
          $data =& $data[$scopes[$i]];
        } else {
          if (array_key_exists($scopes[$i], $data)) {
            $value = $data[$scopes[$i]];
          } else {
            return $default;  
          } 
        }
        $s[] = $scopes[$i];
      } else {
        throw new CException(Yii::t('yii','Cannot read deeper than leaf of array. "{scope}" has no children.',
          array('{scope}'=>implode(".", $s))));
      }
    }
    return $value;
  }
  
  /**
   * Saves a value from to the setting file. You can address several levels of the storage array by 
   * using a application path like syntax. For each not registered scope element a new entry in the 
   * array will be created  
   * 
   * E.g. calling set() with set('plugins.import.import-image.active', FALSE) will set the value of 
   * 'active' in the array below from TRUE to FALSE
   * 
   * array (
   * 'plugins' => 
   * array(
   *   'import' =>
   *     array(
   *       array(
   *         'name' => 'import-image',
   *         'description' => 'import-image',
   *         'active' => TRUE,
   *         'settings' =>
   *         array(
   *         ),
   *       ),
   *     ),
   *   ),
   * );
   * 
   * @param string $scope the scope of the setting.
   * @param mixed $value the value to which the element should be set
   */
  public function set($scope, $value=null) {
    $scopes = explode(".", (string)$scope);
    $data =& FBVStorage::$data;
    
    $c = count($scopes);
    $s = array();
    for ($i=0; $i<$c;$i++) {
      if (is_array($data)) {
        if ($i < $c-1) {
          if (!array_key_exists($scopes[$i], $data)) {
            $data[$scopes[$i]] = array();
          }
          if (is_array($data)) {
            $data =& $data[$scopes[$i]];
          } else {
            throw new CException(Yii::t('yii','Cannot write onto leaf of array. "{scope}" has no children.',
              array('{scope}'=>implode(".", $s))));
          } 
        }
        if ($i == $c-1) {
          $data[$scopes[$i]] = $value;
        }
         $s[] = $scopes[$i];
      } else {
        throw new CException(Yii::t('yii','Cannot write onto leaf of array. "{scope}" has no children.',
          array('{scope}'=>implode(".", $s))));
      }
    }
    
    if ($c > 0)
      $this->saveToFile(FBVStorage::$data, $this->settingsFile);
  }
  
  /**
   * Loads the data from the settings file and merges the loaded data with the static array.
   * It will preserve changes in the static array
   */
  public function load()
  {
    $settings=$this->loadFromFile($this->settingsFile);
    FBVStorage::$data = array_merge($settings, FBVStorage::$data);
  }
  
  /**
   * Loads the settings data from a PHP script file.
   * @param string $file the file path.
   * @return array the settings data
   * @see saveToFile
   */
  protected function loadFromFile($file)
  {
    if(is_file($file))
      return require($file);
    else
      return array();
  }
  
  /**
   * Saves the settings data to a PHP script file.
   * @param array $data the settings data
   * @param string $file the file path.
   * @see loadFromFile
   */
  protected function saveToFile($data,$file)
  {
    file_put_contents($file,"<?php\nreturn ".var_export($data,true).";\n");
  }
}

