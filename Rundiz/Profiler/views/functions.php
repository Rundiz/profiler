<?php
/** 
 * Rundiz Profiler
 * 
 * @license http://opensource.org/licenses/MIT * 
 */


/**
 * load css file and set as variable.
 * 
 * @param string $file css file name without .css extension.
 */
function rdProfilerLoadCss($file = 'rdprofiler')
{
    $css_file = __DIR__.DIRECTORY_SEPARATOR.$file.'.css';
    if (file_exists($css_file) && is_file($css_file)) {
        // use fopen, fread because it does not consume much memory. ( https://stackoverflow.com/a/2749458/128761 )
        $handle = fopen($css_file, 'r');
        $css_content = fread($handle, filesize($css_file));
        fclose($handle);
        unset($handle);

        // remove any new line and multiple spaces. we will put this css file content into js variable.
        $css_content = str_replace(["\r\n", "\r", "\n", "\t", "  "], '', $css_content);
        // replace or escape any double quote because it will break js variable (var variable = "css content here";)
        $css_content = str_replace('"', '\"', $css_content);

        echo '    // '.$file.'.css was loaded into js variable. -------------------'."\n";
        echo '    var rundizProfilerCss'.' = "'.$css_content.'";';
        unset($css_content);
    } else {
        echo '    // '.$file.'.css could not be found. -------------------'."\n";
        echo '    var rundizProfilerCss = \'\';';
    }
    unset($css_file);
}// rdProfilerLoadCss


/**
 * load js file and write into page.
 * 
 * @param string $file js file name without .js extension.
 */
function rdProfilerLoadJs($file = 'rdprofiler')
{
    $js_file = __DIR__.DIRECTORY_SEPARATOR.$file.'.js';
    if (is_file($js_file)) {
        // use fopen, fread because it does not consume much memory. ( https://stackoverflow.com/a/2749458/128761 )
        $handle = fopen($js_file, 'r');
        $js_content = fread($handle, filesize($js_file));
        fclose($handle);
        unset($handle);

        echo '    // '.$file.'.js was loaded into inline js. -------------------'."\n";
        echo $js_content;
        unset($js_content);
    } else {
        echo '    // '.$file.'.js could not be found. -------------------'."\n";
    }
}// rdProfilerLoadJs