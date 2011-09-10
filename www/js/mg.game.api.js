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
      
      $(window).bind('beforeunload', function() {return 'Quit ' + MG_GAME_API.game.name + '?';});
    },
    
    onapiinit : function () {
      MG_GAME_API.loadGame();
    },
    
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
    
    parseLicenceInfo : function (licences) {
      var img_licence_info = [];
      $(licences).each(function (i, licence) {
        img_licence_info.push(licence.name);
      })
      return img_licence_info.join(", ");
    }
  });
}(jQuery);


