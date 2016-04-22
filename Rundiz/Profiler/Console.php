<?php
/** 
 * @package Rundiz\Profiler
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */


namespace Rundiz\Profiler;

/**
 * Console logger class.<br>
 * Do not access to this class directly by using new \Rundiz\Profiler\Console. Please access this class via Profiler class instead. See $profiler->Console.
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
     * database profiling log.
     * 
     * @param string $data sql query statement. for example: SELECT id FROM people;.
     * @param integer $time_start microtime at before start the query.
     */
    public function database($data, $time_start)
    {
        if (!array_key_exists('Database', $this->Profiler->log_sections)) {
            return ;
        }

        $backtrace = debug_backtrace();
        $trace_items = array();
        if (is_array($backtrace)) {
            foreach ($backtrace as $items) {
                if (is_array($items) && array_key_exists('file', $items) && array_key_exists('line', $items)) {
                    $trace_items[] = array('file' => $items['file'], 'line' => $items['line']);
                }
            }
        }
        unset($backtrace, $items);

        $section_database = $this->Profiler->log_sections['Database'];
        $section_database_data = array();
        $section_database_data[] = array(
            'data' => $data,
            'memory' => memory_get_usage(),
            'time_start' => $time_start,
            'time_end' => $this->Profiler->getMicrotime(),
            'call_trace' => $trace_items,
        );
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
        $data = array(
            'logtype' => $logtype,
            'data' => $data,
        );

        $this->writeLogSections('Logs', $data, $file, $line);
    }// log


    /**
     * write memory usage logs.
     * 
     * @param mixed $data log data.
     * @param string $file path to file where it calls logger. (optional).
     * @param integer $line number of line in that file that calls logger. (optional).
     */
    public function memoryUsage($data, $file = '', $line = '')
    {
        $this->writeLogSections('Memory Usage', $data, $file, $line);
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
            $this->Profiler->log_sections[$section] = array();
        }

        return true;
    }// registerLogSections


    /**
     * write time load logs.
     * 
     * @param mixed $data log data.
     * @param string $file path to file where it calls logger. (optional).
     * @param integer $line number of line in that file that calls logger. (optional).
     */
    public function timeload($data, $file = '', $line = '')
    {
        $this->writeLogSections('Time Load', $data, $file, $line);
    }// timeload


    /**
     * write other sections logs.
     * 
     * @param string $section the section name.
     * @param mixed $data log data. this can be any type.
     * @param string $file path to file where it calls logger. (optional).
     * @param integer $line number of line in that file that calls logger. (optional).
     */
    public function writeLogSections($section, $data, $file = '', $line = '')
    {
        if (!is_array($this->Profiler->log_sections)) {
            $this->Profiler->log_sections = array();
        }

        if (!array_key_exists($section, $this->Profiler->log_sections)) {
            $this->Profiler->log_sections[$section] = array();
        }

        if ($section == 'Logs') {
            if (is_array($data) && array_key_exists('logtype', $data) && array_key_exists('data', $data)) {
                $logtype = $data['logtype'];
                $data = $data['data'];
            }
        }

        $section_data_array = array(
            'data' => $data,
            'file' => $file,
            'line' => $line,
            'time' => $this->Profiler->getMicrotime(),
            'memory' => memory_get_usage(),
        );

        if (isset($logtype)) {
            $section_data_array = array_merge($section_data_array, array('logtype' => $logtype));
        }

        $this->Profiler->log_sections[$section][] = $section_data_array;
        unset($section_data_array);
    }// writeLogSections


}