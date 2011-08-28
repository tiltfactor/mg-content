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
    
    $model = new ZenTagForm;  
    $model->load();
    
    $game = GamesModule::loadGame($model->getGameID());
    
    if ($game) {
      $cs = Yii::app()->clientScript;
      $cs->registerCoreScript('jquery');
      $cs->registerCssFile(Yii::app()->baseUrl . '/css/colorbox.css');
      $cs->registerCssFile(GamesModule::getAssetsUrl() . '/zentag/css/style.css');
      $cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.colorbox-min.js', CClientScript::POS_END);
      $cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.tmpl.min.js', CClientScript::POS_END);
      $cs->registerScriptFile(Yii::app()->baseUrl . '/js/mg.api.js', CClientScript::POS_END);
      $cs->registerScriptFile(Yii::app()->baseUrl . '/js/mg.game.api.js', CClientScript::POS_END);
      $cs->registerScriptFile(Yii::app()->baseUrl . '/js/mg.game.zentag.js', CClientScript::POS_END);
      $cs->registerScriptFile(GamesModule::getAssetsUrl() . '/zentag/js/mg.game.zentag.js', CClientScript::POS_END);
      
      $throttleInterval = (int)Yii::app()->fbvStorage->get("throttleInterval", 5);
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
      
      if ($model->play_once_and_move_on == 1) {
        $this->layout = '//layouts/main_no_menu';
      } else {
        $this->layout = '//layouts/column1';
      }
      $this->render('index', array(
        'model' => $model,
        'game' => $game,
      ));  
    } else {
      throw new CHttpException(403, Yii::t('app', 'The game is not active.'));
    }
  }
  
  public function actionView() {
    $model = new ZenTagForm;  
    $model->load();
    
    $game = $this->loadModel(array("unique_id" => $model->getGameID()), 'Game');
    if ($game) {
      $model->active = $game->active;
    }
    
    $this->render('view', array(
      'model' => $model,
      'game' => $game,
    ));
  }
  
  public function actionUpdate() {
    $model = new ZenTagForm;
    $model->load();
    $game = $this->loadModel(array("unique_id" => $model->getGameID()), 'Game');
    
    $this->performAjaxValidation($model, 'zentag-form');
    
    if (isset($_POST['ZenTagForm'])) {
      
      $model->setAttributes($_POST['ZenTagForm']);
      
      if ($model->validate()) {
        $game->active = $model->active;
        $game->save();
        $model->save();
        
        Flash::add('success', $model->name . ' ' . Yii::t('app', "Updated"));
        $this->redirect(array('view'));
      }
    }
    
    if ($game) {
      $model->active = $game->active;
    }
    
    $this->render('update', array(
      'model' => $model,
      ));
  }
}