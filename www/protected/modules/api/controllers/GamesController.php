<?php

class GamesController extends ApiController {
  
  public function filters() {
    return array( // add blocked IP filter here
        'throttle - messages',
        'IPBlock',
        'APIAjaxOnly', // custom filter defined in this class accepts only requests with the header HTTP_X_REQUESTED_WITH === 'XMLHttpRequest'
        'accessControl - messages',
        'sharedSecret', // the API is protected by a shared secret this filter ensures that it is regarded 
    );
  }
  
  /**
   * Defines the access rules for this controller
   */  
  public function accessRules() {
    return array(
      array('allow',
        'actions'=>array('index', 'scores', 'play', 'partner', 'messages'),
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
   *    "turns":"4",
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
   *    
   *    // these fields might be set with single player games 
   *    "play_once_and_move_on":"0",
   *    "play_once_and_move_on_url":"",
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
   * Returns messages for the user playing the given game
   * 
   * Returned JSON: 
   * {
   *  "status" : "ok" or "error",
   *  "messages" : [{message:'message 1'}, {message:'message 2'}, {message:'message 3'}, ...]
   * }
   *
   * @param int $played_game_id The played game id
   */
  public function actionMessages($played_game_id) {
    $data = array();
    $data['status'] = "ok";
    $data['messages'] = array();
    
    $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
    $user_session_id = (int)Yii::app()->session[$api_id .'_SESSION_ID'];
    
    $messages = Yii::app()->db->createCommand()
                  ->select('m.message')
                  ->from('{{message}} m')
                  ->where('m.session_id=:sessionID AND m.played_game_id=:pGameID', array(':sessionID' => $user_session_id, ':pGameID' => $played_game_id)) 
                  ->queryAll();
    if ($messages) {
      $data['messages'] = $messages;
      Yii::app()->db->createCommand()
          ->delete('{{message}}', 'session_id=:sessionID AND played_game_id=:pGameID', array(':sessionID' => $user_session_id, ':pGameID' => $played_game_id));
    }
    $this->sendResponse($data);       
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
      
      $game->played_game_id = null; // at the moment we don't know the played game id. it should be present in the POST request
      
      if ($game_engine->two_player_game) { // we're dealing with a two player game 
        
        if (Yii::app()->getRequest()->getIsPostRequest()) {
          $game->request = new stdClass(); // all request parameter will be stored in this object
          
          if (isset($_POST["played_game_id"])) {
            $game->played_game_id = (int)$_POST["played_game_id"]; 
          }
          
          if (isset($_POST["turn"])) {
            $game->turn = (int)$_POST["turn"]; 
          }
          if ($game->played_game_id != 0 && $game->turn != 0 && $game->turn <= $game->turns && $game_engine->parseSubmission($game, $game_model)) {
            $this->_playTwoPlayerPost($game, $game_model, $game_engine);  
          } else {
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
          }
        } else {
          // a GET request mean's that the user is about to start a new game
          // this means she has first to find a partner.
          $attempt = $game->partner_wait_threshold;
            
          if (isset($_GET["a"]))  // "a" == "attempt"
            $attempt -= (int)$_GET["a"];
          
          $game->game_partner_name = "anonymous";
          $partner_session_id = $this->_getPlayer($attempt, $game, $game_model, $game_engine); // this method changes the $game object by reference
          if ($partner_session_id) {
            $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
            Yii::app()->session[$api_id .'_WATING_GAME_' . $game->played_game_id] = false;
            
            $game->turn = 0;
            $this->_playTwoPlayerGet($game, $game_model, $game_engine);  
          } else {
            $data = array();
            $data['status'] = "retry";
            $data['game'] = $game;
            unset($data['game']->game_id);
            unset($data['game']->arcade_image);
            $this->sendResponse($data);
          }
        }
      } else {
        // if the game is configured to be play once move on then set turns to 1
        // sometimes play_once_and_move_on might not be set at all
        if (isset($game->play_once_and_move_on) && (int)$game->play_once_and_move_on == 1) {
          $game->turns = 1;
        }
        
        if (Yii::app()->getRequest()->getIsPostRequest()) {
          $game->request = new stdClass(); // all request parameter will be stored in this object
          
          if (isset($_POST["played_game_id"])) {
            $game->played_game_id = (int)$_POST["played_game_id"]; 
          }
          
          if (isset($_POST["turn"])) {
            $game->turn = (int)$_POST["turn"]; 
          }
          if ($game->played_game_id != 0 && $game->turn != 0 && $game->turn <= $game->turns && $game_engine->parseSubmission($game, $game_model)) {
            $this->_playSinglePost($game, $game_model, $game_engine);  
          } else {
            throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
          }
        
        } else {
          $game->turn = 0;
          $this->_playSingleGet($game, $game_model, $game_engine); 
        } 
      }
    
    } else {
      throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
    }
  }
  
  /**
   * @param int $attempt the number of attempts (== seconds) left to until the partner search times out. 
   * @param object $game the game object
   * @param object $game_model the model representing the game in the database
   * @param object $game_engine the game engine of the game
   * @return boolean false if no partner found true if a partner has been found and the played_game is entered into the database
   */
  private function _getPlayer($attempt, &$game, $game_model, $game_engine) {
      
    $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
    $user_session_id = (int)Yii::app()->session[$api_id .'_SESSION_ID'];
    $found = false;
    
    if ($attempt == $game->partner_wait_threshold) { // first request for this game play
    
      // does someone wait to play?
      $partner_session = Yii::app()->db->createCommand()
                    ->select('gp.id, gp.session_id_1, s.username')
                    ->from('{{game_partner}} gp')
                    ->join('{{session}} s', 's.id=gp.session_id_1')
                    ->where(array('and', 'gp.session_id_1 <> :sessionID', 'gp.created > :created'), array(":sessionID" => $user_session_id, ":created" => date( 'Y-m-d H:i:s', time() - $game->partner_wait_threshold - 1))) // we have to adjust the milisecond threshol as javascript and server side time measuerment are slightly out of tune 
                    ->order('gp.created ASC')
                    ->limit(1)
                    ->queryRow();
      
      if ($partner_session) { // someone is waiting to play we can add the user's session id and return the partner's session_id
        $this->_createPlayedGame($game, $game_model, $game_engine, (int)$partner_session["session_id_1"], $user_session_id); 
        
        Yii::app()->db->createCommand()
              ->update('{{game_partner}}', array(
                  'session_id_2'=> $user_session_id,
                  'played_game_id'=> $game->played_game_id,
                ), 'id=:id', array(':id'=>$partner_session["id"]));
        
        $game->game_partner_name = $partner_session["username"];
        
        $found = true;
        
      } else { // no one is waiting let's register this user's request for waiting
        $played_game = new GamePartner;
        $played_game->session_id_1 = $user_session_id;
        $played_game->game_id = $game->game_id;
        $played_game->created = date('Y-m-d H:i:s');
        
        $played_game->save();
        
        $game->game_partner_id = $played_game->id;
      }
    } else {
      // the user waits for a partner. 
      
      $game->game_partner_id = null;
      if (isset($_GET["gp"]) && (int)$_GET["gp"] != 0) { // we have found the game_partner id, let's look if she found a partner
        $game->game_partner_id = (int)$_GET["gp"];
        
        $partner_session = Yii::app()->db->createCommand()
                    ->select('gp.id, gp.played_game_id, s.username')
                    ->from('{{game_partner}} gp')
                    ->join('{{session}} s', 's.id=gp.session_id_2')
                    ->where('gp.id=:ID', array(":ID" => (int)$_GET["gp"])) 
                    ->queryRow();
        if ($partner_session && !is_null($partner_session["played_game_id"])) { // yes return the other users session id to go on
          $game->played_game_id = $partner_session["played_game_id"];
          $game->game_partner_name = $partner_session["username"];
          $found = true;
        }
      }
    }
    
    return $found;
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
   *    'play_once_and_move_on' => '0|1', // if the game is a single player game
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
   * @param object $game the game object
   * @param object $game_model the model representing the game in the database
   * @param object $game_engine the game engine of the game 
   */
  private function _playSingleGet($game, $game_model, $game_engine) {
    $data = array();
    
    $data['status'] = "ok";
    
    $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
    if (!$game->played_game_id && isset(Yii::app()->session[$api_id .'_SHARED_SECRET'])) {
      $this->_createPlayedGame($game, $game_model, $game_engine);
    }
    
    $data['turn'] = $game_engine->getTurn($game, $game_model);
    $data['turn']['score'] = 0;
    
    
    $data['game'] = $game;
    
    //we don't want to send certain data
    unset($data['game']->game_id);
    unset($data['game']->arcade_image);
    $this->sendResponse($data);
  }
  
  /**
   * Processes the GET request of the play method call xxx this should be the two player version
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
   *    'play_once_and_move_on' => '0|1', // if the game is a single player game
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
   * @param object $game the game object
   * @param object $game_model the model representing the game in the database
   * @param object $game_engine the game engine of the game 
   */
  private function _playTwoPlayerGet($game, $game_model, $game_engine) {
    $data = array();
    
    $data['status'] = "ok";
    
    $data['turn'] = $this->loadTwoPlayerTurnFromDb($game->played_game_id, 1);
    if (is_null($data['turn'])) {
      $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
      $data['turn'] = $game_engine->getTurn($game, $game_model);
      if (!$this->saveTwoPlayerTurnToDb($game->played_game_id, 1, (int)Yii::app()->session[$api_id .'_SESSION_ID'], $data['turn']))
        $data['turn'] = $this->loadTwoPlayerTurnFromDb($game->played_game_id, 1); // in a freak accident if might happen that for both user it might appear to be the first one to read the table
    }  
    
    if (is_null($data['turn'])) {
      throw new CHttpException(500, Yii::t('app', 'Internal Server Error.'));
    }
    
    $data['turn']['score'] = 0;
    
    $data['game'] = $game;
    
    //we don't want to send certain data
    unset($data['game']->game_id);
    unset($data['game']->arcade_image);
    $this->sendResponse($data);
  }
  
  /**
   * This method generates xxx
   */
  private function _createPlayedGame(&$game, &$game_model, &$game_engine, $session_id_1=null, $session_id_2=null) {
    $played_game = new PlayedGame;
    
    if ($game_engine->two_player_game && $session_id_1 && $session_id_2) {
      $played_game->session_id_1 = (int)$session_id_1;
      $played_game->session_id_2 = (int)$session_id_2;
    } else {
      $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
      $played_game->session_id_1 = (int)Yii::app()->session[$api_id .'_SESSION_ID'];
    }
    
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
  
  /**
   * Processes the POST request of the play method call xxx
   * 
   * @param object $game the game object
   * @param object $game_model the model representing the game in the database
   * @param object $game_engine the game engine of the game
   */
  private function _playSinglePost($game, $game_model, $game_engine) {
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

      
      if ($game->turn == $game->turns || (isset($game->play_once_and_move_on) && (int)$game->play_once_and_move_on == 1)) { // final turn
        $this->_saveUserToGame($game, $data['turn']['score']);
      }
    } else {
      throw new CHttpException(500, Yii::t('app', 'Internal Server Error.'));
    }
    
    $data['game'] = $game;
    
    //we don't want to send certain data
    unset($data['game']->game_id);
    unset($data['game']->arcade_image);
    unset($data['game']->request);
    $this->sendResponse($data);
  }
  
  /**
   * Processes the POST request of the play method call xxx
   * 
   * @param object $game the game object
   * @param object $game_model the model representing the game in the database
   * @param object $game_engine the game engine of the game
   */
  private function _playTwoPlayerPost($game, $game_model, $game_engine) {
    $data = array();
    $data['status'] = "ok";
    
    $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
    $user_session_id = (int)Yii::app()->session[$api_id .'_SESSION_ID'];
    
    $game->submission_id = Yii::app()->db->createCommand()
                  ->select('gs.id')
                  ->from('{{game_submission}} gs')
                  ->where('gs.session_id=:sessionID AND gs.turn=:turn AND gs.played_game_id = :pGameID', array(
                      ':sessionID' => $user_session_id,
                      ':turn' => $game->turn,
                      ':pGameID' => $game->played_game_id)) 
                  ->queryScalar();
    
    if (!$game->submission_id)
      $game->submission_id = $game_engine->saveSubmission($game, $game_model); 
    
    $opponent_info = Yii::app()->db->createCommand()
                  ->select('pg.session_id_1, pg.session_id_2, gs.submission')
                  ->from('{{played_game}} pg')
                  ->leftJoin('{{game_submission}} gs', 'gs.played_game_id=pg.id AND gs.turn=:turn AND gs.session_id <> :sessionID', array(
                      ':turn' => $game->turn,
                      ':sessionID' => $user_session_id,
                      ))
                  ->where('pg.id=:pGameID', array(':pGameID' => $game->played_game_id)) 
                  ->queryRow();
      
    if ($opponent_info && $game->submission_id) { // yes request is for a valid and regstered two player game
      $opponent_session_id = ($opponent_info["session_id_1"] == $user_session_id)? $opponent_info["session_id_2"] : $opponent_info["session_id_1"];
      $game->opponents_submission = json_decode($opponent_info["submission"]);
      
      if ($game->opponents_submission) { // other user has submitted and been waiting for result
        if (!Yii::app()->session[$api_id .'_WATING_GAME_' . $game->played_game_id]) {
          // the other user is waiting thus let him now that i've posted  
          $this->leaveMessage($opponent_session_id, $game->played_game_id, 'posted'); // posted will trigger the game's javascript to repost the turn to finish it
        }
        $tags = $game_engine->parseTags($game, $game_model);
        
        $tags = $game_engine->setWeights($game, $game_model, $tags); // in there you can use weighting functions

        $turn_score = $game_engine->getScore($game, $game_model, $tags);
        
        $data['turn'] = $this->loadTwoPlayerTurnFromDb($game->played_game_id, $game->turn + 1);
        if (is_null($data['turn'])) {
          $data['turn'] = $game_engine->getTurn($game, $game_model);
          $this->saveTwoPlayerTurnToDb($game->played_game_id, $game->turn + 1, (int)Yii::app()->session[$api_id .'_SESSION_ID'], $data['turn']);
        }   
        
        $data['turn']['score'] = 0;
        
        $data['turn']["tags"] = array();
        $data['turn']["tags"]["user"] = $tags;
        
        if (isset($game->opponents_submission["parsed"]))
          $data['turn']["tags"]["opponent"] = $game->opponents_submission["parsed"];
        
        MGTags::saveTags($tags, $game->submission_id);
        
        // update played_game
        $played_game = PlayedGame::model()->findByPk($game->played_game_id);
        if ($played_game) {
          $played_game->modified = date('Y-m-d H:i:s');
          
          if ($played_game->session_id_1 == $user_session_id) {
            $played_game->score_1 = $played_game->score_1 + $turn_score;
            // we want to return the game's total score to the user.
            $data['turn']['score'] = $played_game->score_1;
          } else {
            $played_game->score_2 = $played_game->score_2 + $turn_score;
            // we want to return the game's total score to the user.
            $data['turn']['score'] = $played_game->score_2;
          }
          
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
        
        if ($game->turn == $game->turns || (isset($game->play_once_and_move_on) && (int)$game->play_once_and_move_on == 1)) { // final turn
          $this->_saveUserToGame($game, $data['turn']['score']);
        }
        Yii::app()->session[$api_id .'_WATING_GAME_' . $game->played_game_id] = false;
      } else { // other player has not submitted this turn
        $this->leaveMessage($opponent_session_id, $game->played_game_id, 'waiting');
        Yii::app()->session[$api_id .'_WATING_GAME_' . $game->played_game_id] = true;
        $data['status'] = "wait"; // this will make the client wait. It will query the message queue for a message that the other user has posted
      }
      
    } else {
      throw new CHttpException(500, Yii::t('app', 'Internal Server Error.'));
    }
    
    $data['game'] = $game;
    
    //we don't want to send certain data
    unset($data['game']->game_id);
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
  
  /**
   * Stores a message in the system for a user. The messages can be retrieved via the API. 
   * 
   * @param int $session_id The session id of the for whom a message should be stored
   * @param int $played_game_id the played game id for which a message should be stored
   * @param string $message the message for the user
   */
  private function leaveMessage($session_id, $played_game_id, $message) {
    $num_rows_affected = Yii::app()->db->createCommand()
                            ->insert('{{message}}', array(
                              'session_id' => $session_id,
                              'played_game_id' => $played_game_id,
                              'message' => $message
                              ));
  }
  
  
  /** 
   * loads a turn from the played_game_turn_info table and parses and returns the stored turn info
   * 
   * @param int $played_game_id the played game id
   * @param int $turn the turn number
   * @param boolean $assoc flag for the json_decode assoc array flag (if true elements will be returned as array and not objects)
   * @return mixed array of turn data or null
   */
  private function loadTwoPlayerTurnFromDb($played_game_id, $turn, $assoc=TRUE) {
    $turn_data = Yii::app()->db->createCommand()
                  ->select('pgti.data')
                  ->from('{{played_game_turn_info}} pgti')
                  ->where('pgti.turn=:turn AND pgti.played_game_id = :pGameID', array(
                      ':turn' => $turn,
                      ':pGameID' => $played_game_id)) 
                  ->queryScalar();
    if ($turn_data) {
      return json_decode($turn_data, $assoc);
    } else {
      return null;
    }
  }
  
  /** 
   * stores a turn into the database. the passed data will be json_encoded
   * 
   * @param int $played_game_id the played game id
   * @param int $turn the turn number
   * @param int $session_id the current user's session id
   * @param mixed $data the data that shall be stored in the database 
   */
  private function saveTwoPlayerTurnToDb($played_game_id, $turn, $session_id, $data) {
    try {
      $cmd  = Yii::app()->db->createCommand()
                  ->insert('{{played_game_turn_info}}', array(
                    'played_game_id' => $played_game_id, 
                    'turn' => $turn, 
                    'created_by_session_id' => $session_id, 
                    'data' => json_encode($data),
                  ));
    } catch (CDbException $e) {
      return false;
    }
    return true;
  }
}