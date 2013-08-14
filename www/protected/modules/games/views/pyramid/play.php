<!-- The bounding-box around the text input and the button -->
<div class="wrap group">
    <header>
        <div class="center" align="center">
            <!-- The bounding-box around the text input and the button -->
           <span id="input_area">
                <form action="javascript: return false;">
                    <!-- user text field -->
                    <input type="text" name="word" id="word" class="level_1" placeholder="Enter a 4 letter word"/>
                    <a href="#" id="button-play" class="ir hidden"></a>
                </form>
            </span>
            <span id="countdown" class="countdown_amount"></span>
        </div>
    </header>

    <div id="gamearea" class="group">
        <div id="no_js">Unfortunately we can't show the game as it relies on JavaScript which appears to be disabled on your
            browser.
        </div>
        <!-- Images from the database appear here -->
        <div id="stage">
            <div id="holder">
                <div id="image_container"></div>
            </div>
        </div>
    </div>
    <div id="fieldholder" class="group">
    </div>

</div>

<footer class="group footer_level_1">
    <div>
        What's in this image?
    </div>
</footer>
<div style="height: 0px; padding: 0; margin: 0; overflow:scroll;">
    <audio id="next_level" >
        <source src="<?php echo GamesModule::getAssetsUrl() . '/pyramid/audio/nextlevel.ogg'?>" type="audio/ogg">
        <source src="<?php echo GamesModule::getAssetsUrl() . '/pyramid/audio/nextlevel.mp3'?>" type="audio/mpeg">
        <source src="<?php echo GamesModule::getAssetsUrl() . '/pyramid/audio/nextlevel.wav'?>" type="audio/wav">
    </audio>
    <audio id='try_again' style="height: 0px;">
        <source src="<?php echo GamesModule::getAssetsUrl() . '/pyramid/audio/tryagain.ogg'?>" type="audio/ogg">
        <source src="<?php echo GamesModule::getAssetsUrl() . '/pyramid/audio/tryagain.mp3'?>" type="audio/mpeg">
        <source src="<?php echo GamesModule::getAssetsUrl() . '/pyramid/audio/tryagain.wav'?>" type="audio/wav">
    </audio>
    <script id="template-licence" type="text/x-jquery-tmpl">
        <h4>${name}</h4>

        <p>${description}</p>
    </script>
    <script id="template-turn" type="text/x-jquery-tmpl">
        <div style="text-align:center">
            <img src="${url}" alt="game image" id="image_to_tag" style="width: auto !important; height: auto !important; "/>
        </div>
    </script>
    <script id="template-pyramid-step" type="text/x-jquery-tmpl">
        <div style="margin:5px auto;background-color:#EEEEEE;border:1px solid #CCCCCC;width:${width}px;">${tag}</div>
    </script>
    <script id="template-final-summary-play-once" type="text/x-jquery-tmpl">
        <div style="text-align:center" class="group">
            <a href="${url_full_size}" rel="zoom" title="${licence_info}"><img src="${url}" alt="game image"/></a>
        </div>
    </script>
    <script id="template-more-info" type="text/x-jquery-tmpl">
        <a href="${url}">Click here to learn more about ${name}</a>
    </script>
    <script id="template-final-info" type="text/x-jquery-tmpl">
        <div class="final">${finalMsg}</div>
        <div class="level_9 pyramid">&nbsp;</div>
        <div class="level_8 pyramid">&nbsp;</div>
        <div class="level_7 pyramid">&nbsp;</div>
        <div class="level_6 pyramid">&nbsp;</div>
        <div class="level_5 pyramid">&nbsp;</div>
        <div class="level_4 pyramid">&nbsp;</div>
        <div class="level_3 pyramid">&nbsp;</div>
        <div class="level_2 pyramid">&nbsp;</div>
        <div class="level_1 pyramid"></div>
        <div class="new_game"><a href="#" id="button-play-again"><span>&nbsp;</span></a></div>
    </script>
    <script id="template-final-info-play-once" type="text/x-jquery-tmpl">
        You'll be redirected in <span id="remainingTime">${remainingTime}</span> seconds. <a
            href="${play_once_and_move_on_url}">Click here to proceed right away.</a></p>
    </script>
    <script id="template-info-modal-critical-error" type="text/x-jquery-tmpl">
        ${error} <p>Return to the <a href="${arcade_url}">arcade</a>.</p>
    </script>
</div>