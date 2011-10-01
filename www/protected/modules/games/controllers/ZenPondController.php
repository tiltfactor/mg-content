<?php

class ZenPondController extends GxController
{
  
	public function filters() {
    return array(
        'accessControl', 
        );
  }
  
  public function accessRules() {
    return array(
        array('allow', 
          'actions'=>array('index'),
          'users'=>array('*'),
          ),
        array('allow', 
          'actions'=>array('view', 'update'),
          'roles'=>array('dbmanager', 'admin'),
          ),
        array('deny', 
          'users'=>array('*'),
          ),
        );
  }  
  
  public function actionIndex() {
    MGHelper::setFrontendTheme();
    
    $game = GamesModule::loadGame("ZenPond");
    if ($game) {
      $cs = Yii::app()->clientScript;
      $cs->registerCoreScript('jquery');
      $cs->registerCssFile(Yii::app()->baseUrl . '/css/jquery.fancybox-1.3.4.css');
      $cs->registerCssFile(GamesModule::getAssetsUrl() . '/zenpond/css/style.css');
      $cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.fancybox-1.3.4.pack.js', CClientScript::POS_END);
      $cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.tmpl.min.js', CClientScript::POS_END);
      $cs->registerScriptFile(Yii::app()->baseUrl . '/js/mg.api.js', CClientScript::POS_END);
      $cs->registerScriptFile(Yii::app()->baseUrl . '/js/mg.game.api.js', CClientScript::POS_END);
      $cs->registerScriptFile(GamesModule::getAssetsUrl() . '/zenpond/js/mg.game.zenpond.js', CClientScript::POS_END);
      
      $throttleInterval = (int)Yii::app()->fbvStorage->get("settings.throttle_interval", 1500); 
      $message_queue_interval = (int)Yii::app()->fbvStorage->get("settings.message_queue_interval", 500); 
      $js = <<<EOD
    MG_GAME_ZENPOND.init({
        gid : 'ZenPond',
        app_id : 'MG_API',
        api_url : '{$game->api_base_url}',
        msg_url : '{$game->base_url}/mg_api_messages.php',
        throttleInterval : $throttleInterval,
        message_queue_interval : $message_queue_interval,
        partner_wait_threshold : {$game->partner_wait_threshold},
      });
EOD;
      Yii::app()->clientScript->registerScript(__CLASS__.'#game', $js, CClientScript::POS_READY);
      
      $this->layout = '//layouts/column1';
      
      $this->render('index', array(
        'game' => $game,
      ));  
    } else {
      throw new CHttpException(403, Yii::t('app', 'The game is not active.'));
    }
  }
  
  public function actionView() {
    $model = $this->loadModel(array("unique_id" => "ZenPond"), 'ZenPond');  
    $model->fbvLoad();
    
    $this->render('view', array(
      'model' => $model,
    ));
  }
  
  public function actionUpdate() {
    $model = $this->loadModel(array("unique_id" => "ZenPond"), 'ZenPond');
    $model->fbvLoad();
    
    $this->performAjaxValidation($model, 'zenpond-form');
    if (isset($_POST['ZenPond'])) {
      $model->setAttributes($_POST['ZenPond']);
      
      $relatedData = array(
        'imageSets' => $_POST['ZenPond']['imageSets'] === '' ? null : $_POST['ZenPond']['imageSets'],
        'plugins' => $_POST['ZenPond']['plugins'] === '' ? null : $_POST['ZenPond']['plugins'],
        );
      
      if ($model->saveWithRelated($relatedData)) {
        
        $model->fbvSave();
        MGHelper::log('update', 'Game ' . $model->name . ' updated');
        Flash::add('success', $model->name . ' ' . Yii::t('app', "Updated"));
        $this->redirect(array('view'));
      }
    }
    
    $this->render('update', array(
      'model' => $model,
      ));
  }
}