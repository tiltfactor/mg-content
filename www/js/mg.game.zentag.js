MG_GAME_ZENTAG = function ($) {
  return $.extend(MG_GAME_API, {
    init : function (options) {
      var settings = $.extend(options, {
        ongameinit: MG_GAME_ZENTAG.ongameinit
      });
      MG_GAME_API.game_init(settings);
    },
    
    ongameinit : function () {
      //score box
      var score_info = {
        user_name : MG_GAME_ZENTAG.game.user_name,
        user_score : MG_GAME_ZENTAG.game.user_score,
        current_score : 0,
        turns : MG_GAME_ZENTAG.game.turns,
        current_turn : 0
      };
      $("#template-scores").tmpl(score_info ).appendTo($("#scores"));
      if (!MG_GAME_ZENTAG.game.user_authenticated) {
        $("#scores .total_score").remove();
      }
      MG_GAME_API.curtain.hide();
      $("#stage").fadeIn(1500);
    },
  });
}(jQuery);


