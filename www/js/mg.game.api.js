MG_GAME_API = function ($) {
  return $.extend(MG_API, {
    turns : [],
    turn : 0,
    
    game : {
      name : '',
      description: '',
      more_info_url : ''
    },
    
    game_init : function (options) {
      var settings = $.extend(options, {
        onapiinit: MG_GAME_API.onapiinit
      });
      
      //Combine options with default settings
      if (options) {
        MG_GAME_API.settings = $.extend(MG_GAME_API.settings, settings); //Pull from both defaults and supplied options
      }
      
      MG_GAME_API.api_init(settings);
      // xxx activate after development$(window).bind('beforeunload', function() {return 'Quit ' + MG_GAME_API.game.name + '?';});
    },
    
    onapiinit : function () {
      MG_GAME_API.loadGame();
      log("onapiinit init");
    },
    
    loadGame : function () {
      MG_API.ajaxCall('/games/play/gid/' + MG_GAME_API.settings.gid , function(response) {
        MG_GAME_API.game = $.extend(MG_GAME_API.game, response.game);
        
        MG_GAME_API.settings.ongameinit(response);
      });
    },
    
    process : function (response, loadGame) {
      
    }
  });
}(jQuery);


