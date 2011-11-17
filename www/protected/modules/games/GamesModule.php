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
    $criteria->select='id, unique_id';  // only select the 'title' column
    $criteria->condition='active=1';
    $models = Game::model()->findAll($criteria);  
    
    $games = array();
    foreach ($models as $model) {
      $games[] = self::loadGame($model->unique_id, false, $model->id);
    }
    return $games;
  }
  
  /**
   * This method loads a game with the given unique id
   * 
   * @param string $unique_id The unique id of the game
   * @param boolean $active If true it will check whether the game is active
   * @param int $game_id the id of the game in the database
   * @return object The game as object or null if the game could not be found
   */
  public static function loadGame($unique_id, $active=true, $game_id=null) {
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
      $game->arcade_url = Yii::app()->getRequest()->getHostInfo() . Yii::app()->createUrl('/');
      $game->base_url = Yii::app()->getRequest()->getHostInfo();
      $game->game_base_url = Yii::app()->getRequest()->getHostInfo() . Yii::app()->createUrl('/games');
      $game->user_name = Yii::app()->user->name;
      $game->user_num_played = 0;
      $game->user_score =  0;  
      $game->played_against_computer = false;
      
      if (!Yii::app()->user->isGuest && (isset($game->game_id) || $game_id)) {
        $game_info = GamesModule::loadUserToGameInfo(Yii::app()->user->id, ($game_id)? $game_id : $game->game_id);
        if ($game_info) {
          $game->user_score = $game_info->score; 
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
    
    $where = array('and', 'user_id=:userID', 'g.active=1');
    $params = array(":userID" => $user_id);
    
    if ($game_id) {
      $where[] = 'game_id=:gameID';
      $params[":gameID"] = $game_id;
    }
    
    $command = Yii::app()->db->createCommand()
                    ->select('ug.game_id, g.unique_id, ug.score, ug.number_played')
                    ->from('{{user_to_game}} ug')
                    ->rightJoin('{{game}} g', 'g.id=ug.game_id')
                    ->where($where, $params) 
                    ->order('score DESC, number_played DESC');
                    
    $userToGames = $command->queryAll();
    
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
    
    $where = 'unique_id=:unique_id';
    
    if ($active)
      $where .= ' AND active=1';
    
    $criteria->condition = $where;
    $criteria->params = array(':unique_id'=>$unique_id);
    
    return Game::model()->find($criteria); 
  }
  
  /**
   * Returns the highest scoring user on the platform
   * 
   * @param int $limit The number of players to return in the list
   * @param boolean $return_as_object If true all rows will be converted to objects
   * @return mixed Null if no player found or array of arrays or objects
   */
  public static function getTopPlayers($limit=10, $return_as_object=true) {
    static $players;
    
    if ($players || $players == -1) {
      return ($players == -1)? null : $players;
    } else {
    
      $players = Yii::app()->db->createCommand()
                    ->select('u.id, u.username, SUM(ug.score) as score, SUM(ug.number_played) as number_played')
                    ->from('{{user_to_game}} ug')
                    ->join('{{user}} u', 'u.id=ug.user_id')
                    ->join('{{game}} g', 'g.id=ug.game_id')
                    ->where('g.active=1')
                    ->group('username') 
                    ->order('score DESC, number_played DESC')
                    ->limit((int)$limit)
                    ->queryAll();
          
      if ($players) {
        if ($return_as_object) {
          //remap results to objects 
          foreach ($players as $key => $row) {
            $players[$key] = (object)$row;  
          }
        }
        return $players;
      } else {
        $players = -1;
        return null; 
      } 
    }
  }
  
  /**
   * Returns the scores for all games for a particular player 
   * 
   * @param int $user_id The user_id in the database of the player whoms scores should be returned
   * @return mixed Null if no player scores found or array of objects
   */
  public static function getPlayerScores($user_id, $active=true) {
    static $user_games; // we only want to load the players scores once per request
    
    if (!is_array($user_games)) {
      $user_games = array();
    }
    
    if (isset($user_games[$user_id])) {
      return $user_games[$user_id];
    } else {
         
      $games = Yii::app()->db->createCommand()
                    ->select('g.id, g.unique_id, ug.score, ug.number_played')
                    ->from('{{game}} g')
                    ->leftJoin('{{user_to_game}} ug', 'ug.game_id=g.id AND ug.user_id=:userID', array(':userID' => $user_id))
                    ->where( ($active)? 'g.active=1' : null)
                    ->order('score DESC, number_played DESC')
                    ->queryAll();
      
      if ($games) {
        //remap results to objects 
        foreach ($games as $key => $row) {
          $games[$key] = (object)$row;
          $game = GamesModule::loadGame($row['unique_id'], false);
          $games[$key]->name = $game->name;
          $games[$key]->score = (int)$games[$key]->score;
          $games[$key]->number_played = (int)$games[$key]->number_played;
        }
        $user_games[$user_id] = $games;
      } else {
        $user_games[$user_id] = null;
      } 
      return $user_games[$user_id];
    }
  }
  
  /**
   * Returns all available badges in the system
   * 
   * @return mixed Null if no badges available array of objects {id, title, points}
   */
  public static function getBadges() {
    static $badges; // we only want to load the badges once per request
    
    if ($badges || $badges == -1) {
      return ($badges == -1)? null : $badges;
    } else {
      
      $badges = Yii::app()->db->createCommand()
                    ->select('b.id, b.title, b.points')
                    ->from('{{badge}} b')
                    ->order('b.points')
                    ->queryAll();
          
      if ($badges) {
        foreach ($badges as $key => $badge) {
          $badges[$key] = (object)$badge;  
        }
        return $badges;
      } else {
        $badges = -1;
        return null; 
      } 
    }
  }
  
  /**
   * Returns all available badges and a numberic value how many times they have been awarded in the system
   * 
   * @return mixed Null if no badges available array of objects {id, title, points, counted}
   */
  public static function getAwardedBadges() {
    static $badges; // we only want to load the badges once per request
    
    if ($badges || $badges == -1) {
      return ($badges == -1)? null : $badges;
    } else {
      
      $badges = Yii::app()->db->createCommand()
                    ->select('b.id, b.title, b.points')
                    ->from('{{badge}} b')
                    ->order('b.points')
                    ->queryAll();
          
      if ($badges) {
        foreach ($badges as $key => $badge) {
          $counted = Yii::app()->db->createCommand("  SELECT COUNT(*) FROM (SELECT DISTINCT ug.user_id
                                                      FROM user_to_game ug 
                                                      GROUP BY ug.user_id
                                                      having Sum(ug.score) > :points) as temp")->bindParam(":points", $badge['points'])->queryScalar();
          $badges[$key] = (object)$badge;
          $badges[$key]->counted = $counted;
        }
        return $badges;
      } else {
        $badges = -1;
        return null; 
      } 
    }
  }
  
  /**
   * Returns usage statistics for one or more games.
   * If a game_id is given an object with the available information will be retrieved otherwise
   * an array of objects will be returned. 
   *  
   * returned object {
   *  id, //the game id in the database
   *  unique_id, // the unique game id
   *  cnt_users, // number unique users/players playing the game
   *  sum_played, // number of times a game has been played
   *  sum_scored, // total score of all games.
   *  cnt_played_games, // number of times a game has been played (first turn shown)
   *  cnt_played_games_finished, // number of times a played game has been finished by guests/players
   *  cnt_played_games_by_users, // number of games finished by registered players
   *  cnt_played_games_by_guests, // number of games finished by guests
   * }
   * 
   * @param int $game_id optional id of the game for which the data shall be retrieved
   * @return mixed null/object/array or objects
   */
  public static function getStatistics($game_id = null) {
    $cmd = Yii::app()->db->createCommand()
                    ->select('g.id, g.unique_id, COUNT(DISTINCT ug.user_id) cnt_users, SUM(ug.number_played) as sum_played, SUM(ug.score) as sum_scored')
                    ->from('{{user_to_game}} ug')
                    ->join('{{game}} g', 'g.id = ug.game_id')
                    ->group('ug.game_id')
                    ->order('g.unique_id');
    if ($game_id) {
      $cmd->where('ug.game_id = :gameID', array(':gameID' => $game_id));
    }
    
    $games = $cmd->queryAll();
    
    if ($games) {
      $games_processed = array();
      foreach($games as $game) {
        
        $game['cnt_played_games'] = Yii::app()->db->createCommand()
                ->select('COUNT(id)')
                ->from('{{played_game}} pg')
                ->where('game_id=:gameID', array(':gameID' => $game["id"]))
                ->queryScalar();
        
        $game['cnt_played_games'] = (int)$game['cnt_played_games'];
        
        $game['cnt_played_games_finished'] = Yii::app()->db->createCommand()
                ->select('COUNT(id)')
                ->from('{{played_game}} pg')
                ->where('game_id=:gameID AND finished is not null', array(':gameID' => $game["id"]))
                ->queryScalar();
        $game['cnt_played_games_finished'] = (int)$game['cnt_played_games_finished'];
        
        $game['cnt_played_games_by_users'] = $game['sum_played'];
        $game['cnt_played_games_by_guests'] = $game['cnt_played_games_finished'] - $game['sum_played'];
        
        if ($game_id && $game_id == $game['id']) {
          return (object)$game;
        } else {
          $games_processed[] = (object)$game;
        }
      }
      return $games_processed;
    }
    return null;      
  } 
}
