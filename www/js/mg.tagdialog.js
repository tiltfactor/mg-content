/*
 * Helper class that allows to render the tag helper dialogs in the admin tools
 */
MG_TAGDIALOG = function ($) {
  return {
    settings : {
      admin_tool_url : '/admin'
    },
    
    /*
     * initialize the 
     */
    init : function (options) {
      if (options) {
        MG_TAGDIALOG.settings = $.extend(MG_TAGDIALOG.settings, options); //Pull from both defaults and supplied options
      } 
      
      // make sure each tag-dialog link triggers the dialog
      $('.tag-dialog .edit').unbind('click').click(MG_TAGDIALOG.onclick);
    },
    
    /*
     * helper functions triggered after a grid view has been updated
     */
    refresh : function () {
      $("#modalTagDialog").remove(); 
      $('.tag-dialog .edit').unbind('click').click(MG_TAGDIALOG.onclick);
    },
    
    /*
     * onclick handler. show the tag dialog pop up
     */
    onclick : function () {
      $('#modalTagDialog').remove();
      link = $(this);
      id = 0;
      
      href = link.attr('href').split('/');
      if (href.length > 0) {
        id = href[href.length -1] * 1;
      }
      
      if (id > 0) {
        tag_info = {
          id: id,
          title: link.text(),
          admin_base_url : MG_TAGDIALOG.settings.admin_base_url
        }
        
        $("#template-tag-dialog").tmpl(tag_info).appendTo($("body"));
        $("#modalTagDialog").dialog(); 
      }
      return false;
    },
    
    /*
     * helper function to retrieve the scroll position of a page
     */
    getPageScroll : function() {
      var xScroll, yScroll;
      if (self.pageYOffset) {
        yScroll = self.pageYOffset;
        xScroll = self.pageXOffset;
      } else if (document.documentElement && document.documentElement.scrollTop) {
        yScroll = document.documentElement.scrollTop;
        xScroll = document.documentElement.scrollLeft;
      } else if (document.body) {// all other Explorers
        yScroll = document.body.scrollTop;
        xScroll = document.body.scrollLeft;
      }
      return new Array(xScroll,yScroll)
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

