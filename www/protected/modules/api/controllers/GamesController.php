<?php

class GamesController extends ApiController {
  
  public function filters() {
    return array( // add blocked IP filter here
        'throttle - messages, abort, abortpartnersearch, postmessage',
        'IPBlock',
        'APIAjaxOnly', // custom filter defined in this class accepts only requests with the header HTTP_X_REQUESTED_WITH === 'XMLHttpRequest'
        'accessControl - messages, abort, abortpartnersearch, gameapi, postmessage',
        'sharedSecret', // the API is protected by a shared secret this filter ensures that it is regarded 
    );
  }
  
  /**
   * Defines the access rules for this controller
   */  
  public function accessRules() {
    return array(
      array('allow',
        'actions'=>array('index', 'scores', 'play', 'partner', 'messages', 'abort', 'abortpartnersearch', 'gameapi', 'postmessage'),
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
   * @return string JSON response
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
   * <pre>
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
   * </pre>
   * 
   * @return string JSON response
   */
  public function actionScores() {
    $data = array();
    $data['status'] = "ok";
    $data['scores'] = GamesModule::getTopPlayers();
    $this->sendResponse($data);
  }
  
  /**
   * Attempts to retrive the played game identified by the current users session id and the 
   * given played game id to notifiy the opponent that the user has left the game.
   * 
   * @param int $played_game_id the id of the currently played game
   * @return string JSON response with status message
   */
  public function actionAbort($played_game_id) {
    $data = array();
    $data['status'] = "ok";
    
    $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
    $user_session_id = (int)Yii::app()->session[$api_id .'_SESSION_ID'];
    
    $played_game = Yii::app()->db->createCommand()
                  ->select('pg.session_id_1, pg.session_id_2')
                  ->from('{{played_game}} pg')
                  ->where('pg.id=:pGameID', array(':pGameID' => $played_game_id)) 
                  ->queryRow();
                  
    if ($played_game) {
      $opponent_session_id = ($played_game["session_id_1"] == $user_session_id)? $played_game["session_id_2"] : $played_game["session_id_1"];
      $this->_leaveMessage($opponent_session_id, $played_game_id, 'aborted'); 
    } else {
      throw new CHttpException(400, Yii::t('app', 'Invalid request.'));
    }
    $this->sendResponse($data);
  }
  
  /**
   * Attempts to retrive the game_partner table entry identified by the given id. 
   * It it finds it. It will delete the set the row date to an 01/01/1970 and sends the other
   * user an abort message. If a second user should happen to be assigned to this id. 
   * 
   * This method is also used to skip the waiting for other player screen and play instantly
   * against the computer if the game does allow this option.
   * 
   * @param int $game_partner_id the id of the game_partner table entry
   * @return string JSON response with status message
   */
  public function actionAbortPartnerSearch($game_partner_id) {
    $data = array();
    $data['status'] = "ok";

    $this->_doAbortPartnerSearch($game_partner_id, true);
    
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
   * @return string JSON response with status message and messages for user if any
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
   * Attempts to retrive the played game identified by the current users session id and the 
   * given played game id to leave the posted message for the opponent. 
   * 
   * You have to make sure $_POST['message'] is set
   * 
   * @param int $played_game_id the id of the currently played game
   * @return string JSON response with status message
   */
  public function actionPostMessage($played_game_id) {
    $data = array();
    $data['status'] = "ok";
    
    if (Yii::app()->getRequest()->getIsPostRequest() && isset($_POST['message']) && (is_array($_POST['message']) || trim($_POST['message']) != "")) {
      $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
      $user_session_id = (int)Yii::app()->session[$api_id .'_SESSION_ID'];
      
      $played_game = Yii::app()->db->createCommand()
                    ->select('pg.session_id_1, pg.session_id_2')
                    ->from('{{played_game}} pg')
                    ->where('pg.id=:pGameID', array(':pGameID' => $played_game_id)) 
                    ->queryRow();
                    
      if ($played_game) {
        $opponent_session_id = ($played_game["session_id_1"] == $user_session_id)? $played_game["session_id_2"] : $played_game["session_id_1"];
        $this->_leaveMessage($opponent_session_id, $played_game_id, $_POST['message']); 
      } else {
        throw new CHttpException(400, Yii::t('app', 'Invalid request.'));
      }
    } else {
      $data['status'] = "error";
    }
    $this->sendResponse($data);
  }
  
  /**
   * This method is a bridge between the api and game engines. It allows games to extend the 
   * api with further game specific functionality.
   * 
   * All requests to this API hook are throttled for security. 
   * 
   * You have to make sure $_POST['call'] is set as this is required at the game engine to processs
   * the request. 
   * 
   * The JSON of call should be:
   * 
   * $_POST['call'] = {'method', 'name of method'}
   * 
   * You can optionally pass parameter by setting $_POST['parameter']
   * 
   * The minimum JSON of data should be:
   * 
   * $_POST['parameter'] = {
   *  'parameter1', 'value', 
   *  'parameter2', 'value',
   *  ... 
   * }
   * 
   * See the ZenPondGame component and JavaScript implementation for a working example.
   * 
   * @param string $gid the unique id the game is registered with in the system
   * @param int $played_game_id the id of the currently played game
   * @return string JSON response with status message and data generated by the api call
   */
  public function actionGameApi($gid, $played_game_id) {
    $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
    
    $data = array();
    $data['status'] = "ok";
    $valid_request = false;
    
    if (Yii::app()->getRequest()->getIsPostRequest() && isset($_POST['call']) && is_array($_POST['call'])) {
      $counted = Yii::app()->db->createCommand()
                    ->select('count(pg.id) as counted')
                    ->from('{{played_game}} pg')
                    ->where('pg.id=:pGameID', array(':pGameID' => $played_game_id)) 
                    ->queryScalar();
      
      if ($counted && $counted > 0) {
        
        $call = (object)$_POST['call'];
        if (isset($call->method) && trim($call->method) != "") {
          
          $game = GamesModule::loadGame($gid);
          if($game && $game->game_model) {
            
            $game_model = $game->game_model;
            $game_engine = GamesModule::getGameEngine($gid);
            if (is_null($game_engine)) {
              throw new CHttpException(500, Yii::t('app', 'Internal Server Error.'));
            }
            
            $game->played_game_id = (int)$played_game_id; 
            
            if (isset(Yii::app()->session[$api_id .'_PLAYED_AGAINST_COMPUTER_' . $game->played_game_id]) && 
                      Yii::app()->session[$api_id .'_PLAYED_AGAINST_COMPUTER_' . $game->played_game_id] === true) {
              $game->played_against_computer = true;
            }
            
            $parameter = null;
            if (isset($_POST['parameter']) && is_array($_POST['parameter'])) {
              $parameter = (object)$_POST['parameter'];
            }
            
            $data['response'] = $game_engine->gameAPI($game, $game_model, $call->method, $parameter);
            
            $valid_request = true;
          }  
        }
      }
    }
      
    if (!$valid_request) {
      throw new CHttpException(400, Yii::t('app', 'Invalid request.'));
    } else {
      $this->sendResponse($data);
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
   * @param string $gid the unique id the game is registered with in the system  
   * @return string JSON response with status message and data
   */
  public function actionPlay($gid) {
    $game = GamesModule::loadGame($gid);
    $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
    
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
            
            if (isset(Yii::app()->session[$api_id .'_PLAYED_AGAINST_COMPUTER_' . $game->played_game_id]) && 
                      Yii::app()->session[$api_id .'_PLAYED_AGAINST_COMPUTER_' . $game->played_game_id] === true) {
              $game->played_against_computer = true;
            }
          }
          
          if (isset($_POST["turn"])) {
            $game->turn = (int)$_POST["turn"]; 
          }
          
          $submission_valid = $game_engine->parseSubmission($game, $game_model);
          if ($game->played_game_id != 0 && $game->turn != 0 && $game->turn <= $game->turns && $submission_valid) {
            $this->_playTwoPlayerPost($game, $game_model, $game_engine);  
          } else {
            if (!$submission_valid) {
              throw new CHttpException(400, Yii::t('app', 'Your request\'s submission is invalid.'));
            } else {
              throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));  
            }
          }
        } else {
          // a GET request mean's that the user is about to start a new game
          // this means she has first to find a partner.
          $attempt = $game->partner_wait_threshold;
            
          if (isset($_GET["a"]))  // "a" == "attempt" attemp counts dowm
            $attempt -= (int)$_GET["a"];
          
          $game_partner_id = 0;
          if (isset($_GET["gp"]))  // "gp" == game_partner_id
            $game_partner_id = (int)$_GET["gp"];
          
          $game->turn = 0;
          
          $game->game_partner_name = Yii::t('app', "Anonymous");
          $partner_session_id = $this->_getPlayer($attempt, $game, $game_model, $game_engine); // this method changes the $game object by reference
          if ($partner_session_id) {
            Yii::app()->session[$api_id .'_WATING_GAME_' . $game->played_game_id] = false;
            $this->_playTwoPlayerGet($game, $game_model, $game_engine);  
          } else {
            if ($attempt == 0 && $game->play_against_computer) {
              $user_session_id = (int)Yii::app()->session[$api_id .'_SESSION_ID'];
              
              // make sure other player who accidentally assigned to this 
              // game_partner session are informed that the user is playing against the computer
              if ($game_partner_id > 0)
                $this->_doAbortPartnerSearch($game_partner_id, true); 
              
              Yii::app()->db->createCommand()
                  ->update('{{game_partner}}', array(
                      'session_id_2'=> $user_session_id,
                      'played_game_id'=> $game->played_game_id,
                    ), 'id=:id', array(':id'=>$user_session_id));
              
              $game->game_partner_name = Yii::t('app', "Computer");
              $game->played_against_computer = true;
              
              if (!$game->played_game_id && isset(Yii::app()->session[$api_id .'_SHARED_SECRET'])) {
                $this->_createPlayedGame($game, $game_model, $game_engine);
              }
              if ($game->played_game_id) {
                Yii::app()->session[$api_id .'_PLAYED_AGAINST_COMPUTER_' . $game->played_game_id] = true;
                $this->_playTwoPlayerGet($game, $game_model, $game_engine);
              } else {
                throw new CHttpException(500, Yii::t('app', 'Could not initialize game.'));
              }
            } else {
              $data = array();
              $data['status'] = "retry";
              $data['game'] = $game;
              unset($data['game']->game_id);
              unset($data['game']->arcade_image);
              $this->sendResponse($data);  
            }
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
   * Attempts to pair the waiting player with a second one. 
   * 
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
      
      // lock the table for other players reads until the current user has done his game_partner search
      // this is needed to avoid freak conditions where two users read at the same time the game_partner table and
      // register themselves as second player for the same game partner request
      Yii::app()->db->createCommand("LOCK TABLES {{game_partner}} WRITE, {{played_game}} WRITE, {{game_partner}} gp WRITE, {{session}} s READ, {{game}} WRITE")->execute(); 
      
      // does someone wait to play?
      $partner_session = Yii::app()->db->createCommand()
                    ->select('gp.id, gp.session_id_1, s.username')
                    ->from('{{game_partner}} gp')
                    ->join('{{session}} s', 's.id=gp.session_id_1')
                    ->where(array('and', 'gp.game_id = :gameID', 'gp.session_id_1 <> :sessionID', 'gp.session_id_2 IS NULL', 'gp.created > :created'), array(":gameID" => $game->game_id, ":sessionID" => $user_session_id, ":created" => date( 'Y-m-d H:i:s', time() - $game->partner_wait_threshold - 1))) // we have to adjust the milisecond threshol as javascript and server side time measuerment are slightly out of tune 
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
        $game->game_partner_id = $partner_session["id"];
        
        $found = true;
        
      } else { // no one is waiting let's register this user's request for waiting
        $game_partner = new GamePartner;
        $game_partner->session_id_1 = $user_session_id;
        $game_partner->game_id = $game->game_id;
        $game_partner->created = date('Y-m-d H:i:s');
        
        $game_partner->save();
        
        $game->game_partner_id = $game_partner->id;
      }
      Yii::app()->db->createCommand("UNLOCK TABLES")->execute(); 
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
   * Processes the GET request of the play method call
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
   *  submissions : [{ // JSON of this turns submission. The shape of the JSON request differs per game it will most likely be
   *      image_id: //id of the image that has been tagged
   *      tags: //string of submitted tags
   *  }],  
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
   *    ... 
   *    //a game will list most likely have has more fields, e.g the custom settings
   *  },
   *  turn : {
   *    score : 0, // numeric of the previous turn's score
   *    tags : { //information of the previous turn's tags 
   *      "user" : [{
   *        "tag" : 'tag1',
   *        "original" : '', // set if submitted tag differs from registered tag (3 dogs -> three dogs)
   *        "score" : 1, // score of this tag
   *        "weight" : 1 
   *      },
   *      ...
   *      ],
   *      "partner" : [{ TODO: implement define
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
   * @param Game $game_model the model representing the game in the database
   * @param object $game_engine the game engine of the game
   * @return string JSON response with status message and the game data
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
   * Processes the GET request of a two player game play method call
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
   *        "original" : '', // set if submitted tag differs from registered tag (3 dogs -> three dogs) 
   *        "score" : 1, // score of this tag
   *        "weight" : 1 
   *      },
   *      ...
   *      ],
   *      "partner" : [{ TODO: implement define
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
   * @return string JSON response with status message and the game data
   */
  private function _playTwoPlayerGet($game, $game_model, $game_engine) {
    $data = array();
    
    $data['status'] = "ok";
    $data['turn'] = $game_engine->getTurn($game, $game_model);
    
    $data['turn']['score'] = 0;
    
    $data['game'] = $game;
    
    //we don't want to send certain data
    unset($data['game']->game_id);
    unset($data['game']->arcade_image);
    $this->sendResponse($data);
  }
  
  /**
   * This method generates a new entry of a played game in the system. 
   * 
   * @param Object $game the current game object
   * @param Game $game_model the game model
   * @param Component $game_engine the game component
   * @param int $session_id_1 the id of the session of player one in the database
   * @param int $session_id_2 the id of the session of player two in the database 
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
   * Processes the POST request of the play method call
   * 
   * @param object $game the game object
   * @param object $game_model the model representing the game in the database
   * @param object $game_engine the game engine of the game
   * @return string JSON response with status message and the game data
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
   * Processes the POST request of the play method call of a two player game
   * 
   * @param object $game the game object
   * @param object $game_model the model representing the game in the database
   * @param object $game_engine the game engine of the game
   * @return string JSON response with status message and the game data
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
    
    $opponent_info = false;
    if (!$game->played_against_computer) {
      $opponent_info = Yii::app()->db->createCommand()
                  ->select('pg.session_id_1, pg.session_id_2, gs.submission')
                  ->from('{{played_game}} pg')
                  ->leftJoin('{{game_submission}} gs', 'gs.played_game_id=pg.id AND gs.turn=:turn AND gs.session_id <> :sessionID', array(
                      ':turn' => $game->turn,
                      ':sessionID' => $user_session_id,
                      ))
                  ->where('pg.id=:pGameID', array(':pGameID' => $game->played_game_id)) 
                  ->queryRow();
    }

    if ($game->played_against_computer || ($opponent_info && $game->submission_id)) { // yes request is for a valid and registered two player game
      $game->opponents_submission = null;
    
      if (!$game->played_against_computer) {
        $opponent_session_id = ($opponent_info["session_id_1"] == $user_session_id)? $opponent_info["session_id_2"] : $opponent_info["session_id_1"];
        $game->opponents_submission = json_decode($opponent_info["submission"]);
      }
      
      if ($game->played_against_computer || $game->opponents_submission) { // other user has submitted and been waiting for result
        if (!$game->played_against_computer && !Yii::app()->session[$api_id .'_WATING_GAME_' . $game->played_game_id]) {
          // the other user is waiting thus let him now that i've posted  
          $this->_leaveMessage($opponent_session_id, $game->played_game_id, 'posted'); // posted will trigger the game's javascript to repost the turn to finish it
        }
        $tags = $game_engine->parseTags($game, $game_model);
        
        $tags = $game_engine->setWeights($game, $game_model, $tags); // in there you can use weighting functions

        $turn_score = $game_engine->getScore($game, $game_model, $tags);
        
        $data['turn'] = $game_engine->getTurn($game, $game_model, $tags);
        
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
          
          if ($game->played_against_computer) {
            $played_game->score_1 = $played_game->score_1 + $turn_score;
            // we want to return the game's total score to the user.
            $data['turn']['score'] = $played_game->score_1;
          } else {
            if ($played_game->session_id_1 == $user_session_id) {
              $played_game->score_1 = $played_game->score_1 + $turn_score;
              // we want to return the game's total score to the user.
              $data['turn']['score'] = $played_game->score_1;
            } else {
              $played_game->score_2 = $played_game->score_2 + $turn_score;
              // we want to return the game's total score to the user.
              $data['turn']['score'] = $played_game->score_2;
            }
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
        $this->_leaveMessage($opponent_session_id, $game->played_game_id, 'waiting');
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
  
  /**
   * Creates or updates the user_to_game table entry for the user and game.
   * Either creates one or increments number_played by one and resets the core.
   * 
   * @param Object $game The game object
   * @param int $score The user's current score
   */
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
  private function _leaveMessage($session_id, $played_game_id, $message) {
    $num_rows_affected = Yii::app()->db->createCommand()
                            ->insert('{{message}}', array(
                              'session_id' => $session_id,
                              'played_game_id' => $played_game_id,
                              'message' => is_string($message)? $message : json_encode($message)
                              ));
  }
  
  
  /**
   * The method makes sure that an potential other partner becomes informed about the abort 
   * of a partner search by the other. 
   * 
   * @param int $game_partner_id the game partner id
   * @param boolean $invalidate_date true game_partner.create will be set to 1970/1/1 00:00.01 to make sure to invalidate the game_partner search 
   */
  private function _doAbortPartnerSearch($game_partner_id, $invalidate_date=false) {
    $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
    $user_session_id = (int)Yii::app()->session[$api_id .'_SESSION_ID'];
    
    Yii::app()->db->createCommand("LOCK TABLES {{game_partner}} WRITE")->execute(); 
    $game_partner = Yii::app()->db->createCommand()
                  ->select('session_id_1, session_id_2, played_game_id')
                  ->from('{{game_partner}}')
                  ->where('id=:gpID', array(':gpID' => $game_partner_id)) 
                  ->queryRow();
                  
    if ($game_partner) {
      if ($invalidate_date) {
        Yii::app()->db->createCommand()
                  ->update('{{game_partner}}', array('created' => date('Y-m-d H:i:s', 1)), 'id=:gpID',  array(':gpID' => $game_partner_id));
      }
      
      Yii::app()->db->createCommand("UNLOCK TABLES")->execute();
      
      if (!is_null($game_partner["played_game_id"]) && !is_null($game_partner["session_id_1"]) && !is_null($game_partner["session_id_2"])) {
        $opponent_session_id = ($game_partner["session_id_1"] == $user_session_id)? $game_partner["session_id_2"] : $game_partner["session_id_1"];
        $this->_leaveMessage($opponent_session_id, $game_partner["played_game_id"], 'aborted');
      }
      
    } else {
      Yii::app()->db->createCommand("UNLOCK TABLES")->execute();
      throw new CHttpException(400, Yii::t('app', 'Invalid request.'));
    }
  }
}