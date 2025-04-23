/**
 * Rundiz\Profiler JS.
 */


class RundizProfiler {


    /**
     * Class constructor.
     */
    constructor() {
        // Listen AJAX response on `XMLHttpRequest` and `fetch()`. 
        // This must be called before DOM ready because normal AJAX request start after this.
        this.#listenAJAXResponseForProfiler();
    }// constructor


    /**
     * Format bytes.
     * 
     * @link https://stackoverflow.com/a/18650828/128761 Original source code.
     * @since 1.1.6
     * @param {int} bytes
     * @param {int} decimals
     * @returns {String}
     */
    #formatBytes(bytes, decimals = 2) {
        if (!+bytes)
            return '0 Bytes'

        const k = 1024
        const dm = decimals < 0 ? 0 : decimals
        const sizes = ['Bytes', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB']

        const i = Math.floor(Math.log(bytes) / Math.log(k))

        return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`
    }// #formatBytes


    /**
     * Listen on AJAX response that contain rundiz profiler result in it and display in the profiler bar.
     * 
     * @since 1.1.6
     * @link https://stackoverflow.com/a/27363569/128761 Original souce code that support `XMLHttpRequest`.
     * @link https://blog.logrocket.com/intercepting-javascript-fetch-api-requests-responses/ Original source code that support `fetch()`.
     * @returns {undefined}
     */
    #listenAJAXResponseForProfiler() {
        const thisClass = this;

        // listen AJAX requested via `XMLHttpRequest`.
        var origOpen = XMLHttpRequest.prototype.open;
        XMLHttpRequest.prototype.open = function() {
            const requestURL = arguments[1];
            this.addEventListener('loadend', function(event) {
                const responseType = this.getResponseHeader('content-type');
                const responseText = this.responseText;
                try {
                    if (responseType.toLowerCase().includes('/json') === true) {
                        const JSONObj = JSON.parse(responseText);
                        if (typeof(JSONObj) === 'object' && typeof(JSONObj['rundiz-profiler']) !== 'undefined') {
                            thisClass.#renderAJAXResponseForProfiler(requestURL, JSONObj);
                        }
                    }
                } catch (ex) {
                    console.debug('[rundiz-profiler]: Unable to parse JSON: ' + ex);
                }
            });
            origOpen.apply(this, arguments);
        };

        // listen AJAX requested via `fetch()`.
        const { fetch: originalFetch } = window;
        window.fetch = async (...args) => {
            let [resource, config] = args;
            let response = await originalFetch(resource, config);

            // clone the response for checking and render result.
            const response2 = response.clone();
            response2.json().then((data) => {
                if (typeof(data) === 'object' && typeof(data['rundiz-profiler']) !== 'undefined') {
                    thisClass.#renderAJAXResponseForProfiler(resource, data);
                }
            });

            return response;
        };
    }// #listenAJAXResponseForProfiler


    /**
     * Render AJAX response result that contain rundiz profiler data to profiler bar.
     * 
     * @since 1.1.6
     * @param {string} requestURL 
     * @param {object} JSONObj
     * @returns {undefined}
     */
    #renderAJAXResponseForProfiler(requestURL, JSONObj) {
        if (typeof(JSONObj['rundiz-profiler']) !== 'object' && typeof(JSONObj['rundiz-profiler']?.logSections) !== 'object') {
            return ;
        }
        const logSections = JSONObj['rundiz-profiler'].logSections;

        // render get, post section. ----------------------------------------------------------
        const allowedSections = ['Get', 'Post', 'Session'];
        for (const section in logSections) {
            if (!allowedSections.includes(section)) {
                continue;
            }
            if (!document.querySelector('.rdprofiler #Section' + section)) {
                console.warn('[rundiz-profiler]: Section tab #Section' + section + ' could not be found.');
                continue;
            }
            if (logSections[section].length <= 0) {
                continue;
            }

            const htmlSectionUl = document.querySelector('.rdprofiler #Section' + section + ' > ul');
            // append section's items to list panel.
            htmlSectionUl.insertAdjacentHTML('beforeend', '<li class="rdprofiler-new-xhr-session"><div>New XHR session (' + requestURL + ')</div></li>');
            for (const item of logSections[section]) {
                let resultHTML = '<li>'
                    + '<pre class="rdprofiler-log-data">(XHR) ' + item.data + '</pre>'
                    + '<pre class="rdprofiler-log-inputs-value">' + item.inputvalue + '</pre>'
                    + '</li>';
                htmlSectionUl.insertAdjacentHTML('beforeend', resultHTML);
            }// endfor; loop items of this section.
            // modify total items.
            const profileTabHTML = document.querySelector('.rdprofiler #Section' + section + ' > .rdprofiler-see-details-link');
            const currentTotalRegex = /(?<total>[\d]+)/g;
            let currentTotal = currentTotalRegex.exec(profileTabHTML.innerHTML);
            currentTotal = currentTotal.groups.total;
            profileTabHTML.outerHTML = profileTabHTML.outerHTML.replace(/([\d]+)/g, parseInt(logSections[section].length) + parseInt(currentTotal));
        }// endfor;

        // render database section. ----------------------------------------------------------
        if (logSections?.Database && logSections.Database.length > 0 && document.querySelector('.rdprofiler #SectionDatabase')) {
            const htmlSectionUl = document.querySelector('.rdprofiler #SectionDatabase > ul');
            // append section's items to list panel.
            htmlSectionUl.insertAdjacentHTML('beforeend', '<li class="rdprofiler-new-xhr-session"><div>New XHR session (' + requestURL + ')</div></li>');
            for (const item of logSections.Database) {
                let resultHTML = '<li>'
                    + '<pre class="rdprofiler-log-data">' + item.data + '</pre>'
                    + '<div class="rdprofiler-log-db-timetake">' + ((parseFloat(item.time_end) - parseFloat(item.time_start)) * 1000).toFixed(3) + ' ms</div>'
                    + '<div class="rdprofiler-log-memory">' + this.#formatBytes((parseFloat(item.memory_end) - parseFloat(item.memory_start))) + '</div>'
                    + '<div class="rdprofiler-log-newrow"><div class="rdprofiler-log-db-explain"></div></div>'
                    + '<div class="rdprofiler-log-newrow"><div class="rdprofiler-log-db-trace"><strong>Call trace (XHR):</strong><br>';
                if (item?.call_trace) {
                    for (const traceItem of item.call_trace) {
                        resultHTML += traceItem.file + ', line' + traceItem.line + '<br>';
                    }
                }
                resultHTML += '</div></div>'// end db backtrace.
                    + '</li>';
                htmlSectionUl.insertAdjacentHTML('beforeend', resultHTML);
            }// endfor; loop items of this section.
            // modify total items.
            const profileTabHTML = document.querySelector('.rdprofiler #SectionDatabase > .rdprofiler-see-details-link');
            const currentTotalRegex = /(?<total>[\d]+)/g;
            let currentTotal = currentTotalRegex.exec(profileTabHTML.innerHTML);
            currentTotal = currentTotal.groups.total;
            profileTabHTML.outerHTML = profileTabHTML.outerHTML.replace(/([\d]+)/g, parseInt(logSections.Database.length) + parseInt(currentTotal));
        }// endif; Database section.
    }// #renderAJAXResponseForProfiler


    /**
     * Listen on click and toggle show/hide details panel.
     * 
     * @since 1.1.7
     * @returns {undefined}
     */
    listenClickToggleDetailsPanel() {
        document.addEventListener('click', (event) => {
            const thisTarget = event.target;
            const detailsLink = thisTarget?.closest('.rdprofiler-see-details-link');
            const sectionTab = detailsLink?.closest('.rdprofiler-see-details');
            if (detailsLink && sectionTab) {
                if (sectionTab.classList.contains('rdprofiler-section-active')) {
                    sectionTab.classList.remove('rdprofiler-section-active');
                } else {
                    // close (remove CSS class) all other tabs.
                    document.querySelectorAll('.rdprofiler-see-details')?.forEach((item) => {
                        item.classList.remove('rdprofiler-section-active');
                    });
                    sectionTab.classList.add('rdprofiler-section-active');
                }
            }
        });
    }// listenClickToggleDetailsPanel


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
     * To use, use in a link with return. Example: `<a href="#" onclick="return RundizProfiler.scrollTo('.matchKey', this);">link</a>`
     * 
     * @param {string} matchKey The matchKey class.
     * @param {object} thisobj The JavaScript `this` object.
     * @returns {Boolean} Always return false to prevent any click link action.
     */
    static scrollTo(matchKey, thisobj) {
        const thisClass = new this();
        const rdProfiler = document.querySelector('.rdprofiler');
        const currentListItem = thisobj.closest('li');

        const allMatches = [...rdProfiler.querySelectorAll(matchKey)];
        let newMatches = [];
        let foundCurrent = false;

        for (const eachLi of allMatches) {
            if (true === foundCurrent) {
                newMatches.push(eachLi);
            }
            if (eachLi === currentListItem) {
                foundCurrent = true;
            }
        }// endfor;

        let targetListItem;
        if (typeof(newMatches[0]) !== 'undefined') {
            targetListItem = newMatches[0];
        } else {
            targetListItem = allMatches[0];
        }

        targetListItem?.scrollIntoView({
            'behavior': 'smooth',
        });

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
// Move new rundiz profiler class to out side DOM ready to make listen task(s) run before the others.
const rundizProfilerObj = new RundizProfiler();


document.addEventListener('DOMContentLoaded', () => {
    // Load CSS into HTML > head
    rundizProfilerObj.loadCss();
    // Set class to body and element height value to var.
    rundizProfilerObj.setClassAndValue();
    // Listen on click and toggle details panel.
    rundizProfilerObj.listenClickToggleDetailsPanel();
});