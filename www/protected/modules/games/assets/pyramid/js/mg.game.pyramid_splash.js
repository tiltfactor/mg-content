$( document ).ready(function() {
    onResizes();
    $("#footer").hide();
});
$(window).resize(function() {
    onResizes ();
});
function onResizes () {
    $("#splash_home .middle_height").css("max-height", $(window).height() / 3);
    $("#splash_logo").css({"max-width": $(window).width(), "max-height": $(window).height() / 3 });
}