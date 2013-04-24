<div id="gamearea">
  <div id="no_js">Unfortunately we can't show the game as it relies on JavaScript which appears to be disabled on your browser.</div>
  <!-- Images from the database appear here --> 
  <div id="stage">
    <div id="holder">
      <div id="image_container" class="clearfix"></div>
    </div>

    <div id="scores"></div>
    <!-- The ability to pass on a given image -->
    <div id="passing">
      <br />
      <br />
      <br />
      <br />
      <br />
    </div>

  </div> 
</div>

<div id="fieldholder" class="clearfix">
</div>  

<!-- The bounding-box around the text input and the button -->
<div id="input_area">
  <form action="#">
    <!-- user text field -->
    <input type="text" name="words" id="words" />
    <a href="#" id="button-play" class="ir"><span>+</span></a> 
  </form> 
</div>

<!-- New slide-out panel -->
<div id="sidepanel">
  <div class="tab"></div>
  <div class="content">
    <!-- In the mockup, looks like there's supposed to be a skinnier
         DIV comprising the top section -->
    <div class="skinny">
      <br />
      <br />
      
      <p>NexTag is part of the Metadata Games suite.</p>
      
      <br />
      <br />
      <br />
      <p>Metadata Games is an online game system for gathering useful data on photo, audio, and moving image artifacts.</p>
      <div id="more_info"></div>
      
      <br />
      <div id="licences"></div>
    </div>

    <div id="smallfooter">
      &copy; <?php echo date('Y'); ?> <a href="http://www.tiltfactor.org/">tiltfactor</a>, all rights reserved
      <div id="footerLogos">
	<a href="http://www.dartmouth.edu" target="_blank">
	  <img src="<?php echo MGHelper::bu("/"); ?>images/dartmouth_logo_20120116.jpg" />
	</a>
	<a href="http://www.neh.gov" target="_blank">
	  <img src="<?php echo MGHelper::bu("/"); ?>images/NEH-Logo-Horizontal_252x62.jpg" />
	</a>
	<a href="http://www.acls.org/" target="_blank">
	  <img src="<?php echo MGHelper::bu("/"); ?>images/acls-logo.gif" />
	</a>
      </div>
    </div> <!-- smallfooter -->
    
  </div>
</div>

<script id="template-scores" type="text/x-jquery-tmpl">
<!--
  <h2>Welcome ${user_name}</h2>
  <div class="total_score">You played <span>${user_num_played}</span> times and scored <span>${user_score}</span> Points</div>
  <div class="current_score">This game's score <span>${current_score}</span> Points</div>
  <div class="total_turns">Turn <span>${current_turn} </span>/<span> ${turns}</span></div>
-->
</script>
<script id="template-licence" type="text/x-jquery-tmpl">
  <h4>${name}</h4>
  <p>${description}</p>
</script> 
<script id="template-turn" type="text/x-jquery-tmpl">
  <div style="text-align:center" class="clearfix">
    <img src="${url}" alt="game image" id="image_to_tag" />
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
<script id="template-final-info" type="text/x-jquery-tmpl">
  <p class="final">Congratulations <b>${user_name}</b>, you scored <b>${current_score}</b> points in this game.</p>
</script>
<script id="template-final-tags-new" type="text/x-jquery-tmpl">
  <p class="tag-info">New tag(s): <b>'${tags_new}'</b> scoring <b>${tags_new_score}</b> point(s)</p>
</script>
<script id="template-final-tags-matched" type="text/x-jquery-tmpl">
  <p class="tag-info">Matched tag(s): <b>'${tags_matched}'</b> scoring <b>${tags_matched_score}</b> point(s).</p>
</script>
<script id="template-final-info-play-once" type="text/x-jquery-tmpl">
  You'll be redirected in <span id="remainingTime">${remainingTime}</span> seconds. <a href="${play_once_and_move_on_url}">Click here to proceed right away.</a></p>
</script>
<script id="template-info-modal-critical-error" type="text/x-jquery-tmpl">
  ${error} <p>Return to the <a href="${arcade_url}">arcade</a>.</p>
</script>