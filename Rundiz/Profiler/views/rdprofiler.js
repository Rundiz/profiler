/**
 * Rundiz\Profiler JS.
 */


class RundizProfiler {

    /**
     * Load CSS in the `rundizProfilerCss` variable into HTML head section by create `<style>` element and set content into it.
     * 
     * @returns {undefined}
     */
    loadCss() {
        // rundizProfilerCss variable is defined in Rundiz/Profiler/views/functions.php.
        if (typeof(rundizProfilerCss) !== 'undefined') {
            var sheet = document.createElement("style");
            sheet.setAttribute("type", "text/css");
            sheet.innerHTML = rundizProfilerCss;
            document.getElementsByTagName("head")[0].appendChild(sheet);
        }
    }// loadCss


    /**
     * Scroll to each class in `matchKey` argument.
     * 
     * To use, use in a link with return. Example: `<a href="#" onclick="return RundizProfiler.scrollTo('#section', '.matchKey', jQuery(this));">link</a>`
     * 
     * @param {string} section The section id.
     * @param {string} matchKey The matchKey class.
     * @param {object} thisobj jQuery(this) object.
     * @returns {Boolean} Always return false to prevent any click link action.
     */
    static scrollTo(section, matchKey, thisobj) {
        let $ = jQuery.noConflict();
        let $Container = $(section);
        let $CurrentElement = thisobj.parents('li');
        let $ScrollTo;

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

        return false;
    }// scrollTo


    /**
     * Set class to body and element height value to global vairable.
     * 
     * @returns {undefined}
     */
    setClassAndValue() {
        if (document.body) {
            document.body.classList.add('body-contain-rdprofiler');
        }

        if (document.querySelector('.rdprofiler')) {
            rundizProfilerElementHeight = document.querySelector('.rdprofiler').offsetHeight;
        }
    }// setClassAndValue


}// RundizProfiler


// Run on page loaded ---------------------------------------------------------------------
/**
 * @type Integer rundizProfilerElementHeight The rundiz profiler element height.
 */
var rundizProfilerElementHeight;


jQuery(document).ready(function($) {
    let rundizProfiler = new RundizProfiler();

    // load CSS into HTML > head
    rundizProfiler.loadCss();
    // set class to body and element height value to var.
    rundizProfiler.setClassAndValue();

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