// -*- tab-width:2; indent-tabs-mode:nil -*-

MG_GAME_GUESSWHAT = function ($) {
  return $.extend(MG_GAME_API, {
    wordField : null,
    doQueryMessages : false,
    doPartnerSearch : true, 
    
    init : function (options) {
      var settings = $.extend(options, {
        ongameinit: MG_GAME_GUESSWHAT.ongameinit,
        onunload: function() {
          // we can't make use of onunload to send an ajax request as the browser immidiatly stops working and the request might not be processed. 
          // thus if we are playing a multiplayer game the browsers will exit without error message
          if (MG_GAME_GUESSWHAT.game.game_partner_id !== undefined && MG_GAME_GUESSWHAT.game.game_partner_id && !MG_GAME_GUESSWHAT.game.played_against_computer) {
            if (MG_GAME_GUESSWHAT.game.game_partner_name == "Anonymous") {
              MG_API.ajaxCall('/games/abortPartnerSearch/game_partner_id/' + MG_GAME_GUESSWHAT.game.game_partner_id, function(response) {}, {async:false}, true); // we have to send a synchronous request as a async request might be aborted by page unload
            } else {    
              if (MG_GAME_GUESSWHAT.game.played_game_id !== undefined && MG_GAME_GUESSWHAT.game.played_game_id && !MG_GAME_GUESSWHAT.game.played_against_computer) {
                MG_API.ajaxCall('/games/abort/played_game_id/' + MG_GAME_GUESSWHAT.game.played_game_id, function(response) {}, {async:false}, true); // we have to send a synchronous request as a async request might be aborted by page unload  
              }
            }
          } else {
            return 'Quit ' + MG_GAME_API.game.name + '?';
          }
        }
      });

      MG_GAME_GUESSWHAT.wordField = $("#tag");
      
      // submit on enter
      MG_GAME_GUESSWHAT.wordField.focus().keydown(function(event) {
        if(event.keyCode == 13) {
          MG_GAME_GUESSWHAT.onSendHint(); 
          return false;
        }
      });
      
      MG_GAME_GUESSWHAT.submitHintButton = $("#sendHint").click(MG_GAME_GUESSWHAT.onSendHint);
      
      MG_GAME_API.game_init(settings);
      
      MG_AUDIO.init({
        swfPath: MG_GAME_GUESSWHAT.settings.base_url + "/js/jQuery.jPlayer"
      });
      
      MG_AUDIO.add("fail", { 
          m4a: MG_GAME_GUESSWHAT.settings.asset_url + "/audio/fail.oga", 
          mp3: MG_GAME_GUESSWHAT.settings.asset_url + "/audio/fail.mp3", 
          wav: MG_GAME_GUESSWHAT.settings.asset_url + "/audio/fail.wav"}, 
          {supplied : 'oga, mp3, wav'});
      MG_AUDIO.add("success", { 
          m4a: MG_GAME_GUESSWHAT.settings.asset_url + "/audio/success.oga", 
          mp3: MG_GAME_GUESSWHAT.settings.asset_url + "/audio/success.mp3", 
          wav: MG_GAME_GUESSWHAT.settings.asset_url + "/audio/success.wav"}, 
          {supplied : 'oga, mp3, wav'});
      MG_AUDIO.add("select", { 
          m4a: MG_GAME_GUESSWHAT.settings.asset_url + "/audio/select.oga", 
          mp3: MG_GAME_GUESSWHAT.settings.asset_url + "/audio/select.mp3", 
          wav: MG_GAME_GUESSWHAT.settings.asset_url + "/audio/select.wav"}, 
          {supplied : 'oga, mp3, wav'});
      MG_AUDIO.add("hint", { 
          m4a: MG_GAME_GUESSWHAT.settings.asset_url + "/audio/hint.oga", 
          mp3: MG_GAME_GUESSWHAT.settings.asset_url + "/audio/hint.mp3", 
          wav: MG_GAME_GUESSWHAT.settings.asset_url + "/audio/hint.wav"}, 
          {supplied : 'oga, mp3, wav'});
    },
    
    queryMessages : function () {
      if (MG_GAME_GUESSWHAT.doQueryMessages && !MG_GAME_GUESSWHAT.game.played_against_computer) {
        MG_API.ajaxCall('/games/messages/played_game_id/' + MG_GAME_GUESSWHAT.game.played_game_id , function (response) {
          if (MG_API.checkResponse(response)) { // we have to check whether the API returned a HTTP Status 200 but still json.status == "error" response
            if (MG_GAME_GUESSWHAT.doQueryMessages) {
              if (response.messages !== undefined && response.messages.length > 0) {
                for (index in response.messages) {
                  message = response.messages[index].message;
                  
                  code = message;
                  
                  try {
                    var message = $.parseJSON(message); // jquery seems not to like to parse all strings xyz without ' throws an error
                    if (message.code != undefined)
                      code = message.code;
                  } catch (err) {}
                  
                  switch (code) {
                    case "aborted":
                      MG_GAME_API.releaseOnBeforeUnload(); // make sure the user can navigate away without seeing the leaving confirmation
                      MG_GAME_GUESSWHAT.busy = true;
                      MG_GAME_GUESSWHAT.doQueryMessages = false;
                      
                      MG_GAME_API.curtain.show();
                      $("#info-modal").html("");
                      $("#template-info-modal-partner-aborted").tmpl({
                        game_partner_name: MG_GAME_API.game.game_partner_name,
                        game_base_url: MG_GAME_API.game.game_base_url,
                        arcade_url: MG_GAME_API.game.arcade_url
                      }).appendTo($("#info-modal"));
                      $("#info-modal:hidden").fadeIn(500);
                      break;
                      
                    case "waiting":
                      $("#partner-waiting").html("");
                      $("#template-waiting-for-guess").tmpl({game_partner_name: MG_GAME_API.game.game_partner_name}).appendTo($("#partner-waiting"));
                      $("#partner-waiting").fadeIn(2500);
                      break;
                    
                    case "hintrequest":
                      $("#info-modal:visible").fadeOut(500);
                      MG_GAME_API.curtain.hide();
                      MG_GAME_GUESSWHAT.busy = false;
                    
                      $("#partner-waiting").html("");
                      $("#template-waiting-for-hint").tmpl({game_partner_name: MG_GAME_API.game.game_partner_name}).appendTo($("#partner-waiting"));
                      $("#partner-waiting").fadeIn(500);
                      break;
                    
                    case "failed":
                      $("#info-modal:visible").fadeOut(500);
                      MG_GAME_GUESSWHAT.busy = false;
                      MG_GAME_GUESSWHAT.onsubmit(); 
                      break;
                    
                    case "posted":
                      $("#info-modal:visible").fadeOut(500);
                      MG_GAME_GUESSWHAT.busy = false;
                      MG_GAME_GUESSWHAT.onsubmitTurn(); 
                      break;
                      
                    case "guess":
                      $("#info-modal:visible").fadeOut(500);
                      MG_GAME_GUESSWHAT.busy = false;
                      MG_AUDIO.play("select");
                      MG_GAME_GUESSWHAT.evaluateGuess(message.guessedImageId); 
                      break;
                    
                    case "hint":
                      MG_GAME_GUESSWHAT.processHint(message.hint);
                      break;
                  }
                }
              }
            }
          }
        }, {}, true);
        setTimeout(MG_GAME_GUESSWHAT.queryMessages, MG_GAME_API.settings.message_queue_interval);  
      }
    },
    
    renderGuessTurn : function (response, score_info, turn_info, licence_info, more_info) {
      $("#stage").hide();
      
      if (MG_GAME_API.game.number_hints > 0) { 
        $("#requestHintContainer").html('').show();
        $("#template-request-hint-active").tmpl().appendTo($("#requestHintContainer"));
        $("#requestHint").click(MG_GAME_GUESSWHAT.onRequestHint);  
      } else {
        $("#requestHintContainer").hide();
      }
      
      $("#scores").html(""); 
      $("#template-scores").tmpl(score_info ).appendTo($("#scores"));

      if (!MG_GAME_GUESSWHAT.game.user_authenticated) {
        $("#scores .total_score").remove();
      }
      
      $("#game .describe").hide();
      $('#wrong-guesses').hide();
      $('#wrong-guesses > div').remove();
      $("#game .guess").show();
      $("#game .guess .images").html("");
      $("#game .guess .hints span").remove();
      $("#template-guess-image").tmpl(turn_info).appendTo($("#game .guess .images"));
      
      $("#licences").html("");
      $("#template-licence").tmpl(licence_info).appendTo($("#licences"));
      
      $("#more_info").html("");
      
      if (more_info.length > 0)
        $("#template-more-info").tmpl(more_info).appendTo($("#more_info"));
      
      $('a.guessthis').click(MG_GAME_GUESSWHAT.onguess);
      $("a[rel='zoom']").fancybox({overlayColor: '#000'});
      
      $("#stage").fadeIn(1000, function () {MG_GAME_GUESSWHAT.busy = false;MG_GAME_GUESSWHAT.wordField.focus();});
    },
    
    renderDescribeTurn : function (response, score_info, turn_info, licence_info, more_info, words_to_avoid) {
      $("#stage").hide();
      
      $("#scores").html(""); 
      $("#template-scores").tmpl(score_info ).appendTo($("#scores"));
      if (!MG_GAME_GUESSWHAT.game.user_authenticated) {
        $("#scores .total_score").remove();
      }
      $("#requestHintContainer").hide();
      
      $("#game .guess").hide();
      $('#wrong-guesses').show();
      $('#wrong-guesses > div').remove();
      $("#game .describe").show();
      $("#game .describe .image").html("");
      $("#game .describe .hints span").remove();
      $("#template-describe-image").tmpl(turn_info).appendTo($("#game .describe .image"));
      
      $("#licences").html("");
      $("#template-licence").tmpl(licence_info).appendTo($("#licences"));
      
      $("#more_info").html("");
      
      if (more_info.length > 0)
        $("#template-more-info").tmpl(more_info).appendTo($("#more_info"));
      
      $("#words_to_avoid").html("");
      $("#template-words-to-avoid-heading").tmpl().appendTo($("#words_to_avoid"))
      $("#template-words-to-avoid").tmpl(words_to_avoid).appendTo($("#words_to_avoid"));
     
      $("a[rel='zoom']").fancybox({overlayColor: '#000'});
      
      $("#stage").fadeIn(1000, function () {MG_GAME_GUESSWHAT.busy = false;MG_GAME_GUESSWHAT.wordField.focus();});
    },
    
    renderFinal : function (response, score_info, turn_info, licence_info, more_info) {
      $("#stage").hide();
      
      $('.game_description').hide();
      $('#partner-waiting').remove();
      $("#messages").hide(); 
      $('#game').hide();
      
      $("#scores").addClass("final").html(""); 
      $("#template-final-scoring").tmpl(score_info).appendTo($("#scores"));
      
      $("#more_info").html("");
      if (more_info.length > 0)
        $("#template-more-info").tmpl(more_info).appendTo($("#more_info"));
      
      $("#words_to_avoid").html("");
      
      $('#finalScreen').show();
      $("#template-final-screen-turn-image").tmpl(turn_info).appendTo($("#finalScreen"));
      
      $("a[rel='zoom']").fancybox({overlayColor: '#000'});
      
      MG_GAME_API.releaseOnBeforeUnload(); // make sure the user can navigate away without seeing the leaving confirmation
      
      $("#stage").fadeIn(1000, function () {MG_GAME_GUESSWHAT.busy = false;});
    },

      // Update the 'score' section using the current values stored in
      // MG_GAME_GUESSWHAT.
      //
      // We copied-out this section so that we could call it from
      // inside the hint code. We need to call it in a couple of
      // places, so it would be ideal to refactor those locations to
      // make this shared code.
      updateScore : function () {
        var current_turn = MG_GAME_GUESSWHAT.turns[MG_GAME_GUESSWHAT.turn-1];
        
        var score_info = {
          user_name : MG_GAME_GUESSWHAT.game.user_name,
          game_partner_name : MG_GAME_GUESSWHAT.game.game_partner_name,
          user_score : MG_GAME_GUESSWHAT.game.user_score,
          current_score : current_turn.score,
          user_num_played : MG_GAME_GUESSWHAT.game.user_num_played,
          turns : MG_GAME_GUESSWHAT.game.turns,
          current_turn : MG_GAME_GUESSWHAT.turn,
          guess: current_turn.guesses.length,
          max_guesses : MG_GAME_GUESSWHAT.game.number_guesses,
          num_guesses_left : MG_GAME_GUESSWHAT.game.number_guesses - current_turn.guesses.length,
          max_hints : MG_GAME_GUESSWHAT.game.number_hints,
          num_hints_left : MG_GAME_GUESSWHAT.game.number_hints - current_turn.hints.length
        };
        
        $("#scores").html(""); 
        $("#template-scores").tmpl(score_info ).appendTo($("#scores"));
        if (!MG_GAME_GUESSWHAT.game.user_authenticated) {
          $("#scores .total_score").remove();
        }
      },
    
    onresponse : function (response) {
      MG_GAME_GUESSWHAT.doQueryMessages = false;
      
      if (response.status == "wait") {
        MG_GAME_GUESSWHAT.doQueryMessages = true;
        MG_GAME_GUESSWHAT.queryMessages();
        
        MG_GAME_API.curtain.show();
        $("#info-modal").html('');
        $("#template-info-modal-wait-for-partner-to-submit").tmpl({
          game_partner_name: MG_GAME_API.game.game_partner_name,
          game_base_url: MG_GAME_API.game.game_base_url,
          arcade_url: MG_GAME_API.game.arcade_url
        }).appendTo($("#info-modal"));
        $("#info-modal:hidden").fadeIn(250);

      } else if (response.status == "retry") {
        // no partner available
        $("#info-modal").html("");
        $("#template-info-modal-wait-for-partner").tmpl({
          seconds: Math.round(MG_GAME_API.settings.partner_wait_threshold - MG_GAME_API.settings.partner_waiting_time),
          play_against_computer: MG_GAME_GUESSWHAT.game.play_against_computer,
          arcade_url: MG_GAME_API.game.arcade_url
          }).appendTo($("#info-modal"));
        $("#info-modal:hidden").fadeIn(500);
        
        $('#playAgainstComputerNow').click(function () { 
          // hide the waiting window and make a final game partner search call with attempt == MG_GAME_API.settings.partner_wait_threshold
          // this will trigger the play against the computer mode 
          $("#info-modal").fadeOut(250);
          MG_GAME_GUESSWHAT.doPartnerSearch = false;
          
          MG_API.waitForThrottleIntervalToPass(function () {
            MG_API.ajaxCall('/games/play/gid/' + MG_GAME_API.settings.gid + '/a/' + MG_GAME_API.settings.partner_wait_threshold + '/gp/' + MG_GAME_GUESSWHAT.game.game_partner_id , function(response) {
              if (MG_API.checkResponse(response)) {
                MG_GAME_API.game = $.extend(MG_GAME_API.game, response.game);
                MG_GAME_API.settings.ongameinit(response);
              }
            }); 
          }, 1000);
          return false;
        });
        
        // wait for throttle interval to pass and check if a partner came online
        MG_API.waitForThrottleIntervalToPass(function () {
          if (MG_GAME_GUESSWHAT.doPartnerSearch) {
            var interval = MG_GAME_API.settings.throttleInterval;
            if (interval < 1000)
              interval = 1000;
              
            MG_GAME_API.settings.partner_waiting_time += (interval/1000);
            if (MG_GAME_API.settings.partner_waiting_time <= MG_GAME_API.settings.partner_wait_threshold) {
              MG_API.ajaxCall('/games/play/gid/' + MG_GAME_API.settings.gid + '/a/' + MG_GAME_API.settings.partner_waiting_time + '/gp/' + MG_GAME_GUESSWHAT.game.game_partner_id , function(response) {
                if (MG_API.checkResponse(response)) {
                  MG_GAME_API.game = $.extend(MG_GAME_API.game, response.game);
                  MG_GAME_API.settings.ongameinit(response);
                }
              }); 
            } else {
              MG_GAME_API.releaseOnBeforeUnload(); // make sure the user can navigate away without seeing the leaving confirmation
              $("#info-modal").html("");
              $("#template-info-modal-time-out").tmpl({
                game_base_url: MG_GAME_API.game.game_base_url,
                arcade_url: MG_GAME_API.game.arcade_url
              }).appendTo($("#info-modal"));
            }
          }
        }, 1000);
      } else if (response.status = 'ok') {
        $('#debug span').html('<br/>GAME PARTNER NAME:' + MG_GAME_GUESSWHAT.game.game_partner_name + '<br/>PLAYED GAME ID:' + MG_GAME_GUESSWHAT.game.played_game_id); // xxx remove
        
        MG_GAME_GUESSWHAT.wordField.val("");
        
        $("#info-modal").hide();
        MG_GAME_API.curtain.hide();
      
        MG_GAME_GUESSWHAT.turn++;
        MG_GAME_GUESSWHAT.turns.push(response.turn);
        MG_GAME_GUESSWHAT.turns[MG_GAME_GUESSWHAT.turn-1].hints = [];
        MG_GAME_GUESSWHAT.turns[MG_GAME_GUESSWHAT.turn-1].guesses = [];

        var more_info = {}; 
        if ($.trim(MG_GAME_GUESSWHAT.game.more_info_url) != "")
          var more_info = {url: MG_GAME_GUESSWHAT.game.more_info_url, name: MG_GAME_GUESSWHAT.game.name};
        
        if (MG_GAME_GUESSWHAT.turn > MG_GAME_GUESSWHAT.game.turns) { // render final result
          
          var licence_info = [];  
          var turn_info = [];
          var prev_turn_score = 0;
          if (MG_GAME_GUESSWHAT.turns.length) { // extract turn and licence info
            for (i=0; i < MG_GAME_GUESSWHAT.turns.length - 1; i++) {
              var turn = MG_GAME_GUESSWHAT.turns[i];
              
              var secret_image = turn.images.describe;
              
              var score = MG_GAME_GUESSWHAT.turns[i+1].score;
              if (i > 0) {
                score -= MG_GAME_GUESSWHAT.turns[i].score;
              }
              
              // get turn's describe image info
              image_licence_info = MG_GAME_GUESSWHAT.extractImageLicenceInfo(turn.licences, secret_image);
              var image_info = {
                url : secret_image.guess,
                url_full_size : secret_image.full_size,
                licence_info : MG_GAME_API.parseLicenceInfo(image_licence_info),
                num_guesses : turn.guesses.length,
                num_hints : turn.hints.length,
                hints : turn.hints.join(', '),
                num_points : score
              }
              turn_info.push(image_info);
              
              if (turn.licences.length) {
                for (licence in turn.licences) { // licences
                  var found = false;
                  for (l_index in licence_info) {
                    if (licence_info[l_index].id == turn.licences[licence].id) { 
                      found = true;
                      break;
                    }
                  }
                  
                  if (!found)
                    licence_info.push(turn.licences[licence]);
                }  
              }
            }
          }
          
          //score box
          var score_info = {
            user_name : MG_GAME_GUESSWHAT.game.user_name,
            game_partner_name : MG_GAME_GUESSWHAT.game.game_partner_name,
            user_score : MG_GAME_GUESSWHAT.game.user_score,
            current_score : response.turn.score,
            user_num_played : MG_GAME_GUESSWHAT.game.user_num_played,
            turns : MG_GAME_GUESSWHAT.game.turns,
            current_turn : MG_GAME_GUESSWHAT.turn,
            game_base_url: MG_GAME_API.game.game_base_url,
          };
          
          MG_GAME_GUESSWHAT.renderFinal(response, score_info, turn_info, licence_info, more_info);
          
        } else {
          
          $("#words_to_avoid").hide();
          var words_to_avoid = []
          
          //score box
          var score_info = {
            user_name : MG_GAME_GUESSWHAT.game.user_name,
            game_partner_name : MG_GAME_GUESSWHAT.game.game_partner_name,
            user_score : MG_GAME_GUESSWHAT.game.user_score,
            current_score : response.turn.score,
            user_num_played : MG_GAME_GUESSWHAT.game.user_num_played,
            turns : MG_GAME_GUESSWHAT.game.turns,
            current_turn : MG_GAME_GUESSWHAT.turn,
            guess: 0,
            guesses : MG_GAME_GUESSWHAT.game.number_guesses,
            num_guesses_left : MG_GAME_GUESSWHAT.game.number_guesses,
            num_hints_left : MG_GAME_GUESSWHAT.game.number_hints
            //max_guesses : MG_GAME_GUESSWHAT.game.number_guesses,
            //num_guesses_left : MG_GAME_GUESSWHAT.game.number_guesses - current_turn.guesses.length,
            //max_hints : MG_GAME_GUESSWHAT.game.number_hints,
            //num_hints_left : MG_GAME_GUESSWHAT.game.number_hints - current_turn.hints.length
          };
          
          // find out in what mode we are this can't be only done for the first turn on the server 
          var mode = response.turn.mode;
          if (MG_GAME_GUESSWHAT.game.played_against_computer) {
            MG_GAME_GUESSWHAT.turns[MG_GAME_GUESSWHAT.turn-1].mode = "guess";
          }
          if (MG_GAME_GUESSWHAT.turn > 1 && !MG_GAME_GUESSWHAT.game.played_against_computer) {
            if (MG_GAME_GUESSWHAT.turns[0].mode == "describe") {
              // user started with the describe screen;
              if (MG_GAME_GUESSWHAT.turn % 2 == 0) {
                MG_GAME_GUESSWHAT.turns[MG_GAME_GUESSWHAT.turn-1].mode = "guess";
              } else {
                MG_GAME_GUESSWHAT.turns[MG_GAME_GUESSWHAT.turn-1].mode = "describe";
              } 
            } else {
              if (MG_GAME_GUESSWHAT.turn % 2 == 0) {
                MG_GAME_GUESSWHAT.turns[MG_GAME_GUESSWHAT.turn-1].mode = "describe";
              } else {
                MG_GAME_GUESSWHAT.turns[MG_GAME_GUESSWHAT.turn-1].mode = "guess";
              }
            }
          }
          
          if (MG_GAME_GUESSWHAT.turns[MG_GAME_GUESSWHAT.turn-1].mode == "describe") {
            $('.game_description.describe').show();
            
            if (response.turn.wordstoavoid) {
              for (image in response.turn.wordstoavoid) {
                for (tag in response.turn.wordstoavoid[image]) {
                  words_to_avoid.push(response.turn.wordstoavoid[image][tag]);
                }
              }
              if (words_to_avoid.length) 
                $("#words_to_avoid").show();
            }
            
            if (response.turn.images && response.turn.images['describe'] !== undefined) {
              licence_info = MG_GAME_GUESSWHAT.extractImageLicenceInfo(response.turn.licences, response.turn.images['describe'])
              
              // turn info == image 
              var turn_info = {
                url : response.turn.images['describe'].scaled,
                url_full_size : response.turn.images['describe'].full_size,
                licence_info : MG_GAME_API.parseLicenceInfo(licence_info)
              }
              
              MG_GAME_GUESSWHAT.renderDescribeTurn(response, score_info, turn_info, licence_info, more_info, words_to_avoid);
            }
          } else {
            var licence_info = response.turn.licences;
            
            var turn_info = [];
            if (response.turn.images && response.turn.images['guess'] && response.turn.images['guess'].length) {
              for (i_image in response.turn.images['guess']) {
                var image =response.turn.images['guess'][i_image];
                turn_info.push({
                  image_id : image.image_id,
                  url : image.guess,
                  url_full_size : image.full_size,
                  licence_info : MG_GAME_API.parseLicenceInfo(MG_GAME_GUESSWHAT.extractImageLicenceInfo(response.turn.licences, image))
                });
              }
            }
            
            $('.game_description.guess').show();
            MG_GAME_GUESSWHAT.renderGuessTurn(response, score_info, turn_info, licence_info, more_info);
            MG_GAME_GUESSWHAT.sendHintRequest();
          }
          
          MG_GAME_GUESSWHAT.doQueryMessages = true;
          MG_GAME_GUESSWHAT.queryMessages();
        }
      }
    },

    // Are any more hints are allowed to be given?
    areHintsAllowedLeft : function () {
      var current_turn = MG_GAME_GUESSWHAT.turns[MG_GAME_GUESSWHAT.turn-1];
      return (MG_GAME_API.game.number_hints*1 > current_turn.hints.length);
    },

    // Prepare for the next hint (including blocking further hints,
    // if the appropriate conditions have been met).
    prepareForNextHint : function () {
      if (!MG_GAME_GUESSWHAT.areHintsAllowedLeft()){
        // Delete our hint-giving and hint-requesting buttons,
        // replacing them with informative statements as to their
        // disappearance.
        $("#requestHintContainer").html('');
        $("#template-request-hint-inactive").tmpl().appendTo($("#requestHintContainer"));
        //$("#sendHintFormContainer").html('');
        //$("#template-send-hint-form-inactive").tmpl().appendTo($("#sendHintFormContainer"));
      }
    },
    
    onRequestHint : function () {
      MG_GAME_GUESSWHAT.sendHintRequest();
      return false;
    },
    
    onSendHint : function () {
      if (MG_GAME_GUESSWHAT.busy) {
        return false;
      }

      var tags = $.trim(MG_GAME_GUESSWHAT.wordField.val());
      MG_GAME_GUESSWHAT.wordField.val("");
      if (tags == "") {
        // val filtered for all white spaces (trim)
        MG_GAME_GUESSWHAT.error("<h1>Ooops</h1><p>Please enter a word</p>");
        return false;
      }

      MG_GAME_GUESSWHAT.busy = true;
      MG_GAME_API.curtain.show();
      MG_GAME_API.callGameAPI('validateHint', {'hint': tags}, function (response) {
      var current_turn = MG_GAME_GUESSWHAT.turns[MG_GAME_GUESSWHAT.turn-1];

      if (!MG_API.checkResponse(response)) { 
        return false;
      }

      // The api call returns an empty string if the first
      // tag was a stop word.
      if (response.response == "") {
        MG_GAME_GUESSWHAT.error($("#template-error-hint-stop-word").tmpl());
        MG_GAME_GUESSWHAT.busy = false;
        return false;
      }

      hint_ok = true;
      if (current_turn.wordstoavoid) {
        for (image in current_turn.wordstoavoid) {
          for (tag in current_turn.wordstoavoid[image]) {
            if (current_turn.wordstoavoid[image][tag].tag.toLowerCase() ==
                response.response.toLowerCase()) {
              hint_ok = false;
              MG_GAME_GUESSWHAT.error($("#template-error-hint-word-to-avoid").tmpl());
              MG_GAME_GUESSWHAT.busy = false;
              break;
            }
          }

          if (!hint_ok) 
            break;
        }
      }
                
      if (current_turn.hints.length) {
        for (h_index in current_turn.hints) {
          if (current_turn.hints[h_index].toLowerCase() ==
              response.response.toLowerCase()) {
            hint_ok = false;
            MG_GAME_GUESSWHAT.error($("#template-error-hint-given-twice").tmpl());
            MG_GAME_GUESSWHAT.busy = false;
          }
        }
      }
               
      if (hint_ok) {
        MG_AUDIO.play("hint");
                  
        $('<span>').text(response.response).appendTo($("#game .describe .hints"));
        $("#game .describe .hints:hidden").toggle();
        MG_GAME_API.postMessage( {code:'hint','hint': response.response});
                
        current_turn.hints.push(response.response);

        // Immediately after adding a hint to the list, we need to
        // (potentially) redraw our hint interaction information so
        // that we can hide buttons if no more hints are to be given.
        MG_GAME_GUESSWHAT.prepareForNextHint();

        $("#partner-waiting").hide();
        
        MG_GAME_API.curtain.show();
                  
        $("#info-modal").html("").hide();
        $("#template-info-modal-waiting-for-guess").tmpl({
          game_partner_name: MG_GAME_API.game.game_partner_name,
          game_base_url: MG_GAME_API.game.game_base_url,
          arcade_url: MG_GAME_API.game.arcade_url
        }).appendTo($("#info-modal"));

        $("#info-modal:hidden").fadeIn(500, function () {
          MG_GAME_API.postMessage('waiting');
        });
      }

     });

      return false;      
    },
    
    processHint : function (hint) {
      var current_turn = MG_GAME_GUESSWHAT.turns[MG_GAME_GUESSWHAT.turn-1];

      $("#info-modal:visible").fadeOut(500);
      MG_GAME_API.curtain.hide();
      MG_GAME_GUESSWHAT.busy = false;
      $("#game .guess .hints").show();
      $('<span>').text(hint).appendTo($("#game .guess .hints").fadeIn(2500, function () {MG_AUDIO.play("hint");}));
      current_turn.hints.push(hint);
      
      // Update the score field on the LHS.
      MG_GAME_GUESSWHAT.updateScore();
        
      // Prepare for the next hint.
      MG_GAME_GUESSWHAT.prepareForNextHint();
    },
    
    onsubmitTurn : function () {
      if (!MG_GAME_GUESSWHAT.busy) {
        $("#partner-waiting").hide();
        
        MG_GAME_API.curtain.show();
        MG_GAME_GUESSWHAT.busy = true;
        
        MG_API.waitForThrottleIntervalToPass(function () {
          var tags = "";
          
          if (MG_GAME_GUESSWHAT.turns[MG_GAME_GUESSWHAT.turn-1].mode == 'describe')
            tags = MG_GAME_GUESSWHAT.turns[MG_GAME_GUESSWHAT.turn-1].hints;
          
          MG_API.ajaxCall('/games/play/gid/' + MG_GAME_API.settings.gid , function(response) {
            if (MG_API.checkResponse(response)) {
              MG_GAME_GUESSWHAT.onresponse(response);
            }
          }, {
            type:'post',
            data: {
              turn : MG_GAME_GUESSWHAT.turn,
              wordstoavoid : MG_GAME_GUESSWHAT.turns[MG_GAME_GUESSWHAT.turn-1].wordstoavoid,
              played_game_id : MG_GAME_GUESSWHAT.game.played_game_id,
              submissions: [{
                mode : MG_GAME_GUESSWHAT.turns[MG_GAME_GUESSWHAT.turn-1].mode,
                hints : MG_GAME_GUESSWHAT.turns[MG_GAME_GUESSWHAT.turn-1].hints,
                guesses : MG_GAME_GUESSWHAT.turns[MG_GAME_GUESSWHAT.turn-1].guesses,
                image_id : MG_GAME_GUESSWHAT.turns[MG_GAME_GUESSWHAT.turn-1].images.describe.image_id,
                tags: tags
              }]
            }
          });
        });
      }
      return false;
    },
    
    onguess : function (event) {
      var link = $(this);
      var id = link.attr('id');
      
      event.preventDefault();
      
      if (id.length > 9) {
        var guessedImageId = id.substring(id.indexOf('guess-me-') + 9);
        if (!MG_GAME_GUESSWHAT.game.played_against_computer) {
          MG_GAME_API.postMessage( {code:'guess','guessedImageId': guessedImageId});
        }
        MG_AUDIO.play("select");
        MG_GAME_API.curtain.show();
        $("#partner-waiting").hide();
        setTimeout(function () {
          MG_GAME_GUESSWHAT.evaluateGuess(guessedImageId);  
        }, 1000);
      }
      
      return false;
    },
    
    ongameinit : function (response) {
      MG_GAME_GUESSWHAT.onresponse(response);
    },
    
    extractImageLicenceInfo : function (licences, image) {
      var licence_info = [];
      if (licences.length) { // reduce the licence info only on the licences of the displayed image
        for (i_licence_turn in licences) {
          var licence_id = licences[i_licence_turn]['id'];
          for (i_licence_image in image.licences) { //scores
            if (image.licences[i_licence_image] == licence_id) {
              licence_info.push(licences[i_licence_turn]);
            }
          }
        }
      }
      return licence_info;
    },
    
    evaluateGuess : function (guessedImageID) {
      MG_GAME_GUESSWHAT.turns[MG_GAME_GUESSWHAT.turn-1].guesses.push(guessedImageID);
      
      $("#partner-waiting").hide();
      
      var current_turn = MG_GAME_GUESSWHAT.turns[MG_GAME_GUESSWHAT.turn-1];
      var secret_image = current_turn.images.describe;
      
      MG_GAME_GUESSWHAT.turns[MG_GAME_GUESSWHAT.turn-1].images.describe.image_id
      if (current_turn.guesses.length >= MG_GAME_GUESSWHAT.game.number_guesses) {
        if (secret_image.image_id == guessedImageID) { // correct guess at last guess
          MG_AUDIO.play("success");
          MG_GAME_GUESSWHAT.onsubmitTurn();
          
        } else { // failed to guess within the allowed number of guesses show correct solution in popup 
          MG_AUDIO.play("fail");
          if (current_turn.mode == "describe") {
            licence_info = MG_GAME_GUESSWHAT.extractImageLicenceInfo(current_turn.licences, secret_image);
            var image_info = {
              game_partner_name : MG_GAME_GUESSWHAT.game.game_partner_name,
            }
            
            MG_API.popup( $("#template-partner-failed-to-guess").tmpl(image_info), 
                              { modal: true, 
                                onComplete: function () {
                                  $('#loadNextTurn').click(function () {
                                    $.fancybox.close();
                                    MG_GAME_GUESSWHAT.onsubmitTurn();
                                  });
                                }});
          } else {
            licence_info = MG_GAME_GUESSWHAT.extractImageLicenceInfo(current_turn.licences, secret_image);
            var image_info = {
              url : secret_image.scaled,
              url_full_size : secret_image.full_size,
              licence_info : MG_GAME_API.parseLicenceInfo(licence_info)
            }
            
            MG_API.popup( $("#template-failed-to-guess").tmpl(image_info), 
                              { modal: true, 
                                onComplete: function () {
                                  $('#loadNextTurn').click(function () {
                                    $.fancybox.close();
                                    MG_GAME_GUESSWHAT.onsubmitTurn();
                                  });
                                }});
          }
          
        }
      } else {
        MG_GAME_GUESSWHAT.updateScore();
        
        if (current_turn.mode == "describe") {
          if (secret_image.image_id == guessedImageID) { // the partner has found the right image
            MG_AUDIO.play("success");
            MG_GAME_GUESSWHAT.onsubmitTurn();
             
          } else {
            MG_AUDIO.play("fail");
            if (current_turn.images && current_turn.images['guess'] && current_turn.images['guess'].length) {
              for (i_image in current_turn.images['guess']) {
                var image = current_turn.images['guess'][i_image];
                if (image.image_id == guessedImageID) {
                  image_info = {
                    image_id : image.image_id,
                    url : image.guess,
                    url_full_size : image.full_size,
                    licence_info : MG_GAME_API.parseLicenceInfo(MG_GAME_GUESSWHAT.extractImageLicenceInfo(current_turn.licences, image))
                  };
                  $("#template-wrong-guess-image").tmpl(image_info).appendTo($("#wrong-guesses"));
                  $("a[rel='zoom']").fancybox({overlayColor: '#000'});
                  $("#wrong-guesses").show();
                  break;
                }
              }
            }
          }
          MG_GAME_API.curtain.hide();
          $("#partner-waiting").html("");
          $("#template-wrong-guess-waiting-for-guess").tmpl({game_partner_name: MG_GAME_API.game.game_partner_name}).appendTo($("#partner-waiting"));
          $("#partner-waiting").fadeIn(500);
        } else {
          if (secret_image.image_id == guessedImageID) { // the player has found the right image
            MG_AUDIO.play("success");
            MG_GAME_GUESSWHAT.onsubmitTurn();
          } else { // the player has clicked the wrong image
            MG_AUDIO.play("fail");
            $('#guess-me-' + guessedImageID).unbind('click').click(function () {return false;}).parent().addClass("wrong");
            
            if (MG_GAME_GUESSWHAT.game.played_against_computer) {
              MG_GAME_API.curtain.show();
              setTimeout(function () {
                MG_GAME_API.curtain.hide();
                MG_GAME_GUESSWHAT.sendHintRequest()
              }, 500);
            } else {
              // So what happens if there are no more hints to show?
              // At this point we should only go into 'waiting for
              // hint' mode if there are more hints for
              // us. Otherwise, we should just stay in guessing
              // mode.
                if(MG_GAME_GUESSWHAT.areHintsAllowedLeft) {
                MG_GAME_API.curtain.show();
                $("#info-modal").html("");
                $("#template-info-modal-wrong-guess-waiting-for-hint").tmpl({
                  game_partner_name: MG_GAME_API.game.game_partner_name,
                  game_base_url: MG_GAME_API.game.game_base_url,
                  arcade_url: MG_GAME_API.game.arcade_url
                }).appendTo($("#info-modal"));
                $("#info-modal").show();
              } else {
                // If there are no hints left, then we do not use the
                // curtain. We should only make a note in the
                // #info-modal box that the user needs to keep on
                // guessing.
                $("#partner-waiting").html("Make another guess!");
                $("#partner-waiting").show();
              }
            }   
          }
        }
      }
    },
    
    sendHintRequest : function() {
      var current_turn = MG_GAME_GUESSWHAT.turns[MG_GAME_GUESSWHAT.turn-1];

      // If there are no more hints, we terminate.
      if(!MG_GAME_GUESSWHAT.areHintsAllowedLeft()) {
        return 0;
      }

      MG_GAME_GUESSWHAT.busy = true;
      
      $("#partner-waiting").hide();
      
      MG_GAME_API.curtain.show();
      
      if (MG_GAME_GUESSWHAT.game.played_against_computer) {
        
        if (current_turn.images.describe.available_hints === undefined) {
          current_turn.images.describe.available_hints = [];
          for (var hint in current_turn.images.describe.hints) {
            current_turn.images.describe.available_hints.push(current_turn.images.describe.hints[hint]);
          }
        }
        var secret_image = current_turn.images.describe;
        var next_hint = "";
        
        if (secret_image.available_hints.length > 0) {
          pos = MG_GAME_GUESSWHAT.getRandomInt(0, secret_image.available_hints.length - 1);
          next_hint = secret_image.available_hints[pos].tag;
          current_turn.images.describe.available_hints.splice(pos,1);
        } 
        if (next_hint != "") {
          MG_GAME_GUESSWHAT.processHint(next_hint);
        } else {
          MG_GAME_GUESSWHAT.onsubmitTurn();
        }
      } else {
        if(MG_GAME_GUESSWHAT.areHintsAllowedLeft) {
          $("#info-modal").html("").hide();
          $("#template-info-modal-waiting-for-hint").tmpl({
            game_partner_name: MG_GAME_API.game.game_partner_name,
            game_base_url: MG_GAME_API.game.game_base_url,
            arcade_url: MG_GAME_API.game.arcade_url
          }).appendTo($("#info-modal"));
          $("#info-modal:hidden").fadeIn(500);
          MG_GAME_API.postMessage('hintrequest');

          // Prepare for the next hint.
          MG_GAME_GUESSWHAT.prepareForNextHint();
        } else {
          // If there are no more hints, we need to lift the curtain
          // and prod the player to make another guess.
          MG_GAME_API.curtain.hide();

          $("#partner-waiting").html("Make another guess!");
          $("#partner-waiting").show();

          // Question: Do we need to make sure that the other player
          // (the "hint-giver") is getting updates about the # of
          // guesses left, etc? I'd assume that such information is
          // getting propagated earlier, when this player (the
          // "picker") initially chooses an image, but we might need
          // to check.
        }
      }
    },
    
    getRandomInt : function (min, max) {
      return Math.floor(Math.random() * (max - min + 1)) + min;
    }
  });
}(jQuery);

