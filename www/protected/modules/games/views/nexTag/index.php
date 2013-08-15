<div id="gamearea">
  <div id="no_js">Unfortunately we can't show the game as it relies on JavaScript which appears to be disabled on your browser.</div>
  <!-- Images from the database appear here -->
  <div id="stage">
    <!-- The #holder div contains both the primary image for tagging
         as well as the set of all images displayed at the end of the
         game (added-in via javascript) -->
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

<!-- The bounding-box around the text input and the button -->
<div id="input_area">
  <form action="#">
    <!-- user text field -->
    <input type="text" name="words" id="words" placeholder="List what is in this media, separated by commas" />
    <a href="#" id="button-play" class="ir"></a>
  </form>
</div>

<div id="fieldholder" class="clearfix">
</div>

<!-- New slide-out panel -->
<div id="sidepanel">
  <div id="tab" class="tab_closed"></div>
  <div class="content">
    <!-- In the mockup, looks like there's supposed to be a skinnier
         DIV comprising the top section -->
    <div class="skinny">
      <br />
      <br />

      <p>NexTag is part of the Metadata Games suite.</p>

      <p>Enter words that are relevant to the media. Separate words with a comma.</p>
      <br />
      <br />
      <br />
      <p>Metadata Games is an online game system for gathering useful data on photo, audio, and moving media artifacts.</p>
      <div id="more_info"></div>

      <br />
      <div id="licences"></div>
    </div>

    <div id="smallfooter">
      <div id="footerLogos">
	<a href="http://www.metadatagames.com/" target="_blank">
	  <img src="<?php echo MGHelper::bu("/"); ?>images/metadatagames_logo_650x64.png" />
	</a>
        <br />
	<a href="http://www.neh.gov" target="_blank">
	  <img src="<?php echo MGHelper::bu("/"); ?>images/neh_logo_horizontal_252x62.jpg" />
	</a>
        <br />
	<a href="http://www.dartmouth.edu" target="_blank">
	  <img src="<?php echo MGHelper::bu("/"); ?>images/dartmouth_logo_20120116.jpg" />
	</a>
	<a href="http://www.tiltfactor.org/" target="_blank">
	  <img src="<?php echo MGHelper::bu("/"); ?>images/tilt_logo_portrait_88x88.png" />
	</a>
	<a href="http://www.acls.org/" target="_blank">
	  <img src="<?php echo MGHelper::bu("/"); ?>images/acls-logo.gif" />
	</a>
      </div>
      <p>software developed by tiltfactor</p>
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
<script id="template-turn-image" type="text/x-jquery-tmpl">
  <div style="text-align:center" class="clearfix">
    <img src="${url}" alt="game media" id="image_to_tag" />
  </div>
</script>
<script id="template-turn-video" type="text/x-jquery-tmpl">
    <div style="text-align:center" class="clearfix">
        <video width="${width}" height="${height}" class="video" controls autoplay id="image_to_tag">
            <source src="${url_mp4}"></source>
            <source src="${url_webm}"></source>
        </video>
    </div>
</script>
<script id="template-turn-audio" type="text/x-jquery-tmpl">
    <div style="text-align:center" class="clearfix">
        <audio class="audio" controls preload autoplay id="image_to_tag">
            <source src="${url_mp3}"></source>
            <source src="${url_ogg}"></source>
        </audio>
    </div>
</script>
<script id="template-final-summary" type="text/x-jquery-tmpl">
  <table id="image_review">
    <tr>
      <td>
        <div class="smallholder_left">
          <a href="${url_full_size_1}" rel="zoom" media_type="${media_type_1}" title="${licence_info_1}">
            <img class="scoreimages" src="${url_1}" alt="game media" />
          </a>
        </div>
      </td>
      <td>
        <div class="smallholder_right">
          <a href="${url_full_size_2}" rel="zoom" media_type="${media_type_2}" title="${licence_info_2}">
            <img class="scoreimages" src="${url_2}" alt="game media" />
          </a>
        </div>
      </td>
   </tr>
   <tr>
      <td>
        <div class="smallholder_left">
          <a href="${url_full_size_3}" rel="zoom" media_type="${media_type_3}" title="${licence_info_3}">
            <img class="scoreimages" src="${url_3}" alt="game media" />
          </a>
        </div>
      </td>
      <td>
        <div class="smallholder_right">
          <a href="${url_full_size_4}" rel="zoom" media_type="${media_type_4}" title="${licence_info_4}">
            <img class="scoreimages" src="${url_4}" alt="game media" />
          </a>
        </div>
      </td>
   </tr>
 </table>
</script>
<script id="template-final-summary-play-once" type="text/x-jquery-tmpl">
  <div style="text-align:center" class="clearfix">
    <a href="${url_full_size}" rel="zoom" title="${licence_info}"><img src="${url}" alt="game media" /></a>
  </div>
</script>
<script id="template-more-info" type="text/x-jquery-tmpl">
  <a href="${url}">Click here to learn more about ${name}</a>
</script>
<script id="template-final-info" type="text/x-jquery-tmpl">
  <p class="final">You have earned ${current_score} point(s)! Thank you for playing.</p>
  <a href="#" id="button-play-again" class="ir"><span>Play Again</span></a>
</script>
<script id="template-final-tags-new" type="text/x-jquery-tmpl">
  <p class="tag-info"><!-- No tags displayed in NexTag --></p>
</script>
<script id="template-final-tags-matched" type="text/x-jquery-tmpl">
  <p class="tag-info"><!-- No tags displayed in NexTag --></p>
</script>
<script id="template-final-info-play-once" type="text/x-jquery-tmpl">
  You'll be redirected in <span id="remainingTime">${remainingTime}</span> seconds. <a href="${play_once_and_move_on_url}">Click here to proceed right away.</a></p>
</script>
<script id="template-info-modal-critical-error" type="text/x-jquery-tmpl">
  ${error} <p>Return to the <a href="${arcade_url}">arcade</a>.</p>
</script>