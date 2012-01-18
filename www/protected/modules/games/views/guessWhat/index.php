<div id="no_js">Unfortunately we can't show the game as it relies on JavaScript which appears to be disabled on your browser.</div>
<!-- Images from the database appear here --> 
<div id="stage">
  <div class="left-column">
    <div id="guesswhat"> 
      <img src="<?php echo GamesModule::getAssetsUrl(); ?>/guesswhat/images/guesswhatlogo.png" alt="Zen Pond" width="131" height="97" /> 
    </div> 
    <div id="scores" class="clearfix"></div>
    <div class="game_description describe guess"><h2>How To Play</h2>Help your partner guess the following image from the others. Then you try and guess what your partner's image is!<p>Click on the zoom icon to see a full-screen version.</p></div>
    <div id="wrong-guesses"><h3>Guessed Image(s)</h3></div>
    <div id="more_info"></div>  
  </div>
  
  <div id="messages"></div>
  <div id="partner-waiting"></div>

  <div id="game">
    <div class="describe">      
      <div class="image"></div>
      <div class="form">
      	<div id="words_to_avoid"></div>
        <div class="hints clearfix"><h3>Hints Given</h3></div>
        <div id="sendHintFormContainer">
          <form action="#">
            <input type="text" name="tag" width="60" id="tag"/>
            <a href="#" id="sendHint">SEND HINT</a>
          </form>
        </div>
      </div>
      <div id="guesses"></div>
    </div>
    <div class="guess">
      <div class="images" class="clearfix"></div>
    	<div class="form clearfix">
        <div class="hints"><h3>Hints Received</h3></div> 
      </div>
        <div id="requestHintContainer"></div>
    </div>
  </div>
  <div id="finalScreen"></div>
  
  <!-- Currently don't know value of having license info here -->
	<!-- when it's shown when player clicks on image anyways    -->
	<!-- so I'm commenting it out for now. SP 20111201          -->
  <!-- <div id="licences"></div> -->
  
</div>
<div id="info-modal"></div>

<script id="template-request-hint-active" type="text/x-jquery-tmpl">
  <h3>Select an image or ask for another <a href="#" id="requestHint">HINT</a></h3>
</script>
<script id="template-request-hint-inactive" type="text/x-jquery-tmpl">
  <h3>Select an image (you can not ask for further additional hints)</h3>
</script>

<script id="template-send-hint-form-inactive" type="text/x-jquery-tmpl">
  <h3>Please wait for the other player (you can not give further hints)</h3>
</script>

<script id="template-describe-image" type="text/x-jquery-tmpl">
  <img src="${url}" alt="game image" /><a href="${url_full_size}" rel="zoom" title="${licence_info}" class="zoom">zoom</a>
</script>

<script id="template-guess-image" type="text/x-jquery-tmpl">
 <div><a href="#" id="guess-me-${image_id}" class="guessthis" title="click to send this image as guess"><img src="${url}" alt="game image" /></a><a href="${url_full_size}" rel="zoom" title="${licence_info}" class="zoom">zoom</a><span class="wrong"></span></div>
</script>

<script id="template-wrong-guess-image" type="text/x-jquery-tmpl">
 <div><span><img src="${url}" alt="game image" /></span><a href="${url_full_size}" rel="zoom" title="${licence_info}" class="zoom">zoom</a></div>
</script>

<script id="template-final-scoring" type="text/x-jquery-tmpl">
  <h2>Congratulations <b>${user_name}</b></h2>
  <h3>You played with <b>${game_partner_name}</b> and scored <b>${current_score}</b> points in this game.</h3>
  <p><a href="${game_base_url}/guessWhat" id="newGame">PLAY AGAIN</a></p>
</script>

<script id="template-final-screen-turn-image" type="text/x-jquery-tmpl">
  <div class="finalImage clearfix{{if num_points == 0}} failedToGuess{{/if}}">
  <div><img src="${url}" alt="game image" /><a href="${url_full_size}" rel="zoom" title="${licence_info}" class="zoom">zoom</a></div>
  <p><b>${num_guesses}</b> Guess(es), <b>${num_hints}</b> Hint(s) [${hints}], <b>${num_points}</b> Point(s)</p>
  </div>
</script>

<script id="template-scores" type="text/x-jquery-tmpl">
  <div class="left">
    <h2>Welcome ${user_name}</h2>
    <div class="game_partner">You're playing with <span>${game_partner_name}!</span></div>
  </div>
  <div class="left">
    <div class="total_score">You played <span>${user_num_played}</span> times and scored <span>${user_score}</span> Points</div>
    <div class="current_score">This game's score <span>${current_score}</span> Points</div>
    <div class="total_turns">Turn <span>${current_turn}</span>&nbsp;/&nbsp;<span>${turns}</span></div>
    <div class="guesses"><span>${num_guesses_left}</span> guess(es) left</div>
    <!-- TODO: Give hints their own CSS class... -->
    <div class="hints_left">
      <span>${num_hints_left}</span> hint(s) left
    </div>
  </div>
</script>

<script id="template-more-info" type="text/x-jquery-tmpl">
  <a href="${url}">Click here to learn more about ${name}</a>
</script>

<script id="template-words-to-avoid-heading" type="text/x-jquery-tmpl">
  <h3>Words To Avoid</h3>
</script>

<script id="template-words-to-avoid" type="text/x-jquery-tmpl">
  <span>${tag}</span> 
</script>

<script id="template-licence" type="text/x-jquery-tmpl">
  <span><b>${name}</b> ${description}</span> 
</script>

<script id="template-info-modal-time-out" type="text/x-jquery-tmpl">
  No game partner found. <a href="${game_base_url}/guessWhat">Retry</a>. You could also go to back to the <a href="${arcade_url}">arcade</a> instead.
</script>
<script id="template-info-modal-wait-for-partner" type="text/x-jquery-tmpl">
  <p>Waiting for partner; timeout in <b>'${seconds}'</b> seconds.</p>{{if play_against_computer == 1}}<p><a href="#" id="playAgainstComputerNow">Play with the computer right now</a></p>{{/if}}<a href="${arcade_url}">Abort</a> game.
</script>
<script id="template-info-modal-waiting-for-hint" type="text/x-jquery-tmpl">
  Waiting for <b>${game_partner_name}</b> to submit a hint. <a href="${arcade_url}">Abort</a> game.
</script>
<script id="template-info-modal-wrong-guess-waiting-for-hint" type="text/x-jquery-tmpl">
  Wrong Guess! Waiting for <b>${game_partner_name}</b> to give another hint. <a href="${arcade_url}">Abort</a> game.
</script>
<script id="template-info-modal-waiting-for-guess" type="text/x-jquery-tmpl">
  Waiting for <b>${game_partner_name}</b> to make a guess. <a href="${arcade_url}">Abort</a> game.
</script>
<script id="template-info-modal-partner-aborted" type="text/x-jquery-tmpl">
  <b>${game_partner_name}</b> has left the game. <a href="${game_base_url}/guessWhat">Retry</a>. You could also go to back to the <a href="${arcade_url}">arcade</a> instead.
</script>
<script id="template-info-modal-wait-for-partner-to-submit" type="text/x-jquery-tmpl">
  Waiting for ${game_partner_name} to submit turn. <a href="${arcade_url}">Abort</a> game.
</script>
<script id="template-wating-for-hint" type="text/x-jquery-tmpl">
  <b>${game_partner_name}</b> waits for you to give a hint.
</script>
<script id="template-waiting-for-guess" type="text/x-jquery-tmpl">
  <b>${game_partner_name}</b> waits for you to make a guess.
</script>
<script id="template-wrong-guess-waiting-for-guess" type="text/x-jquery-tmpl">
  <b>${game_partner_name}</b> made a wrong guess. Please give another hint. 
</script>
<script id="template-error-hint-stop-word" type="text/x-jquery-tmpl">
  <h1>Ooops</h1><p>Your hint is on our stop word list. Please give another hint.</p>
</script>
<script id="template-error-hint-word-to-avoid" type="text/x-jquery-tmpl">
  <h1>Ooops</h1><p>Your hint is one of the words to avoid. Please give another hint.</p>
</script>
<script id="template-error-hint-given-twice" type="text/x-jquery-tmpl">
  <h1>Ooops</h1><p>You've already given this hint.</p>
</script>
<script id="template-failed-to-guess" type="text/x-jquery-tmpl">
  <div id="failedToGuess">
    <h2>No Guesses Left</h2>
    <p>The correct image would have been</p>
    <div class="image"><img src="${url}" alt="game image" /></div>
    <a href="#" id="loadNextTurn">Load next turn</a>
  </div>
</script>
<script id="template-partner-failed-to-guess" type="text/x-jquery-tmpl">
  <div id="partnerFailedToGuess">
    <h2>All Guesses Used</h2>
    <p><b>${game_partner_name}</b> did not find the image!</p>
    <a href="#" id="loadNextTurn">Load next turn</a>
  </div>
</script>
<script id="template-info-modal-critical-error" type="text/x-jquery-tmpl">
  ${error} <p>Return to the <a href="${arcade_url}">arcade</a>.</p>
</script>