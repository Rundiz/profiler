<?php
/** 
 * Rundiz Profiler
 * 
 * @license http://opensource.org/licenses/MIT
 */


namespace Rundiz\Profiler;

/**
 * Profiler class.
 * 
 * This class works as processing the data for profiler such as gather things (input, file, sessions), get micro time.<br>
 * This class also display the profiling result and dump the data for check or tests.
 * 
 * @package Rundiz\Profiler
 * @author Vee W.
 */
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
     * Count total log type in the "Logs" section.
     * 
     * @param string $logtype Accept debug, info, notice, warning, error, critical, alert, emergency. referrer: http://www.php-fig.org/psr/psr-3/
     * @return integer Return counted total log type in the "Logs" section.
     */
    public function countTotalLogType($logtype)
    {
        $count = 0;

        if (isset($this->log_sections['Logs']) && is_array($this->log_sections['Logs'])) {
            foreach ($this->log_sections['Logs'] as $item) {
                if (isset($item['logtype']) && $item['logtype'] == $logtype) {
                    $count++;
                }
            }// endforeach;
            unset($item);
        }

        return $count;
    }// countTotalLogType


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
        $this->reset();
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
        return [
            'log_sections' => $this->log_sections, 
            'start_time' => $this->start_time, 
            'end_time' => $this->end_time,
            'max_memory_usage' => $this->max_memory_usage,
        ];
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
        $section_data_array = [];
        $total_size = 0;
        $largest_size = 0;

        if (is_array($files)) {
            foreach ($files as $file) {
                $size = filesize($file);
                $section_data_array[] = [
                    'data' => $file,
                    'size' => $size,
                ];
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

        $section_data_array = [];

        if (isset($_GET) && is_array($_GET)) {
            foreach ($_GET as $name => $value) {
                $section_data_array[] = [
                    'data' => $name,
                    'inputvalue' => $value,
                ];
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

        $section_data_array = [];

        if (isset($_POST) && is_array($_POST)) {
            foreach ($_POST as $name => $value) {
                $section_data_array[] = [
                    'data' => $name,
                    'inputvalue' => $value,
                ];
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

        $section_data_array = [];

        if (isset($_SESSION) && is_array($_SESSION)) {
            foreach ($_SESSION as $name => $value) {
                $section_data_array[] = [
                    'data' => $name,
                    'inputvalue' => $value,
                ];
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
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

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
        $formats = ['ms', 's', 'm'];
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


    /**
     * Summary start and end of match key in a section.
     * 
     * @param string $section The section name.
     * @param string $matchKey The match key in that section.
     * @param integer $sectionKey The array index key of the section for check while displaying in the loop.
     * @return string Return the readable result if found 2 match key and can summary. Return empty string if not found.
     */
    public function summaryMatchKey($section, $matchKey, $sectionKey)
    {
        if (!is_string($section)) {
            $section = null;
        }

        if (!is_string($matchKey)) {
            $matchKey = null;
        }

        if (!is_numeric($sectionKey)) {
            $sectionKey = 0;
        }

        $output = '';

        if (isset($this->log_sections[$section]) && is_array($this->log_sections[$section]) && $matchKey !== null) {
            $matchKeyTime = [];
            $matchKeyMemory = [];

            foreach ($this->log_sections[$section] as $key => $item) {
                if (isset($item['matchKey']) && $item['matchKey'] == $matchKey && $key <= $sectionKey) {
                    if (count($matchKeyMemory) >= 2 || count($matchKeyTime) >= 2) {
                        break;
                    }

                    if (isset($item['time'])) {
                        $matchKeyTime[] = $item['time'];
                    }

                    if (isset($item['memory'])) {
                        $matchKeyMemory[] = $item['memory'];
                    }
                }
            }// endforeach;
            unset($item, $key);

            if (count($matchKeyTime) >= 2) {
                $output = $this->getReadableTime((max($matchKeyTime)-min($matchKeyTime))*1000);
            } elseif (count($matchKeyMemory) >= 2) {
                $output = $this->getReadableFileSize(max($matchKeyMemory)-min($matchKeyMemory));
            }

            unset($matchKeyMemory, $matchKeyTime);
        }

        return $output;
    }// summaryMatchKey

}