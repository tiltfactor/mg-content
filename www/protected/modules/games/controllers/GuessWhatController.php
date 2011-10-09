<?php

class GuessWhatController extends GxController
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
    
    $game = GamesModule::loadGame("GuessWhat");
    if ($game) {
      $cs = Yii::app()->clientScript;
      $cs->registerCoreScript('jquery');
      $cs->registerCssFile(Yii::app()->baseUrl . '/css/jquery.fancybox-1.3.4.css');
      $cs->registerCssFile(GamesModule::getAssetsUrl() . '/guesswhat/css/style.css');
      $cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.fancybox-1.3.4.pack.js', CClientScript::POS_END);
      $cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.tmpl.min.js', CClientScript::POS_END);
      $cs->registerScriptFile(Yii::app()->baseUrl . '/js/mg.api.js', CClientScript::POS_END);
      $cs->registerScriptFile(Yii::app()->baseUrl . '/js/mg.game.api.js', CClientScript::POS_END);
      $cs->registerScriptFile(GamesModule::getAssetsUrl() . '/guesswhat/js/mg.game.guesswhat.js', CClientScript::POS_END);
      
      $throttleInterval = (int)Yii::app()->fbvStorage->get("settings.throttle_interval", 1500); 
      $message_queue_interval = (int)Yii::app()->fbvStorage->get("settings.message_queue_interval", 500); 
      $js = <<<EOD
    MG_GAME_GUESSWHAT.init({
        gid : 'GuessWhat',
        app_id : 'MG_API',
        game_base_url : '{$game->game_base_url}',
        api_url : '{$game->api_base_url}',
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
    $model = $this->loadModel(array("unique_id" => "GuessWhat"), 'GuessWhat');  
    $model->fbvLoad();
    
    $this->render('view', array(
      'model' => $model,
    ));
  }
  
  public function actionUpdate() {
    $model = $this->loadModel(array("unique_id" => "GuessWhat"), 'GuessWhat');
    $model->fbvLoad();
    
    $this->performAjaxValidation($model, 'guesswhat-form');
    if (isset($_POST['GuessWhat'])) {
      $model->setAttributes($_POST['GuessWhat']);
      
      $relatedData = array(
        'imageSets' => $_POST['GuessWhat']['imageSets'] === '' ? null : $_POST['GuessWhat']['imageSets'],
        'plugins' => $_POST['GuessWhat']['plugins'] === '' ? null : $_POST['GuessWhat']['plugins'],
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