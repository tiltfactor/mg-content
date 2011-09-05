<?php

class DefaultController extends Controller
{
	public $defaultAction='admin';
    
	public function filters() {
    return array(
        'accessControl', 
        );
  }
  
  public function accessRules() {
    return array(
        array('allow', 
          'actions'=>array('admin'),
          'roles'=>array('dbmanager', 'admin'),
          ),
        array('deny', 
          'users'=>array('*'),
          ),
        );
  }  

  public function actionAdmin() {
    $this->layout = '//layouts/column1';
    
    $games = array();
    $path = Yii::getPathOfAlias('application.modules.games.components') . DIRECTORY_SEPARATOR;
    if (is_dir($path)) {
      
      
      foreach (glob($path . "*") as $game) {
        $game_name = basename($game);
        if ($game_name != "MGGame.php" && strpos($game_name, "Game.php") !== false) {
          $games[] = str_replace("Game.php", "", $game_name);
        }
      }  
      if (count($games) > 0) {
        $this->refreshGames($games);
      }
    }
    
    $model = new Game('search');
    $model->unsetAttributes();

    if (isset($_GET['Game']))
      $model->setAttributes($_GET['Game']);

    $this->render('admin', array(
      'model' => $model
    ));
  }
  
  /**
   * Scans the folder for available plug-ins.
   * If a new plug-in has been added it will add it to the database. 
   * If a plug-in has been removed the system will display an error 
   */
  protected function refreshGames($games) {
    $registered_games = Game::model()->findAll();
    if (count($registered_games) > 0) {
      foreach ($games as $game) {
        $found = false;
        foreach ($registered_games as $game_registered) {  
          if ($game == $game_registered->unique_id) {
            $found = true;
            break;
          }
        }
        if (!$found) {
          $this->addGame($game);
          Flash::add("success", Yii::t('app', "New game {$game} registered."));
        }
      }
    } else {
      foreach ($games as $game) {
        $this->addGame($game);
        Flash::add("success", Yii::t('app', "New game {$game} registered."));
      }
    }
    
    foreach ($registered_games as $game_registered) {
      $found = false;
        foreach ($games as $game) {
          if ($game == $game_registered->unique_id) {
            $found = true; 
            break;
          }
        }
      
      if (!$found) {
        $game_registered->active = 0;
        $game_registered->save();
        Flash::add("error", Yii::t('app', "The game {$game_registered->unique_id} is registered in the database but its code is not accessible in the file system. It has been automatically disabled!"), TRUE);
      }
    }
  }

  protected function addGame($game) {
    $model = new Game;
    $model->created = date('Y-m-d H:i:s');
    $model->modified = date('Y-m-d H:i:s');
    $model->unique_id = $game;
    $model->active = 0;
    return $model->save();  
  }
}