<?php
/** 
 * Rundiz Profiler
 * 
 * @license http://opensource.org/licenses/MIT * 
 */


/**
 * Render indent by number.
 * 
 * The indent is PHP PSR (4 space per 1 indent).
 * 
 * @param integer $number Number of indent.
 * @return string Return indent string.
 */
function rdProfilerIndent($number = 1)
{
    global $rundizProfilerBeautifulIndent;

    if ($rundizProfilerBeautifulIndent === false) {
        return '';
    }

    if (!is_numeric($number)) {
        $number = 1;
    }

    if (!is_int($number)) {
        $number = intval($number);
    }

    return str_repeat('    ', $number);
}// rdProfilerIndent


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


/**
 * Minify HTML output.
 * 
 * @link https://stackoverflow.com/a/10423788/128761 Original source code.
 * @param string $buffer
 * @return string
 */
function rdProfilerMinifyOutput($buffer)
{
    global $rundizProfilerMinifyHtml;
    if ($rundizProfilerMinifyHtml === false) {
        return $buffer;
    }

    // sanitize newline
    $buffer = str_replace(["\r\n", "\r"], "\n", $buffer);
    $search = array(
        '/\>[^\S ]+/s', //strip whitespaces after tags, except space
        '/[^\S ]+\</s', //strip whitespaces before tags, except space
        '/(\s)+/s'  // shorten multiple whitespace sequences
    );
    $replace = array(
        '>',
        '<',
        '\\1'
    );
    $blocks = preg_split('/(<\/?pre[^>]*>)/', $buffer, null, PREG_SPLIT_DELIM_CAPTURE);
    $buffer = '';
    foreach ($blocks as $i => $block) {
        if ($i % 4 == 2) {
            $buffer .= $block; //break out <pre>...</pre> with \n's
        } else {
            $buffer .= preg_replace($search, $replace, $block);
        }
    }
    // end of pre tag no need new line.
    $buffer = str_replace('</pre>' . "\n", '</pre>', $buffer);

    return $buffer;
}// rdProfilerMinifyOutput