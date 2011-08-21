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
  				'roles'=>array('dbmanager', 'admin', 'xxx'),
  				),
  			array('deny', 
  				'users'=>array('*'),
  				),
  			);
  }
