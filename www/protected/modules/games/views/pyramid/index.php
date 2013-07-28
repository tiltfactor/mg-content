<!-- The bounding-box around the text input and the button -->
<div class="wrap group">
    <header align="center">
        <div class="center">
            <div id="input_area">
                <form action="#">
                    <!-- user text field -->
                    <input type="text" name="word" id="word" placeholder="Enter a 3 letter word"/>
                    <a href="#" id="button-play" class="ir hidden"></a>
                </form>
            </div>
            <div id="countdown"></div>
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

<footer class="group">
    <div>
        Level 1
    </div>
</footer>

<!-- New slide-out panel -->
<div id="sidepanel">
    <div id="tab" class="tab_closed"></div>
    <div class="content">
        <!-- In the mockup, looks like there's supposed to be a skinnier
   DIV comprising the top section -->
        <div class="skinny">
            <br/>
            <br/>

            <p>Pyramid is part of the Metadata Games suite.</p>

            <p>We showed this image to 100 people on the street, and
                had them give one-word descriptions. You’re trying to
                match as many words as you can from what they said in
                one minute.</p>

            <p>But there’s a catch! You can’t just put in any
                word. First you can only put in a 3-letter word, then a 4-
                letter word, then 5-letters, etc.</p>
            <br/>
            <br/>
            <br/>

            <p>Metadata Games is an online game system for gathering useful data on photo, audio, and moving image
                artifacts.</p>

            <div id="more_info"></div>

            <br/>

            <div id="licences"></div>
        </div>

        <div id="smallfooter">
            <div id="footerLogos">
                <a href="http://www.metadatagames.com/" target="_blank">
                    <img src="<?php echo MGHelper::bu("/"); ?>images/metadatagames_logo_650x64.png"/>
                </a>
                <br/>
                <a href="http://www.neh.gov" target="_blank">
                    <img src="<?php echo MGHelper::bu("/"); ?>images/neh_logo_horizontal_252x62.jpg"/>
                </a>
                <br/>
                <a href="http://www.dartmouth.edu" target="_blank">
                    <img src="<?php echo MGHelper::bu("/"); ?>images/dartmouth_logo_20120116.jpg"/>
                </a>
                <a href="http://www.tiltfactor.org/" target="_blank">
                    <img src="<?php echo MGHelper::bu("/"); ?>images/tilt_logo_portrait_88x88.png"/>
                </a>
                <a href="http://www.acls.org/" target="_blank">
                    <img src="<?php echo MGHelper::bu("/"); ?>images/acls-logo.gif"/>
                </a>
            </div>
            <p>software developed by tiltfactor</p>
        </div>
        <!-- smallfooter -->

    </div>
</div>

<script id="template-licence" type="text/x-jquery-tmpl">
    <h4>${name}</h4>

    <p>${description}</p>
</script>
<script id="template-turn" type="text/x-jquery-tmpl">
    <div style="text-align:center">
        <img src="${url}" alt="game image" id="image_to_tag"/>
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
    <div class="word_level_9 pyramid"></div>
    <div class="word_level_8 pyramid"></div>
    <div class="word_level_7 pyramid"></div>
    <div class="word_level_6 pyramid"></div>
    <div class="word_level_5 pyramid"></div>
    <div class="word_level_4 pyramid"></div>
    <div class="word_level_3 pyramid"></div>
    <div class="word_level_2 pyramid"></div>
    <div class="word_level_1 pyramid"></div>
    <div class="new_game"><a href="#" id="button-play-again"><span></span></a></div>
</script>
<script id="template-final-info-play-once" type="text/x-jquery-tmpl">
    You'll be redirected in <span id="remainingTime">${remainingTime}</span> seconds. <a
    href="${play_once_and_move_on_url}">Click here to proceed right away.</a></p>
</script>
<script id="template-info-modal-critical-error" type="text/x-jquery-tmpl">
    ${error} <p>Return to the <a href="${arcade_url}">arcade</a>.</p>
</script>