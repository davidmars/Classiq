require('./cq-visible-in-viewport.less');
$( document ).ready(function() {

    /**
     * affiche/masque les éléments "cq-visible-in-viewport'
     */
    function visibleInViewport(){
        console.log("visibleInViewport");
        $("[cq-visible-in-viewport]").each(function() {
            if ($(this).isInViewport()) { //pov.jQuery.more.js
                $(this).attr("cq-visible-in-viewport","visible")
            } else {
                $(this).attr("cq-visible-in-viewport","")
            }
        });
    }
    $("*").on('scroll', function() {
        visibleInViewport();
    });
    $(window).on('resize scroll', function() {
        visibleInViewport();
    });

});