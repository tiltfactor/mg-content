MG_GAME_API = function ($) {
  return $.extend(MG_API, {
    turns : [],
    turn : 0,
    
    // make sure the default game object always exists
    // this will be extended by each game
    game : {
      name : '',
      description: '',
      more_info_url : ''
    },
    
    /*
     * initialize games. called by a games implementation. ensures that default values and
     * needed parameter are set. will also initialize the API (to e.g. retrieve the SHARED SECRET)
     */
    game_init : function (options) {
      var settings = $.extend({
        onapiinit: MG_GAME_API.onapiinit,
        partner_wait_threshold: 20, // how many seconds will we wait until timeout
        partner_waiting_time: 0, // how many seconds did we wait until timeout
        message_queue_interval: 500,
        onunload : function () {return 'Quit ' + MG_GAME_API.game.name + '?';}
      }, options);
      
      MG_GAME_API.settings = $.extend(MG_GAME_API.settings, settings); //Pull from both defaults and supplied options
      
      MG_GAME_API.api_init(MG_GAME_API.settings);

      MG_GAME_API.observeOnBeforeUnload(settings.onunload);
    },
    
    /*
     * Callback called if the API has been successfully initialized
     */
    onapiinit : function () {
      MG_GAME_API.loadGame();
    },
    
    /*
     * Attempt to initialize a game via a GET call
     */
    loadGame : function () {
      MG_API.waitForThrottleIntervalToPass(function () {
        MG_API.ajaxCall('/games/play/gid/' + MG_GAME_API.settings.gid , function(response) {
          if (MG_API.checkResponse(response)) {
            MG_GAME_API.game = $.extend(MG_GAME_API.game, response.game);
            MG_GAME_API.settings.ongameinit(response);
          }
        });  
      });
    },
    
    /*
     * helper function to extract license infos from the images as comma separated string
     * 
     * @param array licences The licences of the images 
     * @return string The licence names
     */
    parseLicenceInfo : function (licences) {
      var img_licence_info = [];
      $(licences).each(function (i, licence) {
        img_licence_info.push(licence.name);
      })
      return img_licence_info.join(", ");
    },
    
    /*
     * helper function to set an onbeforeunload callback handler
     * 
     * @param function callback Executed on the event a users leaves the page
     */
    observeOnBeforeUnload : function (callback) {
      $(window).bind('beforeunload', callback);
    },
    
    /*
     * helper function to clear the browser's before unload event
     */
    releaseOnBeforeUnload : function () {
      $(window).unbind('beforeunload');
    },
    
    /*
     * Interface to leave a message for another player in the active game's session
     * message queue. 
     * 
     * @param object message The message to leave JSON object 
     */
    postMessage : function (message) {
      if (message !== undefined && MG_GAME_API.game.played_game_id !== undefined) {
        MG_API.ajaxCall('/games/postmessage/played_game_id/' + MG_GAME_API.game.played_game_id , function (response) { 
            // no need to do anything errors are caught be the api 
          }, {
            type : 'post',
            data : {'message': message}
          }, true);
      }
    },
    
    /*
     * Standardized interface to call the GameAPI action. Allowing games to
     * implement additional API call back functions
     * 
     * @param string method Name of the callback method
     * @param object parameter The parameter to be passed on to the callback method
     * @param function callback Called back on success  
     * @param object options Further options to extend the AJAX calls
     */
    callGameAPI : function (method, parameter, callback, options) {
      MG_API.waitForThrottleIntervalToPass(function () {
       MG_API.ajaxCall('/games/gameapi/gid/' + MG_GAME_API.settings.gid + '/played_game_id/' + MG_GAME_API.game.played_game_id,
          callback,
          $.extend({
           type : 'post', 
            'data': {
              'call': {'method':method}, 
              'parameter': parameter
             }
           }, options));     
        
      });
    }
  });
}(jQuery);

