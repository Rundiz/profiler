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


/**
 * Scroll to each class in `matchKey` argument.
 * 
 * To use, use in a link with return. Example: `<a href="#" onclick="return rundizProfilerScrollTo('#section', '.matchKey', jQuery(this));">link</a>`
 * 
 * @param {string} section The section id.
 * @param {string} matchKey The matchKey class.
 * @param {object} thisobj jQuery(this) object.
 * @returns {Boolean} Always return false to prevent any click link action.
 */
function rundizProfilerScrollTo(section, matchKey, thisobj) {
    $ = jQuery.noConflict();
    $Container = $(section);
    $CurrentElement = thisobj.parents('li');

    if ($CurrentElement.prevAll(matchKey).length) {
        // if previous matchKey exists, use that one.
        $ScrollTo = $CurrentElement.prevAll(matchKey).offset().top;
        //console.log('use previous matchKey.');
        //console.log(($ScrollTo - $Container.offset().top + $Container.scrollTop()));
    } else if ($CurrentElement.nextAll(matchKey).length) {
        // if next matchKey exists, use that one.
        $ScrollTo = $CurrentElement.nextAll(matchKey).offset().top;
        //console.log('use next matchKey.');
        //console.log(($ScrollTo - $Container.offset().top + $Container.scrollTop()));
    }

    if (typeof($ScrollTo) !== 'undefined') {
        $Container.scroll();
        $Container.animate({
            scrollTop: ($ScrollTo - $Container.offset().top + $Container.scrollTop())
        }, 'fast');
    }

    delete $Container;
    delete $CurrentElement;
    delete $ScrollTo;

    return false;
}// rundizProfilerScrollTo


// Run on page loaded ---------------------------------------------------------------------
jQuery.noConflict();
jQuery(document).ready(function($) {
    rundizProfilerLoadCSS();

    // set active class to show details panel on click, unset active on click again.
    $('.rdprofiler-see-details').on('click', 'a.see-details', function() {
        if ($(this).closest('li').hasClass('active')) {
            $('.rdprofiler-see-details').removeClass('active');
        } else {
            $('.rdprofiler-see-details').removeClass('active');
            $(this).closest('li').addClass('active');
        }
    });
});