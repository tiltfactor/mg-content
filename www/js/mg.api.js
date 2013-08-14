MG_API = function ($) {
  return {
    curtain : null, // object the screen behind modal windows.
    fancyboxLink : null, // fancybox needs to be triggered via a link so we have to generate this invisible link
    modals : null, // 
    game : null, 
    busy : false, // flag to give you a handle to avoid double submits
    
    timeLastCall : 0, //some requests might want to make sure to wait for the throttle interval to pass before makeing a call
    
    settings : {
      shared_secret : '',
      api_url : '',
      app_id : 'MG_API',
      throttleInterval : 2500,
      onapiinit : function () {}
    },
       
    initialized : false,
    
    /*
     * initialized the API
     */
    api_init : function (options) {
      if (!MG_API.initialized) {
        $('#no_js').remove();
        
        // create curtain and display it
        MG_API.curtainDiv = $('<div id="mg_curtain"/>');
        MG_API.curtainDiv.appendTo($("body")).css({
          opacity:0.7, 
          height: $(document).height(),
          width: $(document).width(),
          backgroundPosition : ($(window).width()/2) + 'px ' + ($(window).height()/2) + 'px'
        }); 
        
        // create functionality to show and hide the curtain
        MG_API.curtain = {
          show : function () {
            MG_API.curtainDiv.show();
            MG_API.curtainDiv.css({height: $(document).height(), width: $(document).width(),backgroundPosition : ($(window).width()/2) + 'px ' + ($(window).height()/2) + 'px'});
          },
          
          hide : function () {
            MG_API.curtainDiv.hide();
          }
        }
        
        // add a resize handler that resizes the curtain if the window resizes
        $(window).resize(function () {
          MG_API.curtainDiv.css({height: $(document).height(), width: $(document).width(),backgroundPosition : ($(window).width()/2) + 'px ' + ($(window).height()/2) + 'px'}); 
        });
        
        // cater for modal windows
        MG_API.fancyboxLink = $('<a id="mg_fancybox_link" href="#" class="ir"></a>'); // fancybox needs a hidden link to trigger it.
        MG_API.modals = $('<div id="mg_modals"/>').appendTo($("body"));
        $('<div id="mg_error"/>').appendTo(MG_API.modals);
        $('<div id="mg_popup"/>').appendTo(MG_API.modals);
        
        //Combine options with default settings
        if (options) {
          MG_API.settings = $.extend(MG_API.settings, options); //Pull from both defaults and supplied options
        } 
        
        if (MG_API.settings.api_url.charAt(MG_API.settings.api_url.length - 1) == "/") // remove trailing slash
          MG_API.settings.api_url = MG_API.settings.api_url.substr(0, MG_API.settings.api_url.length - 1);
        
        if (MG_API.settings.api_url == "") 
          throw "MG_API.init() setting.api_url needs to be set";
        
        // retrieve shared secret
        // nearly all API calls need a shared secret as HTTP header
        // here the system attempts to retrieve 
        // MG_API.ajaxCall will always embed the needed header 
        if (MG_API.settings.shared_secret == "") {
          MG_API.ajaxCall('/user/sharedsecret', function(response) {
            if (MG_API.checkResponse(response)) {
              if (response.shared_secret !== undefined && response.shared_secret !== "") {
                MG_API.settings.shared_secret = response.shared_secret;
                MG_API.settings.onapiinit();
              } else {
                 throw "MG_API.init() can't retrieve shared secret";
              }
            }
          }, {async:false});
        }
        MG_API.initialized=true;
      } else {
        throw "MG_API.init() can only be called once";
      }
    },
    
    /*
     * if the interval between two API calls is to fast it will be throttled. The system 
     * will reply with status 420 causing this method to be called  
     */
    enhanceYourCalm : function () {
      MG_API.error('<h1>Not so fast!</h1><p>The system accepts submissions every ' + (MG_API.settings.throttleInterval/1000) + ' seconds.</p>');
    },
    
    /*
     * use this method to wrap around api calls to ensure that the api call get's not blocked 
     * by MGs throttle filter
     * 
     * @param function callback Function to be called once the throttle timeout interval has been passed
     * @param int minimumInterval make sure to wait at least this many milli seconds
     */
    waitForThrottleIntervalToPass : function (callback, minimumInterval) {
      var timePastSinceLastResponse = new Date().getTime() - MG_API.timeLastCall;
      
      var interval = MG_GAME_API.settings.throttleInterval;
      if (minimumInterval !== undefined)
        interval = minimumInterval;
      
      if (timePastSinceLastResponse < interval) {
        var timeout = interval - timePastSinceLastResponse;
        setTimeout(callback, timeout + 100); // there are some discripancies between JavaScript and the server site time logging the added mills are even this out
      } else {
        callback();
      }
    },
    
    /*
     * shows an error message modal window
     * 
     * @param string msq partial html/content of the modal window
     */
    error : function (msg) {
      MG_API.curtain.hide();
      $("#mg_error").html(msg);
      MG_API.showModal($("#mg_error"), function () {MG_API.busy = false;});
    },
    
    /*
     * shows an popup window
     * 
     * @param string msq partial html/content of the modal window
     * @param options object of additional options to the fancybox modal
     */
    popup : function (content, options) {
      MG_API.curtain.hide();
      $("#mg_popup").html(content);
      MG_API.showModal($("#mg_popup"), function () {MG_API.busy = false;}, options);
    },
    
    /*
     * checks the response if an error happened and shows these in a modal window
     * 
     * @param object response AJAX call response object
     */
    checkResponse : function (response) {
      if (response.status == "error") {
        if (response.errors !== undefined) {
          var errors = "";
          $.each(response.errors, function(key, value) { 
            errors += key + ': ' + value + "<br/>";
          });
          MG_API.error("<h1>Ooops</h1><p>" + errors + "</p>");
          
        } else {
          MG_API.error("<h1>Ooops</h1><p>An error happened!</p>");
        }
        return false;
      } 
      return true;
    },
    
    /*
     * renders modal window that allows to leave a game on a critical error
     * gracefully
     * 
     * @param object response AJAX call response object
     */
    exitGame : function (response) {
      MG_API.curtain.hide();
      $("#mg_popup").html("");
      
      if ($("#template-info-modal-critical-error").length > 0) {
        $("#mg_popup").html(response.responseText);
        $("#template-info-modal-critical-error").tmpl({
          arcade_url: MG_API.settings.arcade_url
        }).appendTo($("#mg_popup"));  
      } else {
        $("#mg_popup").html("Error");
      }
      MG_API.showModal($("#mg_popup"), function () {}, {modal:true});
    },
    
    /*
     * helper function to execure an ajax call. It creates an jquery ajax call with all needed 
     * parameter.
     * 
     * @param string path the path url of the api call (MG_API.settings.api_url + path)
     * @param function callback called on success
     * @param options object of additional options to influence ajax call 
     * @param boolean doNotSaveLastCallTime Do not regard this call in the throttle interval management
     */
    ajaxCall : function (path, callback, options, doNotSaveLastCallTime) {
      var defaults = {
        url : MG_API.settings.api_url + path,
        // set needed shared secret header
        headers : $.parseJSON('{"X_' + MG_API.settings.app_id + '_SHARED_SECRET" : "'.replace("_", "-") + MG_API.settings.shared_secret + '"}'),
        success : callback,
        statusCode : {
          420 : MG_API.enhanceYourCalm,
          600 : MG_API.exitGame
        },
        complete : function(response, status_code) {
          switch(status_code) {
            case "success":
            case "notmodified":
            case "retry":
              break;
            
            case "error":
              var processed = false;
              for (var sc in defaults.statusCode) {
                if (sc == response.status) { // check if this particular code has already been processed
                  processed = true; // it has so don't show the error message
                  break;
                }
              }
              if (!processed) {
                MG_API.error(response.responseText);
              }
              break;
            
            case "timeout":
              MG_API.error("<h1>Error</h1><p>The connection timed out.</p>");
              break;
            
            case "aborted":
              MG_API.error("<h1>Error</h1><p>The connection has been aborted.</p>");
              break;
            
            case "parsererror":
              MG_API.error("<h1>Error</h1><p>The response could not be parsed.</p>");
              break;
                
          }
        }
      }
      if (options) {
        defaults = $.extend(defaults, options); //Pull from both defaults and supplied options
      }
      var jsXHR = $.ajax(defaults);
      
      if (!doNotSaveLastCallTime) {
        MG_API.timeLastCall = new Date().getTime();
      }
    },
    
    /*
     * show a modal fancybox overlay popup
     * 
     * @param modalContent Partial HTML
     * @param function onclosed callback function to be executed onclose
     * @param options object of additional options to influence the fancybox 
     */
    showModal : function(modalContent, onclosed, options) {
      if ($(modalContent).length > 0) {
        MG_API.fancyboxLink.attr("href", "#" + modalContent.attr("id"));
        MG_API.fancyboxLink.fancybox($.extend({
          onClosed: onclosed,
          hideOnOverlayClick:false,
          overlayColor: '#000'
        }, options));
        MG_API.fancyboxLink.trigger("click");
      } 
    },
  };
}(jQuery);

window.log = function(){
  log.history = log.history || [];  
  log.history.push(arguments);
  arguments.callee = arguments.callee.caller;  
  if(this.console) console.log( Array.prototype.slice.call(arguments) );
};
(function(b){function c(){}for(var d="assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,markTimeline,profile,profileEnd,time,timeEnd,trace,warn".split(","),a;a=d.pop();)b[a]=b[a]||c})(window.console=window.console||{});

