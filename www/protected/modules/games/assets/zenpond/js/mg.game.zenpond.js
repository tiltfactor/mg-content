MG_GAME_ZENPOND = function ($) {
  return $.extend(MG_GAME_API, {
    wordField : null,
    submitButton : null,
    doQueryMessages : false,
    doPartnerSearch : true,
    
    init : function (options) {
      var settings = $.extend(options, {
        ongameinit: MG_GAME_ZENPOND.ongameinit,
        onunload: function() {
          // we can't make use of onunload to send an ajax request as the browser immidiatly stops working and the request might not be processed. 
          // thus if we are playing a multiplayer game the browsers will exit without error message
          if (MG_GAME_ZENPOND.game.game_partner_id !== undefined && MG_GAME_ZENPOND.game.game_partner_id && !MG_GAME_ZENPOND.game.played_against_computer) {
            if (MG_GAME_ZENPOND.game.game_partner_name == "Anonymous") {
              MG_API.ajaxCall('/games/abortPartnerSearch/game_partner_id/' + MG_GAME_ZENPOND.game.game_partner_id, function(response) {}, {async:false}, true); // we have to send a synchronous request as a async request might be aborted by page unload
            } else {    
              if (MG_GAME_ZENPOND.game.played_game_id !== undefined && MG_GAME_ZENPOND.game.played_game_id && !MG_GAME_ZENPOND.game.played_against_computer) {
                MG_API.ajaxCall('/games/abort/played_game_id/' + MG_GAME_ZENPOND.game.played_game_id, function(response) {}, {async:false}, true); // we have to send a synchronous request as a async request might be aborted by page unload  
              }
            }
          } else {
            return 'Quit ' + MG_GAME_API.game.name + '?';
          }
        }
      });
      MG_GAME_ZENPOND.wordField = $("#words");
      
      // submit on enter
      MG_GAME_ZENPOND.wordField.focus().keydown(function(event) {
        if(event.keyCode == 13) {
          MG_GAME_ZENPOND.onsubmit(); 
          return false;
        }
      });
      
      MG_GAME_ZENPOND.submitButton = $("#button-play").click(MG_GAME_ZENPOND.onsubmit);
      
      MG_GAME_API.game_init(settings);
      
    },
    
    queryMessages : function () {
      if (MG_GAME_ZENPOND.doQueryMessages && !MG_GAME_ZENPOND.game.played_against_computer) {
        MG_API.ajaxCall('/games/messages/played_game_id/' + MG_GAME_ZENPOND.game.played_game_id , function (response) {
          if (MG_API.checkResponse(response)) { // we have to check whether the API returned a HTTP Status 200 but still json.status == "error" response
            if (MG_GAME_ZENPOND.doQueryMessages) {
              if (response.messages !== undefined && response.messages.length > 0) {
                for (index in response.messages) {
                  message = response.messages[index].message;
                  switch (message) {
                    case "aborted":
                      MG_GAME_API.releaseOnBeforeUnload(); // make sure the user can navigate away without seeing the leaving confirmation
                      MG_GAME_ZENPOND.busy = true;
                      MG_GAME_ZENPOND.doQueryMessages = false;
                      
                      MG_GAME_API.curtain.show();
                      $("#partner-waiting-modal").html("");
                      $("#template-partner-aborted").tmpl({
                        game_partner_name: MG_GAME_API.game.game_partner_name,
                        game_base_url: MG_GAME_API.game.game_base_url,
                        arcade_url: MG_GAME_API.game.arcade_url
                      }).appendTo($("#partner-waiting-modal"));
                      $("#partner-waiting-modal:hidden").fadeIn(500);
                      break;
                      
                    case "waiting":
                      $("#partner-waiting").html("");
                      $("#template-partner-waiting-for-submit").tmpl({game_partner_name: MG_GAME_API.game.game_partner_name}).appendTo($("#partner-waiting"));
                      $("#partner-waiting").show();
                      break;
                      
                    case "posted":
                      $("#partner-waiting-modal:visible").fadeOut(500);
                      MG_GAME_ZENPOND.busy = false;
                      MG_GAME_ZENPOND.onsubmit(); 
                      break;
                  }
                }
              }
            }
          }
        }, {}, true);
        setTimeout(MG_GAME_ZENPOND.queryMessages, MG_GAME_API.settings.message_queue_interval);  
      }
    },
    
    submit : function () {
      MG_API.ajaxCall('/games/play/gid/' + MG_GAME_API.settings.gid , function(response) {
        if (MG_API.checkResponse(response)) { // we have to check whether the API returned a HTTP Status 200 but still json.status == "error" response
          MG_GAME_API.game = $.extend(MG_GAME_API.game, response.game);
          MG_GAME_API.settings.ongameinit(response);
        }
      });
    },
    
    renderTurn : function (response, score_info, turn_info, licence_info, more_info, words_to_avoid) {
      $("#stage").hide();
      
      $("#scores").html(""); 
      $("#template-scores").tmpl(score_info ).appendTo($("#scores"));
      if (!MG_GAME_ZENPOND.game.user_authenticated) {
        $("#scores .total_score").remove();
      }
      
      $("#image_container").html("");
      $("#template-turn").tmpl(turn_info).appendTo($("#image_container"));
      
      $("#licences").html("");
      $("#template-licence").tmpl(licence_info).appendTo($("#licences"));
      
      $("#more_info").html("");
      
      if (more_info.length > 0)
        $("#template-more-info").tmpl(more_info).appendTo($("#more_info"));
      
      $("#words_to_avoid").html("");
      $("#template-words-to-avoid-heading").tmpl().appendTo($("#words_to_avoid"))
      $("#template-words-to-avoid").tmpl(words_to_avoid).appendTo($("#words_to_avoid"));
      
      $("a[rel='zoom']").fancybox({overlayColor: '#000'});
      
      $("#stage").fadeIn(1000, function () {MG_GAME_ZENPOND.busy = false;MG_GAME_ZENPOND.wordField.focus();});
    },
    
    renderFinal : function (response, score_info, turn_info, licence_info, more_info) {
      $("#stage").hide();
      
      $('#game_description').hide();
      
      $("#scores").html(""); 
      
      $("#fieldholder").html("");
      $("#template-final-info").tmpl(score_info ).appendTo($("#fieldholder"));
      if (score_info.tags_new !== undefined && score_info.tags_new != "") 
        $("#template-final-tags-new").tmpl(score_info ).appendTo($("#fieldholder"));
        
      if (score_info.tags_matched !== undefined && score_info.tags_matched != "")
        $("#template-final-tags-matched").tmpl(score_info ).appendTo($("#fieldholder"));
      
      if (score_info.tags_same_as !== undefined && score_info.tags_same_as != "")
        $("#template-final-tags-same_as").tmpl(score_info ).appendTo($("#fieldholder"));
      
      $("#licences").html("");
      $("#template-licence").tmpl(licence_info).appendTo($("#licences"));
      
      $("#more_info").html("");
      
      if (more_info.length > 0)
        $("#template-more-info").tmpl(more_info).appendTo($("#more_info"));
      
      $("#words_to_avoid").html("");
      
      $("#image_container").html("");
      $("#template-final-summary").tmpl(turn_info).appendTo($("#image_container"));

      $("a[rel='zoom']").fancybox({overlayColor: '#000'});
      
      MG_GAME_API.releaseOnBeforeUnload(); // make sure the user can navigate away without seeing the leaving confirmation
      MG_GAME_ZENPOND.submitButton.addClass("again").unbind("click").attr("href", window.location.href);
      
      $("#stage").fadeIn(1000, function () {MG_GAME_ZENPOND.busy = false;MG_GAME_ZENPOND.wordField.focus();});
    },
    
    onresponse : function (response) {
      MG_GAME_ZENPOND.doQueryMessages = false;
      
      if (response.status == "wait") {
        MG_GAME_ZENPOND.doQueryMessages = true;
        MG_GAME_ZENPOND.queryMessages();
        
        MG_GAME_API.curtain.show();
        $("#partner-waiting-modal").html("");
        $("#template-partner-waiting-modal-turn").tmpl().appendTo($("#partner-waiting-modal"));
        $("#partner-waiting-modal:hidden").fadeIn(500);
        
      } else if (response.status == "retry") {
        // no partner available
        $("#partner-waiting-modal").html("");
        $("#template-partner-waiting-modal").tmpl({
          seconds: Math.round(MG_GAME_API.settings.partner_wait_threshold - MG_GAME_API.settings.partner_waiting_time),
          play_against_computer: MG_GAME_ZENPOND.game.play_against_computer,
          arcade_url: MG_GAME_API.game.arcade_url
          }).appendTo($("#partner-waiting-modal"));
        $("#partner-waiting-modal:hidden").fadeIn(500);
        
        $('#playAgainstComputerNow').click(function () { 
          // hide the waiting window and make a final game partner search call with attempt == MG_GAME_API.settings.partner_wait_threshold
          // this will trigger the play against the computer mode 
          $("#partner-waiting-modal").fadeOut(250);
          MG_GAME_ZENPOND.doPartnerSearch = false;
          
          MG_API.waitForThrottleIntervalToPass(function () {
            MG_API.ajaxCall('/games/play/gid/' + MG_GAME_API.settings.gid + '/a/' + MG_GAME_API.settings.partner_wait_threshold + '/gp/' + MG_GAME_ZENPOND.game.game_partner_id , function(response) {
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
          if (MG_GAME_ZENPOND.doPartnerSearch) {
            var interval = MG_GAME_API.settings.throttleInterval;
            if (interval < 1000)
              interval = 1000;
              
            MG_GAME_API.settings.partner_waiting_time += (interval/1000);
            if (MG_GAME_API.settings.partner_waiting_time <= MG_GAME_API.settings.partner_wait_threshold) {
              MG_API.ajaxCall('/games/play/gid/' + MG_GAME_API.settings.gid + '/a/' + MG_GAME_API.settings.partner_waiting_time + '/gp/' + MG_GAME_ZENPOND.game.game_partner_id , function(response) {
                if (MG_API.checkResponse(response)) {
                  MG_GAME_API.game = $.extend(MG_GAME_API.game, response.game);
                  MG_GAME_API.settings.ongameinit(response);
                }
              }); 
            } else {
              MG_GAME_API.releaseOnBeforeUnload(); // make sure the user can navigate away without seeing the leaving confirmation
              $("#partner-waiting-modal").html("");
              $("#template-partner-waiting-time-out").tmpl({
                game_base_url: MG_GAME_API.game.game_base_url,
                arcade_url: MG_GAME_API.game.arcade_url
              }).appendTo($("#partner-waiting-modal"));
            }
          }
        }, 1000);
      } else if (response.status = 'ok'){
        $('#debug span').html('<br/>GAME PARTNER NAME:' + MG_GAME_ZENPOND.game.game_partner_name + '<br/>PLAYED GAME ID:' + MG_GAME_ZENPOND.game.played_game_id); // xxx remove
        
        MG_GAME_ZENPOND.wordField.val("");
        
        $("#partner-waiting-modal").hide();
        MG_GAME_API.curtain.hide();
      
        MG_GAME_ZENPOND.turn++;
        MG_GAME_ZENPOND.turns.push(response.turn);
        
        var more_info = {}; 
        if ($.trim(MG_GAME_ZENPOND.game.more_info_url) != "")
          var more_info = {url: MG_GAME_ZENPOND.game.more_info_url, name: MG_GAME_ZENPOND.game.name};
        
        if (MG_GAME_ZENPOND.turn > MG_GAME_ZENPOND.game.turns) { // render final result
          var licence_info = [];  
            
          var taginfo = {
            'tags_new' : {
              tags : [],
              score: 0
            },
            'tags_matched' : {
              tags : [],
              score: 0
            },
            'tags_same_as' : {
              tags : []
            },
          };
          
          if (MG_GAME_ZENPOND.turns.length) { // extract scoring and licence info
            for (i_turn in MG_GAME_ZENPOND.turns) {
              var turn = MG_GAME_ZENPOND.turns[i_turn];
              for (i_img in turn.tags.user) { //scores
                var image = turn.tags.user[i_img];
                for (i_tag in image) {
                  var tag = image[i_tag];
                  switch (tag.type) {
                    case "new":
                      taginfo.tags_new.tags.push(i_tag);
                      taginfo.tags_new.score += tag.score;
                      break;
                      
                    case "match":
                      taginfo.tags_matched.tags.push(i_tag);
                      taginfo.tags_matched.score += tag.score;
                      break;  
                  }
                  if (turn.tags.opponent !== undefined && turn.tags.opponent.constructor === Object && turn.tags.opponent[i_img] !== undefined) {
                    if (i_tag in turn.tags.opponent[i_img]) {
                      taginfo.tags_same_as.tags.push(i_tag);
                    }
                  }
                }
              }
              
              for (var scope in taginfo) {
                var o_scope = taginfo[scope];
                var tmp = {};
                var tmp2 = [];
                if (o_scope.tags !== undefined && o_scope.tags.length) {
                  for(var index in o_scope.tags) {
                    if (o_scope.tags[index] in tmp) {
                      tmp[o_scope.tags[index]]++;
                    } else {
                      tmp[o_scope.tags[index]] = 1;
                    }
                  }
                  for (var tag in tmp) {
                    if (tmp[tag] > 1) {
                      tmp2.push(tag + ' (' + tmp[tag] + ')');
                    } else {
                      tmp2.push(tag);  
                    }
                  }
                  taginfo[scope].scoreinfo = tmp2.join(", ");
                }
              }
              
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
            user_name : MG_GAME_ZENPOND.game.user_name,
            game_partner_name : MG_GAME_ZENPOND.game.game_partner_name,
            user_score : MG_GAME_ZENPOND.game.user_score,
            current_score : response.turn.score,
            user_num_played : MG_GAME_ZENPOND.game.user_num_played,
            turns : MG_GAME_ZENPOND.game.turns,
            current_turn : MG_GAME_ZENPOND.turn,
            tags_new : taginfo.tags_new.scoreinfo,
            tags_new_score : taginfo.tags_new.score,
            tags_matched : taginfo.tags_matched.scoreinfo,
            tags_matched_score : taginfo.tags_matched.score,
            tags_same_as : taginfo.tags_same_as.scoreinfo,
          };
          
          // turn info == image 
          var turn_info = {
            url_1 : MG_GAME_ZENPOND.turns[0].images[0].final_screen,
            url_full_size_1 : MG_GAME_ZENPOND.turns[0].images[0].full_size,
            licence_info_1 : MG_GAME_API.parseLicenceInfo(MG_GAME_ZENPOND.turns[0].licences),
            url_2 : MG_GAME_ZENPOND.turns[1].images[0].final_screen,
            url_full_size_2 : MG_GAME_ZENPOND.turns[1].images[0].full_size,
            licence_info_2 : MG_GAME_API.parseLicenceInfo(MG_GAME_ZENPOND.turns[1].licences),
            url_3 : MG_GAME_ZENPOND.turns[2].images[0].final_screen,
            url_full_size_3 : MG_GAME_ZENPOND.turns[2].images[0].full_size,
            licence_info_3 : MG_GAME_API.parseLicenceInfo(MG_GAME_ZENPOND.turns[2].licences),
            url_4 : MG_GAME_ZENPOND.turns[3].images[0].final_screen,
            url_full_size_4 : MG_GAME_ZENPOND.turns[3].images[0].full_size,
            licence_info_4 : MG_GAME_API.parseLicenceInfo(MG_GAME_ZENPOND.turns[3].licences)
          }
          
          MG_GAME_API.renderFinal(response, score_info, turn_info, licence_info, more_info);
           
        } else {
          
          //score box
          var score_info = {
            user_name : MG_GAME_ZENPOND.game.user_name,
            game_partner_name : MG_GAME_ZENPOND.game.game_partner_name,
            user_score : MG_GAME_ZENPOND.game.user_score,
            current_score : response.turn.score,
            user_num_played : MG_GAME_ZENPOND.game.user_num_played,
            turns : MG_GAME_ZENPOND.game.turns,
            current_turn : MG_GAME_ZENPOND.turn
          };
          
          var licence_info = response.turn.licences;
          
          $("#words_to_avoid").hide(); 
          var words_to_avoid = []
          if (response.turn.wordstoavoid) {
            for (image in response.turn.wordstoavoid) {
              for (tag in response.turn.wordstoavoid[image]) {
                words_to_avoid.push(response.turn.wordstoavoid[image][tag]);
              }
            }
            if (words_to_avoid.length) 
              $("#words_to_avoid").show();
          }
          
          // turn info == image 
          var turn_info = {
            url : response.turn.images[0].scaled,
            url_full_size : response.turn.images[0].full_size,
            licence_info : MG_GAME_API.parseLicenceInfo(licence_info)
          }
          
          MG_GAME_API.renderTurn(response, score_info, turn_info, licence_info, more_info, words_to_avoid); 
          
          MG_GAME_ZENPOND.doQueryMessages = true;
          MG_GAME_ZENPOND.queryMessages();
        }
      }
      
      
    },
    
    onsubmit : function () {
      if (!MG_GAME_ZENPOND.busy) {
        
        var tags = $.trim(MG_GAME_ZENPOND.wordField.val());
        if (tags == "") {
          // val filtered for all white spaces (trim)
          MG_GAME_ZENPOND.error("<h1>Ooops</h1><p>Please enter at least one word</p>");
        } else {
          $("#partner-waiting").hide();
          
          MG_GAME_API.curtain.show();
          MG_GAME_ZENPOND.busy = true;
          
          MG_API.waitForThrottleIntervalToPass(function () {
            MG_API.ajaxCall('/games/play/gid/' + MG_GAME_API.settings.gid , function(response) {
              if (MG_API.checkResponse(response)) {
                MG_GAME_ZENPOND.onresponse(response);
              }
            }, {
              type:'post',
              data: {
                turn:MG_GAME_ZENPOND.turn,
                wordstoavoid: MG_GAME_ZENPOND.turns[MG_GAME_ZENPOND.turn-1].wordstoavoid,
                played_game_id:MG_GAME_ZENPOND.game.played_game_id,
                'submissions': [{
                  image_id : MG_GAME_ZENPOND.turns[MG_GAME_ZENPOND.turn-1].images[0].image_id,
                  tags: tags
                }]
              }
            });
          });
        }
      }
      return false;
    },
    
    ongameinit : function (response) {
      MG_GAME_ZENPOND.onresponse(response);
    },

  });
}(jQuery);

