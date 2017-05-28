/**
 * Rundiz\Profiler JS.
 * 
 * @author Vee W.
 */


/**
 * Load CSS in the `rundizProfilerCss` variable into HTML head section by create `<style>` element and set content into it.
 * 
 * @returns {undefined}
 */
function rundizProfilerLoadCSS() {
    if (typeof(rundizProfilerCss) !== 'undefined') {
        var sheet = document.createElement("style");
        sheet.setAttribute("type", "text/css");
        sheet.innerHTML = rundizProfilerCss;
        document.getElementsByTagName("head")[0].appendChild(sheet);
    }
}// rundizProfilerLoadCSS


// Run on page loaded ---------------------------------------------------------------------
jQuery.noConflict();
jQuery(document).ready(function($) {
    rundizProfilerLoadCSS();

    // set active class to show details panel on click, unset active on click again.
    $('.rdprofiler-see-details').on('click', 'a', function() {
        if ($(this).closest('li').hasClass('active')) {
            $('.rdprofiler-see-details').removeClass('active');
        } else {
            $('.rdprofiler-see-details').removeClass('active');
            $(this).closest('li').addClass('active');
        }
    });
});