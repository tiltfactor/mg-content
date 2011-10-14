<?php

Yii::import("games.controllers.ZenTagController");
class ZenTagPlayOnceMoveOnController extends ZenTagController 
{
  
  public function filters() {
    return array(
      'IPBlock',
      'accessControl - index', 
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
    
    $game = GamesModule::loadGame("ZenTagPlayOnceMoveOn");
    if ($game) {
      $cs = Yii::app()->clientScript;
      $cs->registerCoreScript('jquery');
      $cs->registerCssFile(Yii::app()->baseUrl . '/css/jquery.fancybox-1.3.4.css');
      $cs->registerCssFile(GamesModule::getAssetsUrl() . '/zentag/css/style.css');
      $cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.fancybox-1.3.4.pack.js', CClientScript::POS_END);
      $cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.tmpl.min.js', CClientScript::POS_END);
      $cs->registerScriptFile(Yii::app()->baseUrl . '/js/mg.api.js', CClientScript::POS_END);
      $cs->registerScriptFile(Yii::app()->baseUrl . '/js/mg.game.api.js', CClientScript::POS_END);
      $cs->registerScriptFile(GamesModule::getAssetsUrl() . '/zentag/js/mg.game.zentag.js', CClientScript::POS_END);
      $throttleInterval = (int)Yii::app()->fbvStorage->get("settings.throttle_interval", 1500);
      $asset_url = Yii::app()->baseUrl;
       
      $js = <<<EOD
    MG_GAME_ZENTAG.init({
        gid : 'ZenTagPlayOnceMoveOn',
        app_id : 'MG_API',
        asset_url : '$asset_url',
        api_url : '{$game->api_base_url}',
        msg_url : '{$game->base_url}/mg_api_messages.php',
        play_once_and_move_on : {$game->play_once_and_move_on},
        play_once_and_move_on_url : '{$game->play_once_and_move_on_url}',
        throttleInterval : $throttleInterval
      });
EOD;
      Yii::app()->clientScript->registerScript(__CLASS__.'#game', $js, CClientScript::POS_READY);
      
      if ($game->play_once_and_move_on == 1) {
        $this->layout = '//layouts/main_no_menu';
      } else {
        $this->layout = '//layouts/column1';
      }
      $this->render('/zenTag/index', array(
        'game' => $game,
      ));  
    } else {
      throw new CHttpException(403, Yii::t('app', 'The game is not active.'));
    }
  }
  
  public function actionView() {
    $model = $this->loadModel(array("unique_id" => "ZenTagPlayOnceMoveOn"), 'ZenTagPlayOnceMoveOn');  
    $model->fbvLoad();
    
    $this->render('/zenTag/view', array(
      'model' => $model,
    ));
  }
  
  public function actionUpdate() {
    $model = $this->loadModel(array("unique_id" => "ZenTagPlayOnceMoveOn"), 'ZenTagPlayOnceMoveOn');
    $model->fbvLoad();
    
    $this->performAjaxValidation($model, 'zentag-form');
    if (isset($_POST['ZenTagPlayOnceMoveOn'])) {
      $model->setAttributes($_POST['ZenTagPlayOnceMoveOn']);
      
      $relatedData = array(
        'imageSets' => $_POST['ZenTagPlayOnceMoveOn']['imageSets'] === '' ? null : $_POST['ZenTagPlayOnceMoveOn']['imageSets'],
        'plugins' => $_POST['ZenTagPlayOnceMoveOn']['plugins'] === '' ? null : $_POST['ZenTagPlayOnceMoveOn']['plugins'],
        );
      
      if ($model->saveWithRelated($relatedData)) {
        $model->fbvSave();
        MGHelper::log('update', 'Game ' . $model->name . ' updated');
        Flash::add('success', $model->name . ' ' . Yii::t('app', "Updated"));
        $this->redirect(array('view'));
      }
    }
    
    $this->render('/zenTag/update', array(
      'model' => $model,
      ));
  }
}