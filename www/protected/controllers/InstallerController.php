<?php
/**
 * Class InstallerController. Holds the actions needed by the installer. 
 * 
 * @package MG
 */
class InstallerController extends Controller
{
  public $layout='//layouts/installer';
  
  public function filters() {
    return array( 
      'AlreadyInstalled -error todo',
    );
  }
  
  /**
   * The filter method for 'AlreadyInstalled' filter.
   * Checks whether the system is already installed  
   * 
   * @param CFilterChain $filterChain the filter chain that the filter is on.
   * 
   */
  public function filterAlreadyInstalled($filterChain)
  {
    if (!Yii::app()->fbvStorage->get("installed", false)) {
      $filterChain->run(); 
    } else {
      throw new CHttpException(403, Yii::t('app', 'Metadata Games is already installed.'));   
    }
  }
  
  /**
   * This is the default index it displays a short introduction into the installer
   */
  public function actionIndex()
  {
    $this->render('index');
  }
  
  /**
   * Implements the requirements action. Here all system requirement will be checked. 
   */
  public function actionRequirements() {
    $requirements=array(
      array(
        Yii::t('yii','PHP version'),
        true,
        version_compare(PHP_VERSION,"5.1.0",">="),
        '<a href="http://www.yiiframework.com">Yii Framework</a>',
        Yii::t('yii','PHP 5.1.0 or higher is required.')),
      array(
        Yii::t('yii','$_SERVER variable'),
        true,
        ($message=MGRequirementsHelper::checkServerVar(realpath(__FILE__))) === '',
        '<a href="http://www.yiiframework.com">Yii Framework</a>',
        $message),
      array(
        Yii::t('yii','Reflection extension'),
        true,
        class_exists('Reflection',false),
        '<a href="http://www.yiiframework.com">Yii Framework</a>',
        ''),
      array(
        Yii::t('yii','PCRE extension'),
        true,
        extension_loaded("pcre"),
        '<a href="http://www.yiiframework.com">Yii Framework</a>',
        ''),
      array(
        Yii::t('yii','SPL extension'),
        true,
        extension_loaded("SPL"),
        '<a href="http://www.yiiframework.com">Yii Framework</a>',
        ''),
      array(
        Yii::t('yii','DOM extension'),
        false,
        class_exists("DOMDocument",false),
        '<a href="http://www.yiiframework.com/doc/api/CHtmlPurifier">CHtmlPurifier</a>, <a href="http://www.yiiframework.com/doc/api/CWsdlGenerator">CWsdlGenerator</a>',
        ''),
      array(
        Yii::t('yii','PDO extension'),
        false,
        extension_loaded('pdo'),
        Yii::t('yii','All <a href="http://www.yiiframework.com/doc/api/#system.db">DB-related classes</a>'),
        ''),
      array(
        Yii::t('yii','PDO MySQL extension'),
        false,
        extension_loaded('pdo_mysql'),
        Yii::t('yii','All <a href="http://www.yiiframework.com/doc/api/#system.db">DB-related classes</a>'),
        Yii::t('yii','This is required if you are using MySQL database.')),
      array(
        Yii::t('yii','ZIP extension'),
        false,
        extension_loaded("zip"),
        Yii::t('app','Recommended to improve the performance and relyablility of imports and exports'),
        extension_loaded("zip") ? '' : Yii::t('app', 'We recommend to install/activate the php zip extension to improve the performance of the imports and exports.')),
      array(
        Yii::t('yii','Memcache extension'),
        false,
        extension_loaded("memcache") || extension_loaded("memcached"),
        '<a href="http://www.yiiframework.com/doc/api/CMemCache">CMemCache</a>',
        extension_loaded("memcached") ? Yii::t('yii', 'To use memcached set <a href="http://www.yiiframework.com/doc/api/CMemCache#useMemcached-detail">CMemCache::useMemcached</a> to <code>true</code>.') : ''),
      array(
        Yii::t('yii','APC extension'),
        false,
        extension_loaded("apc"),
        '<a href="http://www.yiiframework.com/doc/api/CApcCache">CApcCache</a>',
        ''),
      array(
        Yii::t('yii','Mcrypt extension'),
        false,
        extension_loaded("mcrypt"),
        '<a href="http://www.yiiframework.com/doc/api/CSecurityManager">CSecurityManager</a>',
        Yii::t('yii','This is required by encrypt and decrypt methods.')),
      array(
        Yii::t('yii','SOAP extension'),
        false,
        extension_loaded("soap"),
        '<a href="http://www.yiiframework.com/doc/api/CWebService">CWebService</a>, <a href="http://www.yiiframework.com/doc/api/CWebServiceAction">CWebServiceAction</a>',
        ''),
      array(
        Yii::t('yii','GD extension in Version 2'),
        MGRequirementsHelper::checkGDVersion(),
        !MGRequirementsHelper::checkGDVersion(),
        'MG Image Resizing',
        ''),
      array(
        Yii::t('yii','GD extension with<br />FreeType support'),
        false,
        ($message=MGRequirementsHelper::checkGD()) === '',
        '<a href="http://www.yiiframework.com/doc/api/CCaptchaAction">CCaptchaAction</a>',
        $message),
      array(
        Yii::t('yii','ImageMagick'),
        false,
        MGRequirementsHelper::checkImageMagick(),
        'Metadata Games recommend to use Image Magick for image resizing.',
        ''),
      array(
        Yii::t('yii','Ctype extension'),
        false,
        extension_loaded("ctype"),
        '<a href="http://www.yiiframework.com/doc/api/CDateFormatter">CDateFormatter</a>, <a href="http://www.yiiframework.com/doc/api/CDateFormatter">CDateTimeParser</a>, <a href="http://www.yiiframework.com/doc/api/CTextHighlighter">CTextHighlighter</a>, <a href="http://www.yiiframework.com/doc/api/CHtmlPurifier">CHtmlPurifier</a>',
        ''
      ),
      array(
        Yii::t('yii','Folder/File Write Permissions'),
        true,
        MGRequirementsHelper::checkFolderPermissions(false),
        MGRequirementsHelper::checkFolderPermissions(true),
        ''
      )
    );
    
    $result=1;  // 1: all pass, 0: fail, -1: pass with warnings

    foreach($requirements as $i=>$requirement)
    {
      if($requirement[1] && !$requirement[2])
        $result=0;
      else if($result > 0 && !$requirement[1] && !$requirement[2])
        $result=-1;
      if($requirement[4] === '')
        $requirements[$i][4]='&nbsp;';
    }
    
    $this->render('requirements', array(
      'requirements' => $requirements,
      'result' => $result,
    ));
  } 
  
  /**
   * Queries the needed database settings, checks them, and if they are valid configures
   * the main.php config file.
   */
  public function actionDatabase() {
    $error = "";
    $model=new DatabaseForm;
    if(isset($_POST['DatabaseForm'])) {
      $model->attributes=$_POST['DatabaseForm'];
      if($model->validate()) {
        
        $host = $model->host;
        if (isset($model->port) && (int)$model->port > 0) {
          $host .= ':' . $model->port;
        }
        
        $link = @mysql_connect($host, $model->user, $model->password);
        if (!$link) {
          $error = mysql_error();
        } else{
          $dbcheck = @mysql_select_db($model->database);
          if (!$dbcheck) {
            $error = mysql_error();
          } else {
            
            $arr_find = array('%%host%%','%%database%%','%%user%%','%%password%%','%%tablePrefix%%');
            $arr_replace = array($host, $model->database, $model->user, $model->password, $model->tablePrefix);
            
            $main = file_get_contents(Yii::getPathOfAlias('application.config') . DIRECTORY_SEPARATOR . 'main.install.php');
            
            if ($main) {
              file_put_contents(Yii::getPathOfAlias('application.config') . DIRECTORY_SEPARATOR . 'main.php', str_replace($arr_find, $arr_replace, $main));
              
              $connection = new CDbConnection('mysql:host=' . $host . ';dbname=' . $model->database, $model->user, $model->password);
              $connection->active = true;
              $connection->charset = 'utf8';
              $connection->emulatePrepare = true;
              $connection->tablePrefix = (trim((string)$model->tablePrefix) != "")? $model->tablePrefix : null;
              Yii::app()->setComponent('db', $connection);
              
              try {
                ob_start(); // yii migrate write a lot of feedback that we only want to show in error case
                $this->runMigrationTool();
                ob_end_clean();
                $this->redirect(Yii::app()->baseUrl . '/index.php/installer/Configuration');
              } catch (Exception $e) {
                $log = ob_get_clean();
                throw new CHttpException(500, Yii::t('app', "Error! Can't create needed database structure: \n\n $log ")); 
              }
              
            } else {
              throw new CHttpException(500, Yii::t('app', 'Install error! Can\'t modify config files.')); 
            }
           
          }
        }
      }
    }
    $this->render('database',array(
      'model'=>$model,
      'error' => $error,
    ));
  }
  
  /**
   * Configure first user
   */
  public function actionConfiguration() {
    $model = new InstallConfigurationForm;
    $profile=new Profile;
    $profile->regMode = true;
            
    if(isset($_POST['InstallConfigurationForm'])) {
      $model->attributes=$_POST['InstallConfigurationForm'];
      if($model->validate()) {
        $soucePassword = $model->password;
        $model->activekey=UserModule::encrypting(microtime().$model->password);
        $model->password=UserModule::encrypting($model->password);
        $model->verifyPassword=UserModule::encrypting($model->verifyPassword);
        $model->created = date('Y-m-d H:i:s');
        $model->modified = date('Y-m-d H:i:s');
        $model->lastvisit = NULL;
        $model->role ='admin';
        $model->status = User::STATUS_ACTIVE;
        
        if ($model->save()) {
          $profile->user_id=$model->id;
          if ($profile->save()) {
            $model->fbvSave();
            
            // here you can finish off further things you need to do during installation
            CFileHelper::copyDirectory(Yii::getPathOfAlias('application.data.installer.badges'),Yii::getPathOfAlias('webroot.uploads.badges'),array(
              'level'=> -1,
              'newDirMode'=>0777,
              'newFileMode'=>0777,
            ));
            
            $this->redirect(Yii::app()->baseUrl . '/index.php/installer/todo');
          }
        }
      }
    }
    $this->render('configuration',array('model'=>$model));
  }
  
  /**
   * This action renders the todo screen
   */
  public function actionTodo() {
    $this->render('todo');
  }
  
  /**
   * This is the action to handle external exceptions.
   */
  public function actionError()
  {
    MGHelper::setFrontendTheme();
    
    if($error=Yii::app()->errorHandler->error) {
      if(Yii::app()->request->isAjaxRequest) {
         echo $error['message'];
      }else {
         $this->render('error', $error);
      }
    }
  }
  
  /**
   * Executes the Yii migration tool via the browser
   */
  private function runMigrationTool() {
    $commandPath = Yii::app()->getBasePath() . DIRECTORY_SEPARATOR . 'commands';
    $runner = new CConsoleCommandRunner();
    $runner->addCommands($commandPath);
    $commandPath = Yii::getFrameworkPath() . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . 'commands';
    $runner->addCommands($commandPath);
    $args = array('yiic', 'migrate', '--interactive=0');
    ob_start();
    $runner->run($args);
    echo htmlentities(ob_get_clean(), null, Yii::app()->charset);
  }
  
}