<div id="gamearea">
  <!-- Images from the database appear here --> 
  <div id="stage">
    <div id="zentag"> 
      <img src="<?php echo GamesModule::getAssetsUrl(); ?>/zentag/images/zentag.gif" alt="Zen Tag" width="222" height="46" /> 
    </div> 
    
    <div id="holder">
      <div id="image_container" class="clearfix"></div>
    </div>
    <!-- user text field -->
    <div id="fieldholder">   
      <textarea name="words" cols="50" rows="8" id="words">Describe the image as accurately as you can. Use commas to separate phrases or individual words. Click Ohm when you are done.</textarea> 
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
    <div id="licence"></div>  
  </div> 
</div>
<script id="template-scores" type="text/x-jquery-tmpl">
  <h2>Welcome ${user_name}</h2>
  <div class="total_score">Your total score <span>${user_score}</span></div>
  <div class="current_score">This game's score <span>${current_score}</span></div>
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
<script id="template-final-info" type="text/x-jquery-tmpl">
  <p class="final">Congratulations <b>${user_name}</b>, you scored <b>${current_score}</b> points in this game.</p>
</script>
