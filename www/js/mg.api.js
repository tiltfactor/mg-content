MG_API = {
  settings : {
    shared_secret : '',
    api_url : '',
    app_id : 'MG_API'
  },
     
  initialized : false,
  
  base_init : function (options) {
    if (!MG_API.initialized) {
      //Combine options with default settings
      if (options) {
        MG_API.settings = $.extend(MG_API.settings, options); //Pull from both defaults and supplied options
      } 
      
      if (MG_API.settings.api_url.charAt(MG_API.settings.api_url.length - 1) == "/") // remove trailing slash
        MG_API.settings.api_url = MG_API.settings.api_url.substr(0, MG_API.settings.api_url.length - 1);
      
      if (MG_API.settings.api_url == "") 
        throw "MG_API.init() setting.api_url needs to be set";
      
      if (MG_API.settings.shared_secret == "") {
        MG_API.ajaxCall('/sharedsecret', function(response) {
          if (response.shared_secret !== undefined && response.shared_secret !== "") {
            MG_API.settings.shared_secret = response.shared_secret;
          } else {
             throw "MG_API.init() can't retrieve shared secret";
          }
        }, {async:false});
      }
      MG_API.initialized=true;
    } else {
      throw "MG_API.init() can only be called once";
    }
  },
  
  enhanceYourCalm : function () {
    alert("not too fast buddy"); // xxx implement throttle
    // on close close screen to reallow interaction with the game
    // reset system to allow a further request
  },
  
  error : function (msg) {
    alert($msg);
    log('Error: ' + MG_API.settings.shared_secret); // add overlay here
  },
  
  ajaxCall : function (path, callback, options) {
    var defaults = {
      url : MG_API.settings.api_url + path,
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
            if (!processed)
              MG_API.error(response.responseText);
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
  }
};

//xxx add msg_url to multiplayer  API

window.log = function(){
  log.history = log.history || [];  
  log.history.push(arguments);
  arguments.callee = arguments.callee.caller;  
  if(this.console) console.log( Array.prototype.slice.call(arguments) );
};
(function(b){function c(){}for(var d="assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,markTimeline,profile,profileEnd,time,timeEnd,trace,warn".split(","),a;a=d.pop();)b[a]=b[a]||c})(window.console=window.console||{});

