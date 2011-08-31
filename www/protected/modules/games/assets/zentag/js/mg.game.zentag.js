MG_GAME_ZENTAG = function ($) {
  return $.extend(MG_GAME_API, {
    wordField : null,
    wordFieldCleared : false,
    
    submitButton : null,
    
    init : function (options) {
      var settings = $.extend(options, {
        ongameinit: MG_GAME_ZENTAG.ongameinit
      });
      
      MG_GAME_ZENTAG.wordField = $("#words").focus(
        function () {
          if (!MG_GAME_ZENTAG.wordFieldCleared) {
            MG_GAME_ZENTAG.wordField.val("");
            MG_GAME_ZENTAG.wordFieldCleared = true;
          }
        }
      );
      
      // submit on enter
      MG_GAME_ZENTAG.wordField.keydown(function(event) {
        if(event.keyCode == 13) {
          MG_GAME_ZENTAG.onsubmit(); 
          return false;
        }
      });
      
      MG_GAME_ZENTAG.submitButton = $("#button-play").click(MG_GAME_ZENTAG.onsubmit);
      
      MG_GAME_API.game_init(settings);
    },
    
    submit : function () {
      MG_API.ajaxCall('/games/play/gid/' + MG_GAME_API.settings.gid , function(response) {
        if (MG_API.checkResponse(response)) { // we have to check whether the API returned a HTTP Status 200 but still json.status == "error" response
          MG_GAME_API.game = $.extend(MG_GAME_API.game, response.game);
          MG_GAME_API.settings.ongameinit(response);
        }
      });
    },
    
    renderTurn : function (response, score_info, turn_info, licence_info, more_info) {
      $("#stage").hide();
      
      $("#scores").html(""); 
      $("#template-scores").tmpl(score_info ).appendTo($("#scores"));
      if (!MG_GAME_ZENTAG.game.user_authenticated) {
        $("#scores .total_score").remove();
      }
      
      $("#image_container").html("");
      $("#template-turn").tmpl(turn_info).appendTo($("#image_container"));
      
      $("a[rel='zoom']").colorbox();
      
      $("#stage").fadeIn(1500, function () {MG_GAME_ZENTAG.busy = false;});
    },
    
    renderFinal : function (response, score_info, turn_info, licence_info, more_info) {
      $("#stage").hide();
      
      $("#scores").html(""); 
      
      $("#fieldholder").html("");
      $("#template-final-info").tmpl(score_info ).appendTo($("#fieldholder"));
      
      $("#image_container").html("");
      $("#template-final-summary").tmpl(turn_info).appendTo($("#image_container"));
      
      $("a[rel='zoom']").colorbox();
      
      $(window).unbind('beforeunload');
      MG_GAME_ZENTAG.submitButton.addClass("again").unbind("click").attr("href", window.location.href);
      
      $("#stage").fadeIn(1500, function () {MG_GAME_ZENTAG.busy = false;});
    },
    
    onresponse : function (response) {
      MG_GAME_API.curtain.hide();
      
      MG_GAME_ZENTAG.turn++;
      MG_GAME_ZENTAG.turns.push(response.turn);

      if (MG_GAME_ZENTAG.turn > MG_GAME_ZENTAG.game.turns) { // render final result

        //score box
        var score_info = {
          user_name : MG_GAME_ZENTAG.game.user_name,
          user_score : MG_GAME_ZENTAG.game.user_score,
          current_score : response.turn.score,
          user_num_played : MG_GAME_ZENTAG.game.user_num_played,
          turns : MG_GAME_ZENTAG.game.turns,
          current_turn : MG_GAME_ZENTAG.turn
        };
        
        var licence_info = {}; //xxx add licence parsing here
        
        var more_info = {}; //xxx add more_info parsing here
        
        // turn info == image 
        var turn_info = {
          url_1 : MG_GAME_ZENTAG.turns[0].images[0].thumbnail,
          url_full_size_1 : MG_GAME_ZENTAG.turns[0].images[0].full_size,
          licence_info_1 : 'xxx add some licence info',
          url_2 : MG_GAME_ZENTAG.turns[1].images[0].thumbnail,
          url_full_size_2 : MG_GAME_ZENTAG.turns[1].images[0].full_size,
          licence_info_2 : 'xxx add some licence info',
          url_3 : MG_GAME_ZENTAG.turns[2].images[0].thumbnail,
          url_full_size_3 : MG_GAME_ZENTAG.turns[2].images[0].full_size,
          licence_info_3 : 'xxx add some licence info',
          url_4 : MG_GAME_ZENTAG.turns[3].images[0].thumbnail,
          url_full_size_4 : MG_GAME_ZENTAG.turns[3].images[0].full_size,
          licence_info_4 : 'xxx add some licence info'
        }
       
        MG_GAME_API.renderFinal(response, score_info, turn_info, licence_info, more_info); 
      } else {
        
        //score box
        var score_info = {
          user_name : MG_GAME_ZENTAG.game.user_name,
          user_score : MG_GAME_ZENTAG.game.user_score + " xxx not implemented",
          current_score : response.turn.score,
          user_num_played : MG_GAME_ZENTAG.game.user_num_played,
          turns : MG_GAME_ZENTAG.game.turns,
          current_turn : MG_GAME_ZENTAG.turn
        };
        
        var licence_info = {}; //xxx add licence parsing here
        
        var more_info = {}; //xxx add more_info parsing here
        
        // turn info == image 
        var turn_info = {
          url : response.turn.images[0].scaled,
          url_full_size : response.turn.images[0].full_size,
          licence_info : 'xxx add some licence info'
        }
        
        MG_GAME_API.renderTurn(response, score_info, turn_info, licence_info, more_info); 
      }
    },
    
    onsubmit : function () {
      if (!MG_GAME_ZENTAG.busy) {
        var tags = MG_GAME_ZENTAG.wordField.val().replace(/^\s+|\s+$/g,"");
        if (tags == "" || !MG_GAME_ZENTAG.wordFieldCleared) {
          // val filtered for all white spaces (trim)
          MG_GAME_ZENTAG.error("<h1>Ooops</h1><p>Please enter at least one word</p>");
        } else {
          log("submit");
          MG_GAME_API.curtain.show();
          MG_GAME_ZENTAG.busy = true;
          
          MG_API.ajaxCall('/games/play/gid/' + MG_GAME_API.settings.gid , function(response) {
            if (MG_API.checkResponse(response)) {
              MG_GAME_ZENTAG.wordField.val("");
              MG_GAME_ZENTAG.onresponse(response);
            }
          }, {
            type:'post',
            data: {
              turn:MG_GAME_ZENTAG.turn,
              played_game_id:MG_GAME_ZENTAG.game.played_game_id,
              'submissions': [{
                image_id : MG_GAME_ZENTAG.turns[MG_GAME_ZENTAG.turn-1].images[0].image_id,
                tags: tags
              }]
            }
          });
        }
      }
      return false;
    },
    
    ongameinit : function (response) {
      MG_GAME_ZENTAG.onresponse(response);
    },
  });
}(jQuery);


