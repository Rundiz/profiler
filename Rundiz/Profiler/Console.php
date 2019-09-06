<?php
/** 
 * Rundiz Profiler
 * 
 * @license http://opensource.org/licenses/MIT
 */


namespace Rundiz\Profiler;

/**
 * Console logger class.
 * 
 * This class works as logger things for profiling.
 * 
 * @package Rundiz\Profiler
 * @version 1.1.3
 * @author Vee W.
 * @access protected Do not access to this class directly by using new \Rundiz\Profiler\Console. Please access this class via Profiler class instead. See $Profiler->Console.
 */
class Console extends \Rundiz\Profiler\ProfilerBase
{


    /**
     * profiler class for call back to the caller class.
     * 
     * @var \Rundiz\Profiler\Profiler 
     */
    protected $Profiler;


    /**
     * class constructor
     * 
     * @param \Rundiz\Profiler\Profiler $profiler
     */
    public function __construct(\Rundiz\Profiler\Profiler $profiler) {
        $this->Profiler = $profiler;
    }// __construct


    /**
     * Database profiling log.
     * 
     * @param string $data SQL query statement. for example: SELECT id FROM people;.
     * @param double $time_start `microtime(true)` at before start the query.
     * @param integer $memory_start `memory_get_usage()` at before start the query. If this was not set then it will not be showing the real memory start. (Required).
     * @todo [rundizprofiler] the "memory" in array key for Database will be remove in future version.
     */
    public function database($data, $time_start, $memory_start = null)
    {
        if (!array_key_exists('Database', $this->Profiler->log_sections)) {
            return ;
        }

        if (!is_string($data)) {
            return ;
        }

        if (!is_float($time_start)) {
            $time_start = $this->Profiler->getMicrotime();
        }

        if ($memory_start === null) {
            // is dev still not using memory start? trigger error.
            trigger_error('You did not enter $memory_start argument for \Rundiz\Profiler\Console->database() method. Please set the $memory_start to memory_get_usage() in your code.', E_USER_NOTICE);
        }
        if (!is_int($memory_start)) {
            $memory_start = memory_get_usage();
        }

        $backtrace = debug_backtrace();
        $trace_items = [];
        if (is_array($backtrace)) {
            foreach ($backtrace as $items) {
                if (is_array($items) && array_key_exists('file', $items) && array_key_exists('line', $items)) {
                    $trace_items[] = ['file' => $items['file'], 'line' => $items['line']];
                }
            }
        }
        unset($backtrace, $items);

        $section_database = $this->Profiler->log_sections['Database'];
        $section_database_data = [];
        $section_database_data[] = [
            'data' => $data,
            'memory_start' => $memory_start,
            'memory' => memory_get_usage(),// @deprecated [rundizprofiler] and will be removed in the future
            'memory_end' => memory_get_usage(),
            'time_start' => $time_start,
            'time_end' => $this->Profiler->getMicrotime(),
            'call_trace' => $trace_items,
        ];
        $section_database = array_merge($section_database, $section_database_data);

        $this->Profiler->log_sections['Database'] = $section_database;
        unset($section_database, $section_database_data, $trace_items);

        //$this->writeLogSections('Database', $data, $file, $line);
    }// database


    /**
     * write normal logs.
     * 
     * @param string $logtype accept debug, info, notice, warning, error, critical, alert, emergency. referrer: http://www.php-fig.org/psr/psr-3/
     * @param mixed $data log data.
     * @param string $file path to file where it calls logger. (optional).
     * @param integer $line number of line in that file that calls logger. (optional).
     */
    public function log($logtype, $data, $file = '', $line = '')
    {
        $data = [
            'logtype' => $logtype,
            'data' => $data,
        ];

        $this->writeLogSections('Logs', $data, $file, $line);
    }// log


    /**
     * write memory usage logs.
     * 
     * @param mixed $data log data.
     * @param string $file path to file where it calls logger. (optional).
     * @param integer $line number of line in that file that calls logger. (optional).
     * @param string|null $matchKey The key to be match at the beginning and end for calculate total time or memory only for this key. (optional). The key must be unique in this section.
     */
    public function memoryUsage($data, $file = '', $line = '', $matchKey = null)
    {
        $this->writeLogSections('Memory Usage', $data, $file, $line, $matchKey);
    }// memoryUsage


    /**
     * register log sections.<br>
     * it is very important! you have to call this before other methods in this class or all log_sections data will be erased.
     * 
     * @param array $sections register log sections by order. suggest: array('Logs', 'Time Load', 'Memory Usage', 'Database', 'Files', 'Session', 'Get', 'Post')
     */
    public function registerLogSections(array $sections)
    {
        if (!empty($this->log_sections)) {
            return false;
        }

        foreach ($sections as $section) {
            $this->Profiler->log_sections[$section] = [];
        }

        return true;
    }// registerLogSections


    /**
     * Write time load logs.
     * 
     * To calculate total time load only for specification functional, use `$matchKey`.<br>
     * Example:
     * <pre>
     * $Profiler->Console->timeload('Before run sleep.', '', '', 'run_sleep');
     * sleep(3);
     * $Profiler->Console->timeload('After run sleep.', '', '', 'run_sleep');
     * </pre>
     * The `$matchKey` must be unique from others.
     * 
     * @param mixed $data Log data.
     * @param string $file Path to file where it calls logger. (optional).
     * @param integer $line Number of line in that file that calls logger. (optional).
     * @param string|null $matchKey The key to be match at the beginning and end for calculate total time or memory only for this key. (optional). The key must be unique in this section.
     */
    public function timeload($data, $file = '', $line = '', $matchKey = null)
    {
        $this->writeLogSections('Time Load', $data, $file, $line, $matchKey);
    }// timeload


    /**
     * Write other sections logs.
     * 
     * @param string $section The section name.
     * @param mixed $data Log data. This can be any type.
     * @param string $file Path to file where it calls logger. (optional).
     * @param integer $line Number of line in that file that calls logger. (optional).
     * @param string|null $matchKey The key to be match at the beginning and end for calculate total time or memory only for this key. (optional). The key must be unique (for start and end) in each section.
     */
    public function writeLogSections($section, $data, $file = '', $line = '', $matchKey = null)
    {
        if (!is_array($this->Profiler->log_sections)) {
            $this->Profiler->log_sections = [];
        }

        if (!array_key_exists($section, $this->Profiler->log_sections)) {
            // if section is not exists in the key means it was not registered, get out of this function.
            return ;
        }

        if (!is_string($matchKey)) {
            $matchKey = null;
        }

        if ($section == 'Logs') {
            if (is_array($data) && array_key_exists('logtype', $data) && array_key_exists('data', $data)) {
                $logtype = $data['logtype'];
                $data = $data['data'];
            }
        }

        $section_data_array = [
            'data' => $data,
            'file' => $file,
            'line' => $line,
        ];
        if ($matchKey !== null) {
            $section_data_array['matchKey'] = $matchKey;
        }
        if ($section == 'Time Load') {
            $section_data_array['time'] = $this->Profiler->getMicrotime();
        }
        if ($section == 'Memory Usage') {
            $section_data_array['memory'] = memory_get_usage();
        }

        if (isset($logtype)) {
            $section_data_array['logtype'] = $logtype;
        }

        $this->Profiler->log_sections[$section][] = $section_data_array;
        unset($section_data_array);
    }// writeLogSections


}