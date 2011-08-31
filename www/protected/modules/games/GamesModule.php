<?php

class GamesModule extends CWebModule
{
	private static $_assetsUrl;   
    
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'games.models.*',
			'games.components.*',
		));
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
  
  public static function getAssetsUrl() {
    if (self::$_assetsUrl === null) {
      self::$_assetsUrl = Yii::app()->getAssetManager()->publish(
            Yii::getPathOfAlias('application.modules.games.assets'),false,-1,YII_DEBUG);
    }
    return self::$_assetsUrl;
  }
  
  public static function listActiveGames() {
    $criteria=new CDbCriteria;
    $criteria->select='unique_id';  // only select the 'title' column
    $criteria->condition='active=1';
    $models = Game::model()->findAll($criteria);  
    
    $games = array();
    foreach ($models as $model) {
      $games[] = self::loadGame($model->unique_id);
    }
    return $games;
  }
  
  /**
   * This method loads a game with the given unique id
   * 
   * @param string $unique_id The unique id of the game
   * @param boolean $active If true it will check whether the game is active
   * @return object The game as object or null if the game could not be found
   */
  public static function loadGame($unique_id, $active=true) {
    $game = null;
      
    $registered_game = null;
    
    if ($active) {
      $registered_game = GamesModule::loadGameFromDB($unique_id); 
    }
    
    if ($registered_game || !$active) {
      $game = (object)Yii::app()->fbvStorage->get("games." . $unique_id, array(
          'name' => '',
          'description' => '',
        ));
        
      $game->game_model = null;
      if ($registered_game) {
        $game->game_model = $registered_game;
        $game->game_id = $registered_game->id;  
      }
      $game->gid =  $unique_id;
      $game->url =  Yii::app()->createUrl('games/'.$unique_id);
      $game->image_url =  self::getAssetsUrl() . '/' . strtolower($unique_id) . '/images/' . (isset($game->arcade_image)? $game->arcade_image : '');
      $game->api_base_url = Yii::app()->getRequest()->getHostInfo() . Yii::app()->createUrl('/api');
      $game->base_url = Yii::app()->getRequest()->getHostInfo();
      
      $game->user_name = Yii::app()->user->name;
      $game->user_num_played = 0;
      $game->user_score =  0;  
        
      if (!Yii::app()->user->isGuest && isset($game->game_id)) {
        $game_info = GamesModule::loadUserToGameInfo(Yii::app()->user->id, $game->game_id);
        if ($game_info) {
          $game->user_score =  $game_info->score; 
          $game->user_num_played = $game_info->number_played;   
        }
      }
      $game->user_authenticated = !Yii::app()->user->isGuest;
    }
    return $game;
  }

  public static function getGameEngine($unique_id) {
    
    $game_engine = null;
    
    try {
      Yii::import("games.components.*");
      $game_engine = Yii::createComponent($unique_id. "Game");
    } catch (Exception $e) {
      throw new CHttpException(500, Yii::t('app', 'Internal Server Error.'));
    } 
    
    return $game_engine;
  }
  
  /**
   * Retrieve User to Game info stored in the database. It can be that the user has not finished all games
   * missing data will have to be filled in by hand
   * 
   * Ruturn Values
   * array (
   *  game_id => (object){
   *    game_id,
   *    score,
   *    number_played
   *  }
   * )
   * 
   * or array()
   * 
   * OR if game_id is set
   * 
   * (object){
   *    game_id,
   *    score,
   *    number_played
   *  } 
   * 
   * or null
   * 
   * if game_id is set 
   * 
   * @param int $user_id The user.id in the database
   * @param int $game_id The game.id in the database -> only information for that game will be returned.
   * @return mixed array of info for all game or just the object   
   */
  public static function loadUserToGameInfo($user_id, $game_id=null) {
    $data = array();
      
    $builder = Yii::app()->db->getCommandBuilder();
    
    $findCriteria = new CDbCriteria(array(
      'select' => 'game_id, score, number_played',
      'condition' => 'user_id=:userID' . (($game_id)? ' AND game_id=:gameID': ''),
      'params' => array(
          ':userID' => $user_id,
          ':gameID' => $game_id,
        )
    ));
    
    $userToGames = $builder->createFindCommand(
        UserToGame::model()->tableSchema,
        $findCriteria
        )->queryAll();
        
    //remap results
    foreach ($userToGames as $key => $info) {
      if ($game_id) {
        return (object)$info;
      } else {
        $data[$info["game_id"]] = (object)$info;  
      }
    }
    if ($game_id) {
      return null;
    } else {
      return $data;  
    }
  }
  
  public static function loadGameFromDB($unique_id, $active=true) {
    $criteria=new CDbCriteria;
    $criteria->params='unique_id=:unique_id';
    
    if ($active)
      $criteria->condition='active=1';
    
    return Game::model()->find($criteria, array(':unique_id'=>$unique_id)); 
  }
}
