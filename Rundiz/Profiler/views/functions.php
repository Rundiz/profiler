<?php
/** 
 * @package Rundiz\Profiler
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */


/**
 * load css file and set as variable.
 * 
 * @param string $file css file name without .css extension.
 */
function rdprofilerLoadCss($file = 'rdprofiler')
{
    $css_file = __DIR__.DIRECTORY_SEPARATOR.$file.'.css';
    if (file_exists($css_file) && is_file($css_file)) {
        $handle = fopen($css_file, 'r');
        $css_content = fread($handle, filesize($css_file));
        fclose($handle);
        unset($handle);

        $css_content = str_replace(array("\r\n", "\r", "\n", "\t", "  "), '', $css_content);

        echo 'var '.$file.'_css'.' = "'.$css_content.'";';
        unset($css_content);
    } else {
        echo 'var '.$file.' = \'\';';
    }
    unset($css_file);
}// rdprofilerLoadCss


/**
 * load js file and write into page.
 * 
 * @param string $file js file name without .js extension.
 */
function rdprofilerLoadJs($file = 'rdprofiler')
{
    $js_file = __DIR__.DIRECTORY_SEPARATOR.$file.'.js';
    if (file_exists($js_file) && is_file($js_file)) {
        $handle = fopen($js_file, 'r');
        $js_content = fread($handle, filesize($js_file));
        fclose($handle);
        unset($handle);

        echo $js_content;
        unset($js_content);
    }
}// rdprofilerLoadJs