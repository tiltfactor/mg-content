/*
 * This library makes use of the jQuery jPlayer to play sounds.  
 * 
 */
MG_AUDIO = function ($) {
  return {
    settings : {
      swfPath : '/js',
      wmode:"window"
    },
    
    player_ok : false,
       
    sounds : {},
    
    /*
     * You have to call MG_AUDIO.init to initialize the player. Pass at least the swfPath setting to the player 
     * via the options. 
     * 
     * {
     *   swfPath: '/js'
     * } 
     * 
     */
    init : function (options) {
      //Combine options with default settings
      if (options) {
        MG_AUDIO.settings = $.extend(MG_AUDIO.settings, options); //Pull from both defaults and supplied options
      } 
      
      if ($.jPlayer !== undefined) {
        if (!MG_AUDIO.player_ok) {
          $('<div/>').attr("id", "mg_audio_player_container").css({
            display: 'block',
            position: 'absolute',
            zIndex: -9999,
            height: 0,
            width: 0, 
            top: -10000,
            left: -10000 
          }).appendTo($('body'));
          MG_AUDIO.player_ok = true;
        }
      } else {
        throw "$.jPlayer() cannot be found. Make sure to load the needed JavaScript file!";
      }
    },
    
    /*
     * Plays a sound you've previously added. 
     * 
     * string sound the handle identifying a sound
     */
    
    play : function (sound) {
      if (MG_AUDIO.player_ok && MG_AUDIO.sounds[sound] !== undefined && MG_AUDIO.sounds[sound].playerIsReady) {
        MG_AUDIO.sounds[sound].jPlayer("play");
      }
    },
    
    /*
     * Registers a sound file to play
     * 
     * Example
     * 
     * MG_AUDIO.add("hint", { 
     *     m4a: MG_GAME_GUESSWHAT.settings.asset_url + "/audio/hint.oga", 
     *     mp3: MG_GAME_GUESSWHAT.settings.asset_url + "/audio/hint.mp3", 
     *     wav: MG_GAME_GUESSWHAT.settings.asset_url + "/audio/hint.wav"}, 
     *     {supplied : 'oga, mp3, wav'});
     * 
     * string sound the handle identifying a sound
     * object files the files available for the sound effect
     * object options further settings for the jPlayer 
     */
    add : function (sound, files, options) {
      if (MG_AUDIO.player_ok) {
        if (MG_AUDIO.sounds[sound] === undefined) {
          MG_AUDIO.sounds[sound] = $('<div/>').attr("id", "mg_audio_player_" + sound).appendTo($('#mg_audio_player_container'));
          var player_settings = $.extend({}, MG_AUDIO.settings);
          player_settings = $.extend(player_settings, {
                  ready: function () {
                    MG_AUDIO.sounds[sound].playerIsReady = true;
                    $(this).jPlayer("setMedia", files);
                  },
                  supplied: "mp3, oga, wav"
                });
          player_settings = $.extend(player_settings, options);
          MG_AUDIO.sounds[sound].playerIsReady = false;
          MG_AUDIO.sounds[sound].jPlayer(player_settings);
        } else {
          throw 'MG_AUDIO sound effect "' + sound + '" already registered';
        }
      } else {
        throw 'MG_AUDIO has not been initialized';
      }
    }
  };
}(jQuery);