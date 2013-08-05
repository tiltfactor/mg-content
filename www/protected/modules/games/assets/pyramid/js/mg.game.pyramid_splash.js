$( document ).ready(function() {
    onResize();
    $("#footer").hide();
    $(".hover_btn").hover(
        function () {
            this.src = this.src.replace("_off","_on");
        }, function() {
            this.src = this.src.replace("_on","_off");
        }
    )
});
$(window).resize(function() {
    onResize ();
});
function onResize () {
    $("#splash_home .middle_height").css("max-height", $(window).height() / 3);
    $("#splash_logo").centerVertival();
}