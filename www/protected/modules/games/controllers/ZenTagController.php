<?php

class ZenTagController extends GxController
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
    
    $game = GamesModule::loadGame("ZenTag");
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
      $js = <<<EOD
    MG_GAME_ZENTAG.init({
        gid : 'ZenTag',
        app_id : 'MG_API',
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
      $this->render('index', array(
        'game' => $game,
      ));  
    } else {
      throw new CHttpException(403, Yii::t('app', 'The game is not active.'));
    }
  }
  
  public function actionView() {
    $model = $this->loadModel(array("unique_id" => "ZenTag"), 'ZenTag');  
    $model->fbvLoad();
    
    $this->render('view', array(
      'model' => $model,
    ));
  }
  
  public function actionUpdate() {
    $model = $this->loadModel(array("unique_id" => "ZenTag"), 'ZenTag');
    $model->fbvLoad();
    
    $this->performAjaxValidation($model, 'zentag-form');
    if (isset($_POST['ZenTag'])) {
      $model->setAttributes($_POST['ZenTag']);
      
      $relatedData = array(
        'imageSets' => $_POST['ZenTag']['imageSets'] === '' ? null : $_POST['ZenTag']['imageSets'],
        'plugins' => $_POST['ZenTag']['plugins'] === '' ? null : $_POST['ZenTag']['plugins'],
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