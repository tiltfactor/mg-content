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
   * This action returns a list of all games available in the system
   * You'll have to set the HTTP_X_<fbvStorage(api_id)>_SHARED_SECRET with the current session's
   * shared secret.
   * 
   * It will return an array of objects. Each object represents the game
   * 
   * Returned JSON
   * {
   *  "status" : "ok" or "error",
   *  "games" : [{
   *    "name":"Game Name",
   *    "description":"Game description",
   *    "arcade_image":"zentag_arcade.png",
   *    "more_info_url":"",
   *    "play_once_and_move_on":"0",
   *    "play_once_and_move_on_url":"",
   *    "turns":"4",
   *    "score_new":"2",
   *    "score_match":"1",
   *    "score_expert":"3",
   *    "image_width":"450",
   *    "image_height":"450",
   *    "game_id":"1",
   *    "gid":"ZenTag",
   *    "url":"\/index_dev.php\/games\/ZenTag",
   *    "image_url":"\/assets\/66beaa01\/zentag\/images\/zentag_arcade.png",
   *    "api_base_url":"http:\/\/metadatagames.test\/index_dev.php\/api",
   *    "base_url":"http:\/\/metadatagames.test",
   *    "user_name":"Guest",
   *    "user_num_played":0,
   *    "user_score":0,
   *    "user_authenticated":false
   *  },
   *  ...
   *  ]
   * }
   * 
   */
  public function actionIndex() {
    if(Yii::app()->getRequest()->getIsAjaxRequest()) {
      $data = array();
      $data['status'] = "ok";
      $data['games'] = array();
      
      $games = GamesModule::listActiveGames();
      if ($games) {
        foreach($games as $game) {
          // we want to hide some things
          unset($game->game_model);
          $data['games'][] = $game;
        }
      }
      $this->sendResponse($data);  
    } else {
      parent::actionIndex();
    }
  }
  
  /**
   * Returns the top 10 score list of all users
   * 
   * Returned JSON
   * {
   *  "status" : "ok" or "error",
   *  "scores" : [{
   *      "id":"1",
   *      "username":"admin",
   *      "score":"291",
   *      "number_played":"22"
   *    },
   *    ...
   *  ]
   * }
   */
  public function actionScores() {
    $data = array();
    $data['status'] = "ok";
    $data['scores'] = GamesModule::getTopPlayers();
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
   * This method handels play requests into the system. It distinguishes between GET and POST requests. 
   * A GET requests is the initial call for a game. It prepares the needed database entries and provides 
   * the first turn's information. 
   * 
   * The user submits data as POST request. In the post request the users submission will be parsed, weightend, 
   * scored and stored in the database. The post method returns scoring results and the next turns information.
   * 
   * @param string $gid the unique id the game is regitered with in the system  
   */
  public function actionPlay($gid) {
    $game = GamesModule::loadGame($gid);
    
    if($game && $game->game_model) {
        
      $game_engine = GamesModule::getGameEngine($gid);
      if (is_null($game_engine)) {
        throw new CHttpException(500, Yii::t('app', 'Internal Server Error.'));
      }
      
      $game_model = $game->game_model; // we need the current games model in the game engine but don't want to risk to send it on to the user
      unset($game->game_model);
      
      // if the game is configured to be play once move on then set turns to 1
      if ((int)$game->play_once_and_move_on == 1) {
        $game->turns = 1;
      }
      
      $game->played_game_id = null;
      if (Yii::app()->getRequest()->getIsPostRequest()) {
        $game->request = new stdClass(); // all request parameter will be stored in this object
        
        if (isset($_POST["played_game_id"])) {
          $game->played_game_id = (int)$_POST["played_game_id"]; 
        }
        
        if (isset($_POST["turn"])) {
          $game->turn = (int)$_POST["turn"]; 
        }
        if ($game->played_game_id != 0 && $game->turn != 0 && $game->turn <= $game->turns && $game_engine->parseSubmission($game, $game_model)) {
          $this->_playPost($game, $game_model, $game_engine);  
        } else {
          throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
        }
      
      } else {
        $game->turn = 0;
        $this->_playGet($game, $game_model, $game_engine);
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
   *  submissions[] : { // JSON of this turns submission. The shape of the JSON request differs per game it will most likely be
   *      image_id: //id of the image that has been tagged
   *      tags: //string of submitted tags
   *  } 
   *  ... // you can add further values that are important for a particular game e.g. wordstoavoid
   *  
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
   *    'user_num_played' => 0 or x // if the user is authenticated how many times has the user finished this game  
   *    'user_authentiated => false/true // true if user is authenticated 
   *    //a game could have more fields
   *  },
   *  turn : {
   *    score : 0, // numeric of the previous turn's score
   *    tags : { //information of the previous turn's tags 
   *      "user" : [{
   *        "tag" : 'tag1',
   *        "original" : '', // set if submitted tag differs from registered tag (3 dogs -> three dogs) // xxx define cleanup rules
   *        "score" : 1, // score of this tag
   *        "weight" : 1 
   *      },
   *      ...
   *      ],
   *      "partner" : [{ xxx implement define
   *        "tag" : 'tag1',
   *        "tag" : 'tag1',
   *      },
   *      ...
   *      ]
   *    },
   *    // the following fields are available in all games
   *    images : [{
   *      // all urls are relative to game.base_url
   *      full_size : '',
   *      scaled : '',
   *      thumbnail : '',
   * 
   *      licences : [1,2,3] //id of licence(s) of the image that can be found in turn.licences,
   *      id : 1 // the id of the image in the database
   *    }, {...}],
   * 
   *    licences : [{
   *      id: '',
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
  private function _playGet($game, $game_model, $game_engine) {
    $data = array();
    
    $data['status'] = "ok";
    
    
    
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
      
      // increase game counter by one      
      $game_model->saveCounters(array('number_played'=>1));
      
    }
    $data['turn'] = $game_engine->getTurn($game, $game_model);
    $data['turn']['score'] = 0;
    
    
    $data['game'] = $game;
    
    //we don't want to send certain data
    unset($data['game']->game_id);
    unset($data['game']->score_new);
    unset($data['game']->score_match);
    unset($data['game']->score_expert);
    unset($data['game']->arcade_image);
    
    $this->sendResponse($data);
  }
  
  /**
   * Processes the POST request of the play method call xxx
   */
  private function _playPost($game, $game_model, $game_engine) {
    $data = array();
    $data['status'] = "ok";
    
    $game->submission_id = $game_engine->saveSubmission($game, $game_model); 
    
    if ($game->submission_id) {
      
      $tags = $game_engine->parseTags($game, $game_model);
      
      $tags = $game_engine->setWeights($game, $game_model, $tags); // in there you can use weighting functions
      
      $data['turn']['score'] = 0; 
      $turn_score = $game_engine->getScore($game, $game_model, $tags);
      
      $data['turn'] = $game_engine->getTurn($game, $game_model, $tags);
      
      MGTags::saveTags($tags, $game->submission_id);
    
      
      // update played_game
      $played_game = PlayedGame::model()->findByPk($game->played_game_id);
      if ($played_game) {
        $played_game->modified = date('Y-m-d H:i:s');
        $played_game->score_1 = $played_game->score_1 + $turn_score;
        
        // we want to return the game's total score to the user.
        $data['turn']['score'] = $played_game->score_1;
        
        if ($game->turn == $game->turns) { // final turn 
          $played_game->finished = date('Y-m-d H:i:s');
        }
        
        if ($played_game->validate()) {
          $played_game->save();  
        } else {
          throw new CHttpException(500, Yii::t('app', 'Internal Server Error.'));
        }
      } else {
        throw new CHttpException(500, Yii::t('app', 'Internal Server Error.'));
      } 

      
      if ($game->turn == $game->turns || (int)$game->play_once_and_move_on == 1) { // final turn
        $this->_saveUserToGame($game, $data['turn']['score']);
      }
    } else {
      throw new CHttpException(500, Yii::t('app', 'Internal Server Error.'));
    }
    
    $data['game'] = $game;
    
    //we don't want to send certain data
    unset($data['game']->game_id);
    unset($data['game']->score_new);
    unset($data['game']->score_match);
    unset($data['game']->score_expert);
    unset($data['game']->arcade_image);
    unset($data['game']->request);
    
    $this->sendResponse($data);
  }
  
  
  private function _saveUserToGame($game, $score=null) {
    if (!Yii::app()->user->isGuest) {
      $userToGame = UserToGame::model()->findByPk(array(
        "user_id" => Yii::app()->user->id,
        "game_id" => $game->game_id,
        ));
      if ($userToGame) {
        $userToGame->saveCounters(array('number_played'=>1, 'score'=>$score));
      } else {
        $userToGame = new UserToGame;
        $userToGame->user_id = Yii::app()->user->id;
        $userToGame->game_id = $game->game_id;
        $userToGame->score = $score;
        $userToGame->number_played = 1;
        if ($userToGame->validate()) { // final turn
          $userToGame->save();
        } else {
          Yii::log($userToGame->errors, 'error');
          throw new CHttpException(500, Yii::t('app', 'Internal Server Error.'));
        }
      }
    }
  }
}