<div id="gamearea">
  <div id="no_js">Unfortunately we can't show the game as it relies on JavaScript which appears to be disabled on your browser.</div>
  <!-- Images from the database appear here --> 
  <div id="stage">
    <div id="game_description">
    	<h2>How To Play</h2>
    	<ul>
    		<li>Describe the image as accurately as you can.</li> 
    		<li>Use commas to separate phrases or individual words.</li>
    		<li>Hit enter or click Ohm when done.</li>
    		<li>Match your partner's tags for bonus points!</li>
    	</ul>
    	<p>Click on the image to see a full-screen version.</p>
    </div>
    <div id="zenpond"> 
      <img src="<?php echo GamesModule::getAssetsUrl(); ?>/zenpond/images/zenpond.gif" alt="Zen Pond" width="222" height="46" /> 
    </div> 
    
    <div id="holder">
      <div id="image_container" class="clearfix"></div>
    </div>
    <!-- user text field -->
    <div id="fieldholder" class="clearfix">   
      <form action="#"><textarea name="words" cols="50" rows="8" id="words"></textarea></form> 
    </div>  
    <div id="box1"> 
      <div id="box2"> 
        <div id="box3"> 
          <a href="#" id="button-play" class="ir"> 
            play
          </a> 
        </div> 
      </div> 
    </div>
    <div id="scores"></div>
    <div id="licences"></div>
    <div id="more_info"></div>
    <div id="words_to_avoid"></div>
    <div id="partner-waiting"></div>
  </div>
  <div id="partner-waiting-modal"></div>
</div>
<h3 id="debug">SESSION ID: <?php 
    $api_id = Yii::app()->fbvStorage->get("api_id", "MG_API");
    echo (int)Yii::app()->session[$api_id .'_SESSION_ID'];
    ?><span></span></h3>
<script id="template-scores" type="text/x-jquery-tmpl">
  <h2>Welcome ${user_name}</h2>
  <div class="game_partner">You're playing with 
  	<br /><span>${game_partner_name}!</span></div>
  <div class="total_score">You played <span>${user_num_played}</span> times and scored <span>${user_score}</span> Points</div>
  <div class="current_score">This game's score <span>${current_score}</span> Points</div>
  <div class="total_turns">Turn <span>${current_turn}</span>/<span>${turns}</span></div>
</script>
<script id="template-licence" type="text/x-jquery-tmpl">
  <h4>${name}</h4>
  <p>${description}</p>
</script> 
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
<script id="template-more-info" type="text/x-jquery-tmpl">
  <a href="${url}">Click here to learn more about ${name}</a>
</script>
<script id="template-words-to-avoid-heading" type="text/x-jquery-tmpl">
  <h2>Words To Avoid</h2>
</script>
<script id="template-words-to-avoid" type="text/x-jquery-tmpl">
  <span>${tag}</span>
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
<script id="template-partner-waiting-modal" type="text/x-jquery-tmpl">
  <p>Waiting for partner; timeout in <b>'${seconds}'</b> seconds.</p>{{if play_against_computer == 1}}<p><a href="#" id="playAgainstComputerNow">Play against the computer right now</a></p>{{/if}}<a href="${arcade_url}">Abort</a> game.
</script>
<script id="template-partner-waiting-modal-turn" type="text/x-jquery-tmpl">
  Waiting for partner to submit turn.
</script>
<script id="template-partner-waiting-time-out" type="text/x-jquery-tmpl">
  No game partner found. <a href="${game_base_url}/ZenPond">Retry</a>. You could also play a round of <a href="${game_base_url}/ZenTag">Zen Tag</a>, or go to back to the <a href="${arcade_url}">arcade</a> instead.
</script>
<script id="template-partner-waiting-for-submit" type="text/x-jquery-tmpl">
  <b>${game_partner_name}</b> <br />waits for your submission!
</script>
<script id="template-partner-aborted" type="text/x-jquery-tmpl">
  <b>${game_partner_name}</b> has left the game. <a href="${game_base_url}/ZenPond">Retry</a>. You could also play a round  of <a href="${game_base_url}/ZenTag">Zen Tag</a>, or go to back to the <a href="${arcade_url}">arcade</a> instead.
</script>

