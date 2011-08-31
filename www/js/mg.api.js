MG_API = function ($) {
  return {
    curtain : null,
    errorModal : null,
    busy : false, // flag to give you a handle to avoid double submits  
    settings : {
      shared_secret : '',
      api_url : '',
      app_id : 'MG_API',
      throttleInterval : 5,
      onapiinit : function () {}
    },
       
    initialized : false,
    
    api_init : function (options) {
      if (!MG_API.initialized) {
        // create curtain and display it
        MG_API.curtain = $('<div id="mg_curtain"/>');
        MG_API.curtain.appendTo($("body")).css({opacity:0.8}); 
        
        MG_API.errorModal = $('<div id="mg_error"/>');
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
      MG_API.error('<h1>Not so fast!</h1><p>The system accepts submissions every ' + MG_API.settings.throttleInterval + ' seconds.</p>');
    },
    
    error : function (msg) {
      MG_API.curtain.hide();
      MG_API.errorModal.html(msg);
      MG_API.showModal(MG_API.errorModal, function () {MG_API.busy = false;});
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
      $.ajax(defaults);
    },
    
    showModal : function(id, onclosed) {
      if ($(id).length > 0) {
        $.colorbox({
          inline:true,
          href:id,
          onClosed: onclosed,
          overlayClose:false,
          opacity:0.75
        });  
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

