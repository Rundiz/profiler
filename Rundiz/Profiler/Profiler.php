<?php
/** 
 * @package Rundiz\Profiler
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */


namespace Rundiz\Profiler;

class Profiler extends \Rundiz\Profiler\ProfilerBase
{


    /**
     * console class chaining.
     * 
     * @var \Rundiz\Profiler\Console for access console class
     */
    public $Console;


    /**
     * class constructor.
     */
    public function __construct() {
        $this->start_time = $this->getMicrotime(true);

        if (!class_exists('\\Rundiz\\Profiler\\Console')) {
            require_once __DIR__.DIRECTORY_SEPARATOR.'Console.php';
        }
        $this->Console = new \Rundiz\Profiler\Console($this);
    }// __construct


    /**
     * display the profiler data results.
     * 
     * @return string return the profiler result in html.
     */
    public function display($dbh = '', $display_db_function = '')
    {
        $this->gatherAll();

        // return display views.
        ob_start();
        require __DIR__.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'display.php';
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }// display


    /**
     * for checking only.
     * 
     * @return array
     */
    public function dumptest()
    {
        return array(
            'log_sections' => $this->log_sections, 
            'start_time' => $this->start_time, 
            'end_time' => $this->end_time,
            'max_memory_usage' => $this->max_memory_usage,
        );
    }// dumptest


    /**
     * gather all data before call display. this can be done automatically if you just call display().
     */
    public function gatherAll()
    {
        // don't call getMicrotime and memory_get_peak_usage in display page because it can increase too much.
        // at this point the application process should end already. push these number into class's property instead.
        $this->end_time = $this->getMicrotime();
        $this->max_memory_usage = memory_get_peak_usage();

        $this->gatherFiles();
        $this->gatherInputGet();
        $this->gatherInputPost();
        $this->gatherInputSession();
    }// gatherAll


    /**
     * gather included files and its size.
     */
    private function gatherFiles()
    {
        if (!array_key_exists('Files', $this->log_sections)) {
            return ;
        }

        $files = get_included_files();
        $section_data_array = array();
        $total_size = 0;
        $largest_size = 0;

        if (is_array($files)) {
            foreach ($files as $file) {
                $size = filesize($file);
                $section_data_array[] = array(
                    'data' => $file,
                    'size' => $size,
                );
                $total_size = bcadd($total_size, $size);

                if ($size > $largest_size) {
                    $largest_size = $size;
                }
            }// endforeach;
            unset($file, $size);
        }

        $section_data_array['total_size'] = $total_size;
        $section_data_array['largest_size'] = $largest_size;
        unset($largest_size, $total_size);

        $this->log_sections['Files'] = $section_data_array;
        unset($files, $section_data_array);
    }// gatherFiles


    /**
     * gather input get
     */
    private function gatherInputGet()
    {
        if (!array_key_exists('Get', $this->log_sections)) {
            return ;
        }

        $section_data_array = array();

        if (isset($_GET) && is_array($_GET)) {
            foreach ($_GET as $name => $value) {
                $section_data_array[] = array(
                    'data' => $name,
                    'inputvalue' => $value,
                );
            }// endforeach;
            unset($name, $value);
        }

        $this->log_sections['Get'] = $section_data_array;
        unset($section_data_array);
    }// gatherInputGet


    /**
     * gather input post
     */
    private function gatherInputPost()
    {
        if (!array_key_exists('Post', $this->log_sections)) {
            return ;
        }

        $section_data_array = array();

        if (isset($_POST) && is_array($_POST)) {
            foreach ($_POST as $name => $value) {
                $section_data_array[] = array(
                    'data' => $name,
                    'inputvalue' => $value,
                );
            }// endforeach;
            unset($name, $value);
        }

        $this->log_sections['Post'] = $section_data_array;
        unset($section_data_array);
    }// gatherInputPost


    /**
     * gather input session
     */
    private function gatherInputSession()
    {
        if (!array_key_exists('Session', $this->log_sections)) {
            return ;
        }

        $section_data_array = array();

        if (isset($_SESSION) && is_array($_SESSION)) {
            foreach ($_SESSION as $name => $value) {
                $section_data_array[] = array(
                    'data' => $name,
                    'inputvalue' => $value,
                );
            }// endforeach;
            unset($name, $value);
        }

        $this->log_sections['Session'] = $section_data_array;
        unset($section_data_array);
    }// Session


    /**
     * get readable file size.<br>
     * copy from php quick profiler
     * 
     * @param int $size
     * @param string $retstring
     * @return string
     */
    public function getReadableFileSize($size, $retstring = null) {
        // adapted from code at http://aidanlister.com/repos/v/function.size_readable.php
        $sizes = array('bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

        if ($retstring === null) {
            $retstring = '%01.2f %s';
        }

        $lastsizestring = end($sizes);

        foreach ($sizes as $sizestring) {
            if ($size < 1024) {
                break;
            }
            if ($sizestring != $lastsizestring) {
                $size /= 1024;
            }
        }
        if ($sizestring == $sizes[0]) {
            $retstring = '%01d %s';
        } // Bytes aren't normally fractional
        return sprintf($retstring, $size, $sizestring);
    }// getReadableFileSize


    /**
     * get microtime.
     * 
     * @param boolean $at_start set to true if this microtime is get at the very beginning of the app. this can allow newer php version to use $_SERVER['REQUEST_TIME_FLOAT'];
     * @return float microtime in float.
     */
    public function getMicrotime($at_start = false)
    {
        if ($at_start === true && is_array($_SERVER) && array_key_exists('REQUEST_TIME_FLOAT', $_SERVER)) {
            return floatval($_SERVER['REQUEST_TIME_FLOAT']);
        }

        return floatval(microtime(true));
    }// getMicrotime


    /**
     * get readable time.<br>
     * copy from php quick profiler
     * 
     * @param integer $time
     * @return string
     */
    public function getReadableTime($time) {
        $ret = $time;
        $formatter = 0;
        $formats = array('ms', 's', 'm');
        if ($time >= 1000 && $time < 60000) {
            $formatter = 1;
            $ret = ($time / 1000);
        }
        if ($time >= 60000) {
            $formatter = 2;
            $ret = ($time / 1000) / 60;
        }
        $ret = number_format($ret, 3, '.', '') . ' ' . $formats[$formatter];
        unset($formats, $formatter);
        return $ret;
    }// getReadableTime

}