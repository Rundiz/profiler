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
 * This class works as processing the data for profiler such as gather things (input, file, sessions), get `microtime()`.<br>
 * This class also display the profiling result and dump the data for check or tests.
 * 
 * @package Rundiz\Profiler
 */
class Profiler extends \Rundiz\Profiler\ProfilerBase
{


    /**
     * @var bool Set to `true` for beautiful indent, `false` for not indent. The indent HTML result makes code looks easier.
     */
    public $beautifulIndent = false;


    /**
     * Console class chaining.
     * 
     * @var \Rundiz\Profiler\Console For access `Console` class.
     */
    public $Console;


    /**
     * @var bool Set to `true` to minify HTML output, `false` for not. If this was set to `true` then the beautiful indent will be ignored. Default is `false`.
     */
    public $minifyHtml = false;


    /**
     * Class constructor.
     */
    public function __construct() 
    {
        $this->start_time = $this->getMicrotime(true);

        if (!class_exists('\\Rundiz\\Profiler\\Console')) {
            require_once __DIR__.DIRECTORY_SEPARATOR.'Console.php';
        }
        $this->Console = new \Rundiz\Profiler\Console($this);
    }// __construct


    /**
     * Count total log type in the "Logs" section.
     * 
     * @param string $logtype Accepted debug, info, notice, warning, error, critical, alert, emergency. reference: http://www.php-fig.org/psr/psr-3/
     * @return int Return counted total log type in the "Logs" section.
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
     * Display the profiler data results.
     * 
     * @param \PDO $dbh The `\PDO` class. This will be use in display views file.
     * @param callable $display_db_function The display DB profiler callback function. This will be use in display views file.
     * @return string Return the profiler result in HTML.
     */
    public function display($dbh = '', $display_db_function = '')
    {
        $this->gatherAll();

        global $rundizProfilerBeautifulIndent;
        $rundizProfilerBeautifulIndent = $this->beautifulIndent;
        global $rundizProfilerMinifyHtml;
        $rundizProfilerMinifyHtml = $this->minifyHtml;

        // return display views.
        ob_start();
        require __DIR__.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'display.php';
        $this->reset();
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }// display


    /**
     * For checking only.
     * 
     * @return array Return data of sections, start time, end time, max memory usage.
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
     * Gather all data before call display. This can be done automatically if you just call `display()`.
     * 
     * It is not recommend to call this method directly until you have to use some method like `dumptest()`.
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
     * Gather included files and its size.
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
     * Gather input get
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
     * Gather input post
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
     * Gather input session
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
    }// gatherInputSession


    /**
     * Get microtime.
     * 
     * @param bool $at_start Set to `true` if this `microtime()` is get at the very beginning of the app. This can allow newer PHP version to use $_SERVER['REQUEST_TIME_FLOAT'];
     * @return float Return `microtime()` in float.
     */
    public function getMicrotime($at_start = false)
    {
        if ($at_start === true && is_array($_SERVER) && array_key_exists('REQUEST_TIME_FLOAT', $_SERVER)) {
            return floatval($_SERVER['REQUEST_TIME_FLOAT']);
        }

        return floatval(microtime(true));
    }// getMicrotime


    /**
     * Get log sections in array format.
     * 
     * This can be use with custom rendering or response via AJAX.
     * 
     * @see ProfilerBase:log_sections
     * @since 1.1.6
     * @return array Return associative array of `log_sections` property.
     */
    public function getLogSectionsForResponse()
    {
        // re-format the result to be ready to render in JS. (There is no `print_r()` in JS.)
        foreach ($this->log_sections as $section => $dataArray) {
            if (is_array($dataArray)) {
                foreach ($dataArray as $dataIndex => $dataItem) {
                    if (is_array($dataItem) && array_key_exists('inputvalue', $dataItem)) {
                        $dataItem['inputvalue'] = print_r($dataItem['inputvalue'], true);
                        $this->log_sections[$section][$dataIndex] = $dataItem;
                    }
                }// endforeach;
                unset($dataIndex, $dataItem);
            }
        }

        return $this->log_sections;
    }// getLogSectionsForResponse


    /**
     * Get readable file size.<br>
     * Copy from php quick profiler
     * 
     * @param int $size File size in bytes.
     * @param string $retstring Return string in format. Example `%01d %s`.
     * @return string Return formatted from bytes to readable file size. Example `1.5 GB`.
     */
    public function getReadableFileSize($size, $retstring = null)
    {
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
     * Get readable time.<br>
     * Copy from php quick profiler
     * 
     * @param int $time The timestamp.
     * @return string Return formatted from timestamp to readable time. Example `0.55 ms`.
     */
    public function getReadableTime($time) 
    {
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
     * @param int $sectionKey The array index key of the section for check while displaying in the loop.
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