  
<!-- Images from the database appear here --> 
<div id="stage">
  <div id="zentag"> 
    <img src="<?php echo GamesModule::getAssetsUrl(); ?>/zentag/images/zentag.gif" alt="Zen Tag" width="222" height="46" /> 
  </div> 

  <div id="holder2">
    <!-- user text field --> 
    <div id="fieldholder">   
      <textarea name="words" cols="50" rows="8">Describe the image as accurately as you can. Use commas to separate phrases or individual words. Click Ohm when you are done.</textarea> 
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
  </div>
</div> 
 
<script id="template-turn" type="text/x-jquery-tmpl">
  <div align="center" style="width:500px;">
    <img src="${url}" alt="game image" />
  </div>
</script>
<script id="template-final-summary" type="text/x-jquery-tmpl">
  <div id="smallholder0"> 
    <img class="scoreimages" src="${url_1}" alt="game image" /> 
  </div> 
  <div id="smallholder1"> 
    <img class="scoreimages" src="${url_2}" alt="game image" /> 
  </div> 
  <div id="smallholder2"> 
    <img class="scoreimages" src="${url_3}" alt="game image" /> 
  </div> 
  <div id="smallholder3"> 
    <img class="scoreimages" src="${url_4}" alt="game image" /> 
  </div> 
</script>
