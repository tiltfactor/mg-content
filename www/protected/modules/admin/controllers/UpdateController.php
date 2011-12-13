<?php

class UpdateController extends GxController {
  /**
   * Full path of the main uploading folder.
   * @var string
   */
  public $path;
  
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
  				'actions'=>array('index','update'),
  				'roles'=>array('admin'),
  				),
  			array('deny', 
  				'users'=>array('*'),
  				),
  			);
  }

	public function actionIndex() {
		$this->render('index', array());
	}

	public function actionUpdate() {
    $commandPath = Yii::app()->getBasePath() . DIRECTORY_SEPARATOR . 'commands';
    $runner = new CConsoleCommandRunner();
    $runner->addCommands($commandPath);
    $commandPath = Yii::getFrameworkPath() . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . 'commands';
    $runner->addCommands($commandPath);
    $args = array('yiic', 'migrate', '--interactive=0');
    ob_start();
    $runner->run($args);
    
    $status = htmlentities(ob_get_clean(), null, Yii::app()->charset);  
      
    $this->render('update', array(
      'status' => $status
    ));
	}
}