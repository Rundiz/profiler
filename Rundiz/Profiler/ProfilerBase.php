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
 * @version 1.1.11
 */
abstract class ProfilerBase
{


    /**
     * Profiler data sections (previous known as log sections).<br>
     * This property is for storing logs data for profiling process and display it in display page.
     * 
     * @var array The array of log sections. the values should be: <br>
     * <code>array('Logs' => array(0 => array('logtype' => 'debug', 'data' => 'data in any type', 'file' => 'file to log', 'line'=> 'line of file to log', 'time' => 'time used', 'memory' => 'memory used')))</code>, <br>
     * <code>array('Time Load' => array(0 => array('data' => 'data in any type', 'file' => 'file to log', 'line'=> 'line of file to log', 'time' => 'time used', 'memory' => 'memory used')))</code>, <br>
     * and more...<br>
     * The array key logtype is for Logs only.<br>
     * The array key file and line is not required, time and memory key is for display only.<br>
     * The sections suggest are: Logs, Time Load, Memory Usage, Database and automatic sections are: Files, Session, Get, Post
     */
    protected $log_sections = [];


    /**
     * @var int Number of bytes of max memory usage. This property will be set by `Profiler::gatherAll()`, `ProfilerBase::reset()`.
     */
    protected $max_memory_usage;


    /**
     * @var float The `microtime()` of application start time. This property will be set by `Profiler::__construct()`, `ProfilerBase::reset()`.
     */
    protected $start_time;


    /**
     * @var float The `microtime()` of application end time. This property will be set by `Profiler::gatherAll()`, `ProfilerBase::reset()`.
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