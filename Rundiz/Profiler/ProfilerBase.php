<?php
/** 
 * Rundiz Profiler
 * 
 * @license http://opensource.org/licenses/MIT
 */


namespace Rundiz\Profiler;

/**
 * Profiler based class.
 * 
 * For access common properties between console and profiler class.
 * 
 * @package Rundiz\Profiler
 * @author Vee W.
 */
abstract class ProfilerBase
{


    /**
     * log sections.<br>
     * this property is for storing logs data for profiling process and display it in display page.
     * 
     * @var array the array of log sections. the values should be: <br>
     * <code>array('Logs' => array(0 => array('logtype' => 'debug', 'data' => 'data in any type', 'file' => 'file to log', 'line'=> 'line of file to log', 'time' => 'time used', 'memory' => 'memory used')))</code>, <br>
     * <code>array('Time Load' => array(0 => array('data' => 'data in any type', 'file' => 'file to log', 'line'=> 'line of file to log', 'time' => 'time used', 'memory' => 'memory used')))</code>, <br>
     * and more...<br>
     * the array key logtype is for Logs only.<br>
     * the array key file and line is not required, time and memory key is for display only.<br>
     * the sections suggest are: Logs, Time Load, Memory Usage, Database and automatic sections are: Files, Session, Get, Post
     */
    protected $log_sections = [];


    /**
     * max memory usage.
     * @var integer number of bytes of max memory usage.
     */
    protected $max_memory_usage;


    /**
     * application start time.
     * @var float the value is microtime of start time.
     */
    protected $start_time;


    /**
     * application end time.
     * @var float the value is microtime of end time.
     */
    protected $end_time;


    /**
     * Reset everything to cleanup.
     */
    protected function reset()
    {
        $this->log_sections = [];
        $this->max_memory_usage = null;
        $this->start_time = null;
        $this->end_time = null;
    }// reset


}