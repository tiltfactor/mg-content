<?php

class NexTagController extends GxController
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
  
  /**
   * As most of the game play is handled via JavaScript and API callbacks the controller
   * renders only the initial needed HTML while making sure all needed assets CSS 
   * and JavaScript are loaded 
   */
  public function actionIndex() {
    MGHelper::setFrontendTheme();
    
    $game = GamesModule::loadGame("NexTag");
    if ($game) {
      $cs = Yii::app()->clientScript;
      $cs->registerCoreScript('jquery');
      $cs->registerCssFile(Yii::app()->baseUrl . '/css/jquery.fancybox-1.3.4.css');
      $cs->registerCssFile(GamesModule::getAssetsUrl() . '/nextag/css/style.css');
      $cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.fancybox-1.3.4.pack.js', CClientScript::POS_END);
      $cs->registerScriptFile(Yii::app()->baseUrl . '/js/html5_api/html5media.min.js', CClientScript::POS_HEAD);
      $cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.tmpl.min.js', CClientScript::POS_END);
      $cs->registerScriptFile(Yii::app()->baseUrl . '/js/mg.api.js', CClientScript::POS_END);
      $cs->registerScriptFile(Yii::app()->baseUrl . '/js/mg.game.api.js', CClientScript::POS_END);
      $cs->registerScriptFile(GamesModule::getAssetsUrl() . '/nextag/js/mg.game.nextag.js', CClientScript::POS_END);
      $throttleInterval = (int)Yii::app()->fbvStorage->get("settings.throttle_interval", 1500);
      $asset_url = Yii::app()->baseUrl;
      $arcade_url = Yii::app()->getRequest()->getHostInfo() . Yii::app()->createUrl('/');
      
      $js = <<<EOD
    MG_GAME_NEXTAG.init({
        gid : 'NexTag',
        app_id : 'MG_API',
        asset_url : '$asset_url',
        api_url : '{$game->api_base_url}',
        arcade_url : '$arcade_url',
        game_base_url : '{$game->game_base_url}',
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
  
  /**
   * show the game's settings
   */
  public function actionView() {
    $model = $this->loadModel(array("unique_id" => "NexTag"), 'NexTag');  
    $model->fbvLoad();
    
    $this->render('view', array(
      'model' => $model,
      'statistics' => GamesModule::getStatistics($model->id)
    ));
  }
  
  /**
   * edit the game's settings
   */
  public function actionUpdate() {
    $model = $this->loadModel(array("unique_id" => "NexTag"), 'NexTag');
    $model->fbvLoad();
    
    $this->performAjaxValidation($model, 'nextag-form');
    if (isset($_POST['NexTag'])) {
      $model->setAttributes($_POST['NexTag']);

      $relatedData = array(
        'collections' => $_POST['NexTag']['collections'] === '' ? null : $_POST['NexTag']['collections'],
        'plugins' => $_POST['NexTag']['plugins'] === '' ? null : $_POST['NexTag']['plugins'],
        );
      
      // save the games data in the database
      if ($model->saveWithRelated($relatedData)) {
        $model->fbvSave(); // but also save it in the settings file as each game uses FBVstorage
        
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