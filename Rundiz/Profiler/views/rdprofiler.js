/**
 * Rundiz\Profiler JS.
 * 
 * @author Vee W.
 */


function loadCSS() {
	var sheet = document.createElement("style");
	sheet.setAttribute("type", "text/css");
	sheet.innerHTML = rdprofiler_css;
	document.getElementsByTagName("head")[0].appendChild(sheet);

	delete rdprofiler_css;
}// loadCSS


jQuery.noConflict();
jQuery(document).ready(function($) {
	loadCSS();

	$('.rdprofiler-see-details').on('click', 'a', function() {
		if ($(this).closest('li').hasClass('active')) {
			$('.rdprofiler-see-details').removeClass('active');
		} else {
			$('.rdprofiler-see-details').removeClass('active');
			$(this).closest('li').addClass('active');
		}
	});
});