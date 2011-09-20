MG_API = function ($) {
  return {
    curtain : null,
    fancyboxLink : null, // fancybox needs to be triggered via a link so we have to generate this invisible link
    modals : null,
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
    
    api_init : function (options) {
      if (!MG_API.initialized) {
        $('#no_js').remove();
        
        // create curtain and display it
        MG_API.curtain = $('<div id="mg_curtain"/>');
        MG_API.curtain.appendTo($("body")).css({opacity:0.7}); 
        
        MG_API.fancyboxLink = $('<a id="mg_fancybox_link" href="#" class="ir"></a>');
        
        MG_API.modals = $('<div id="mg_modals"/>').appendTo($("body"));
        $('<div id="mg_error"/>').appendTo(MG_API.modals);
        
        MG_API.curtain.appendTo($("body"));
        
        //Combine options with default settings
        if (options) {
          MG_API.settings = $.extend(MG_API.settings, options); //Pull from both defaults and supplied options
        } 
        
        if (MG_API.settings.api_url.charAt(MG_API.settings.api_url.length - 1) == "/") // remove trailing slash
          MG_API.settings.api_url = MG_API.settings.api_url.substr(0, MG_API.settings.api_url.length - 1);
        
        if (MG_API.settings.api_url == "") 
          throw "MG_API.init() setting.api_url needs to be set";
        
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
    
    enhanceYourCalm : function () {
      MG_API.error('<h1>Not so fast!</h1><p>The system accepts submissions every ' + (MG_API.settings.throttleInterval/1000) + ' seconds.</p>');
    },
    
    waitForThrottleIntervalToPass : function (callback, minimumInterval) {
      var timePastSinceLastResponse = new Date().getTime() - MG_API.timeLastCall;
      
      var interval = MG_GAME_API.settings.throttleInterval;
      if (minimumInterval !== undefined)
        interval = minimumInterval;
      
      if (timePastSinceLastResponse < interval) {
        var timeout = interval - timePastSinceLastResponse;
        setTimeout(callback, timeout + 100); // xxx investigate this further there are some discripancies between JavaScript and the server site time logging the added mills are even this out
      } else {
        callback();
      }
    },
    
    error : function (msg) {
      MG_API.curtain.hide();
      $("#mg_error").html(msg);
      MG_API.showModal($("#mg_error"), function () {MG_API.busy = false;});
    },
    
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
    
    ajaxCall : function (path, callback, options) {
      var defaults = {
        url : MG_API.settings.api_url + path,
        headers : $.parseJSON('{"X_' + MG_API.settings.app_id + '_SHARED_SECRET" : "' + MG_API.settings.shared_secret + '"}'),
        success : callback,
        statusCode : {
          420 : MG_API.enhanceYourCalm
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
      MG_API.timeLastCall = new Date().getTime();
    },
    
    showModal : function(modalContent, onclosed) {
      if ($(modalContent).length > 0) {
        MG_API.fancyboxLink.attr("href", "#" + modalContent.attr("id"));
        MG_API.fancyboxLink.fancybox({
          onClosed: onclosed,
          hideOnOverlayClick:false,
          overlayColor: '#000'
        });
        MG_API.fancyboxLink.trigger("click");
      } 
    }
  };
}(jQuery);

window.log = function(){
  log.history = log.history || [];  
  log.history.push(arguments);
  arguments.callee = arguments.callee.caller;  
  if(this.console) console.log( Array.prototype.slice.call(arguments) );
};
(function(b){function c(){}for(var d="assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,markTimeline,profile,profileEnd,time,timeEnd,trace,warn".split(","),a;a=d.pop();)b[a]=b[a]||c})(window.console=window.console||{});

