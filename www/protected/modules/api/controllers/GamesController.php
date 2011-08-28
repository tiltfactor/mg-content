<?php

class GamesController extends ApiController {
  
  public function filters() {
    return array( // add blocked IP filter here
        'throttle',
        'IPBlock',
        'APIAjaxOnly', // custom filter defined in this class accepts only requests with the header HTTP_X_REQUESTED_WITH === 'XMLHttpRequest'
        'accessControl',
        'sharedSecret', // the API is protected by a shared secret this filter ensures that it is regarded 
    );
  }
  
  /**
   * Defines the access rules for this controller
   */  
  public function accessRules() {
    return array(
      array('allow',
        'actions'=>array('index', 'scores', 'play', 'partner'),
        'users'=>array('*'),
        ),
      array('deny', 
        'users'=>array('*'),
        ),
      );
  }
  
  /**
   * This action returns a list of all games available in the system xxx
   */
  public function actionIndex() {
    if(Yii::app()->getRequest()->getIsAjaxRequest()) {
      $data = array();
      $data['status'] = "ok";
      $data['games'] = GamesModule::listActiveGames();
      $this->sendResponse($data);  
    } else {
      parent::actionIndex();
    }
  }
  
  /**
   * Returns the top 10 list of scores of all users xxx
   * 
   */
  public function actionScores() {
    $data = array();
    $data['status'] = "ok";
    $data['scores'] = array(); // 
    $this->sendResponse($data);
  }
  
  /**
   * Via this method call the user can request a partner for a game
   */
  public function actionPartner($unique_id) {
    if (Yii::app()->getRequest()->getIsGetRequest()) {
      $data = array();
      $data['status'] = "ok";
      $data['partner'] = array();
      $this->sendResponse($data);  
    } else {
      throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    } 
  }
  
  /**
   * This 
   */
  public function actionPlay($gid) {
    $game = GamesModule::loadGame($gid);
    
    if($game) {
      $game_engine = GamesModule::getGameEngine($gid);
      if (is_null($game_engine)) {
        throw new CHttpException(500, Yii::t('app', 'Internal Server Error.'));
      }
      
      $game->played_game_id = null;
      if (Yii::app()->getRequest()->getIsPostRequest()) {
        if (isset($_POST["played_game_id"])) {
          $game->played_game_id = (int)$_POST["played_game_id"]; 
        }
        
        if ($game->played_game_id != 0 && $game_engine->validateSubmission($game)) {
          $this->_playPost($game, $game_engine);  
        } else {
          throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
        }
      } else {
        $this->_playGet($game, $game_engine);
      }
    } else {
      throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }
  }
  
  /**
   * Processes the GET request of the play method call xxx
   * 
   * GET Request expect only one parameter (however like all requests it expects shared secret and X_REQUESTED_WITH header) 
   * - gid = the unique id of the game
   * 
   * POST Requests needs to fullfill the get request and at least to pass on the following fields in the body
   * 
   * JSON representation of post request's body
   * {
   *  turn : 2 // the current turn's number
   *  played_game_id : 1 // the id in the database representing that played game
   *  submission : {} // JSON of this turns submission. The shape of the JSON request differs per game
   * }
   * 
   * Returned JSON
   * {
   *  status: 'ok|error',
   *  errors: {"field":["Error Message"]}, // if status == error
   *  game: {
   *    // the following fields are available in all games
   *    unique_id : '',
   *    played_game_id : '', // as a user can play more than one game per session we have to track a played game id
   *    name : '',
   *    description : '',
   *    more_info_url : '',
   *    base_url : '',
   *    'play_once_and_move_on' => '0|1',
   *    'turns' => '4',
   *    'user_name' => null or 'user name' // if the user is authenticated
   *    'user_score' => 0 or x // if the user is authenticated 
   *    'user_authentiated => false/true // true if user is authenticated 
   *    //a game could have more fields
   *  },
   *  turn : {
   *    score : 0, // numeric of the previous turn's score
   *
   *    // the following fields are available in all games
   *    images : [{
   *      // all urls are relative to game.base_url
   *      url_full_size : '',
   *      url_scaled : '',
   *      url_thumb : '',
   *      licence : 'name of licence that can be found in turn.licences',
   *    }, {...}],
   * 
   *    licences : [{
   *      // 
   *      name : '',
   *      description : '',
   *    }, {...}],   
   *    
   *    // the turn can have further elements created by plugins or similar. e.g
   *    wordsToAvoid : ["dog", "house", "car"],
   *  }
   * }
   *
   */
  private function _playGet($game, $game_engine) {
    $data = array();
    $data['status'] = "ok";
    $data['game'] = $game;
    
    $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
    if (!$game->played_game_id && isset(Yii::app()->session[$api_id .'_SHARED_SECRET'])) {

      $played_game = new PlayedGame;
      
      
      $played_game->session_id_1 = (int)Yii::app()->session[$api_id .'_SESSION_ID'];
      $played_game->game_id = $game->game_id;
      $played_game->created = date('Y-m-d H:i:s'); 
      $played_game->modified = date('Y-m-d H:i:s');   
      
      if ($played_game->validate()) {
        $played_game->save();  
      } else {
        throw new CHttpException(500, Yii::t('app', 'Internal Server Error.'));
      }
      $game->played_game_id = $played_game->id;
    }
    
    $data['turn'] = $game_engine->getTurn($game);
    $this->sendResponse($data);
  }
  
  /**
   * Processes the POST request of the play method call xxx
   */
  private function _playPost($game, $game_engine) {
    
  }
  
  private function _getTurn($game, $game_engine) {
    if ($game->played_game_id) {
      
    }
  }
  
  
}