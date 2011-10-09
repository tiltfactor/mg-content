<div id="no_js">Unfortunately we can't show the game as it relies on JavaScript which appears to be disabled on your browser.</div>
<!-- Images from the database appear here --> 
<div id="stage">
  <div class="left-column">
    <div id="guesswhat"> 
      <img src="<?php echo GamesModule::getAssetsUrl(); ?>/guesswhat/images/guesswhatlogo.png" alt="Zen Pond" width="200" height="90" /> 
    </div> 
    <div id="game_description" class="guess"><h2>How To Play</h2>Lorem ipsum sid amed. XXX. Describe here the 'guess' role of the game <br/>Click on the image(s) to see a full-screen version.</div>
    <div id="game_description" class="describe"><h2>How To Play</h2>Lorem ipsum sid amed. XXX.  Describe here the 'describe' role of the game <br/>Click on the image to see a full-screen version.</div>
    <div id="partner-waiting"></div>
    <div id="more_info"></div>
    <div id="words_to_avoid"></div>
  </div>
  <div id="scores" class="clearfix"></div>
  <div id="messages"></div>
  <div id="game">
    <div class="describe">
      <div class="image"></div>
      <div class="form">
        <div class="hints clearfix"><h3>Hints given</h3></div>
        <form action="#">
          <input type="text" name="tag" width="60" id="tag"/>
          <a href="#" id="sendHint">SEND A HINT</a>
        </form>
      </div>
      <div id="guesses"></div>
    </div>
    <div class="guess">
      <div class="form clearfix">
        <div class="hints"><h3>Hints received</h3></div>
        <a href="#" id="requestHint">REQUEST A HINT</a>
      </div>
      <div class="images" class="clearfix"></div>
    </div>
  </div>
  <div id="licences"></div>
  
</div>
<div id="info-modal"></div>

<h3 id="debug">SESSION ID: <?php 
  $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
  echo (int)Yii::app()->session[$api_id .'_SESSION_ID'];
  ?><span></span></h3>

<script id="template-describe-image" type="text/x-jquery-tmpl">
  <a href="${url_full_size}" rel="zoom" title="${licence_info}"><img src="${url}" alt="game image" /></a>
</script>

<script id="template-guess-image" type="text/x-jquery-tmpl">
 <a href="${url_full_size}" rel="zoom" title="${licence_info}"><img src="${url}" alt="game image" /></a>
</script>

<script id="template-scores" type="text/x-jquery-tmpl">
  <div class="left">
    <h2>Welcome ${user_name}</h2>
    <div class="game_partner">You're playing with <span>${game_partner_name}!</span></div>
  </div>
  <div class="left">
    <div class="total_score">You played <span>${user_num_played}</span> times and scored <span>${user_score}</span> Points</div>
    <div class="current_score">This game's score <span>${current_score}</span> Points</div>
    <div class="total_turns">Turn <span>${current_turn}</span>/<span>${turns}</span></div>
    <div class="guesses"><span>${num_guesses_left}</span> guesses left</div>
  </div>
</script>

<script id="template-more-info" type="text/x-jquery-tmpl">
  <a href="${url}">Click here to learn more about ${name}</a>
</script>

<script id="template-words-to-avoid-heading" type="text/x-jquery-tmpl">
  <h2>Words To Avoid</h2>
</script>

<script id="template-words-to-avoid" type="text/x-jquery-tmpl">
  <span>${tag}</span> 
</script>

<script id="template-licence" type="text/x-jquery-tmpl">
  <span><b>${name}</b> ${description}</span> 
</script>

<script id="template-info-modal-time-out" type="text/x-jquery-tmpl">
  No game partner found. <a href="${game_base_url}/guesswhat">Retry</a>. You could also go to back to the <a href="${arcade_url}">arcade</a> instead.
</script>
<script id="template-info-modal-wait-for-partner" type="text/x-jquery-tmpl">
  Waiting for partner; timeout in <b>'${seconds}'</b> seconds. xxx give info about computer player.
</script>
<script id="template-info-modal-waiting-for-hint" type="text/x-jquery-tmpl">
  Waiting for <b>${game_partner_name}</b> to submit a hint. <a href="${arcade_url}">Abort</a> game.
</script>
<script id="template-info-modal-waiting-for-guess" type="text/x-jquery-tmpl">
  Waiting for <b>${game_partner_name}</b> to make a guess. <a href="${arcade_url}">Abort</a> game.
</script>
<script id="template-info-modal-partner-aborted" type="text/x-jquery-tmpl">
  <b>${game_partner_name}</b> has left the game. <a href="${game_base_url}/guesswhat">Retry</a>. You could also go to back to the <a href="${arcade_url}">arcade</a> instead.
</script>
<script id="template-wating-for-hint" type="text/x-jquery-tmpl">
  <b>${game_partner_name}</b> waits for you to give a hint.
</script>
<script id="template-wating-for-guess" type="text/x-jquery-tmpl">
  <b>${game_partner_name}</b> waits for you to make a guess.
</script>
<script id="template-error-hint-stop-word" type="text/x-jquery-tmpl">
  <h1>Ooops</h1><p>Your hint is on our stop word list. Please give another hint.</p>
</script>
<script id="template-error-hint-word-to-avoid" type="text/x-jquery-tmpl">
  <h1>Ooops</h1><p>Your hint is one of the words to avoid. Please give another hint.</p>
</script>

<?php
/*    
 xxx remove
<script id="template-turn" type="text/x-jquery-tmpl">
  <div style="text-align:center" class="clearfix">
    <a href="${url_full_size}" rel="zoom" title="${licence_info}"><img src="${url}" alt="game image" /></a>
  </div>
</script>
<script id="template-final-summary" type="text/x-jquery-tmpl">
  <div id="smallholder0"> 
    <a href="${url_full_size_1}" rel="zoom" title="${licence_info_1}"><img class="scoreimages" src="${url_1}" alt="game image" /></a>
  </div> 
  <div id="smallholder1"> 
    <a href="${url_full_size_2}" rel="zoom" title="${licence_info_2}"><img class="scoreimages" src="${url_2}" alt="game image" /></a>
  </div> 
  <div id="smallholder2"> 
    <a href="${url_full_size_3}" rel="zoom" title="${licence_info_3}"><img class="scoreimages" src="${url_3}" alt="game image" /></a>
  </div> 
  <div id="smallholder3"> 
    <a href="${url_full_size_4}" rel="zoom" title="${licence_info_4}"><img class="scoreimages" src="${url_4}" alt="game image" /></a>
  </div> 
</script>
<script id="template-final-summary-play-once" type="text/x-jquery-tmpl">
  <div style="text-align:center" class="clearfix">
    <a href="${url_full_size}" rel="zoom" title="${licence_info}"><img src="${url}" alt="game image" /></a>
  </div>
</script>

<script id="template-final-info" type="text/x-jquery-tmpl">
  <p class="final">Congratulations <b>${user_name}</b>, you scored <b>${current_score}</b> points in this game.</p>
</script>
<script id="template-final-tags-new" type="text/x-jquery-tmpl">
  <p class="tag-info">New tag(s): <b>'${tags_new}'</b> scoring <b>${tags_new_score}</b> point(s)</p>
</script>
<script id="template-final-tags-matched" type="text/x-jquery-tmpl">
  <p class="tag-info">Matched tag(s): <b>'${tags_matched}'</b> scoring <b>${tags_matched_score}</b> point(s).</p>
</script>
<script id="template-final-tags-same_as" type="text/x-jquery-tmpl">
  <p class="tag-info">Same as ${game_partner_name}: <b>'${tags_same_as}'</b>.</p>
</script>

*/
?>
