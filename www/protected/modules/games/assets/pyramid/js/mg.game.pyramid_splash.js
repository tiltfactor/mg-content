$( document ).ready(function() {
    onResize();
    $("#footer").hide();
});
$(window).resize(function() {
    onResize ();
});
function onResize () {
    $("#splash_home .middle_height").css("max-height", $(window).height() / 3);
    $("#splash_logo").centerVertival();
//    $("#splash_logo").css({"max-width": $(window).width(), "max-height": $(window).height() / 3 });
}