<?php
/**
 * This file is for displaying db profiler result.
 * 
 * @license http://opensource.org/licenses/MIT MIT
 */


/**
 * A callback class for display DB profiler.
 * 
 * You can copy and use this source code or change from PDO to how you query your database.
 * 
 * @author Vee W.
 */
class rdpDisplayProfilerDb
{


    /**
     * Display profiler db.<br>
     * This part is not accessible by any URL. it can be used via `$Profiler->display()` only.
     */
    public function display()
    {
        /* @var $dbh \PDO */
        list($profiler, $dbh, $data_values) = func_get_args();

        if (is_array($data_values)) {
            if (array_key_exists('time_start', $data_values) && array_key_exists('time_end', $data_values)) {
                echo '<div class="rdprofiler-log-db-timetake">'."\n";
                echo $profiler->getReadableTime(($data_values['time_end']-$data_values['time_start'])*1000);
                echo '</div>'."\n";
            }

            if (array_key_exists('memory_end', $data_values) && array_key_exists('memory_start', $data_values) && is_int($data_values['memory_end']) && is_int($data_values['memory_start'])) {
                echo '<div class="rdprofiler-log-memory">';
                if (isset($number)) {
                    echo $number->fromBytes($data_values['memory_end']-$data_values['memory_start']);
                } else {
                    echo $profiler->getReadableFileSize($data_values['memory_end']-$data_values['memory_start']);
                }
                echo '</div>'."\n";
            }
        }

        if (strpos($data_values['data'], ';') !== false) {
            // prevent sql injection! example: SELECT * FROM table where username = 'john'; DROP TABLE table;' this can execute 2 queries. explode them and just get the first!
            $exp_data = explode(';', str_replace('; ', ';', $data_values['data']));
            $data_values['data'] = $exp_data[0];
        }

        // use try ... catch to prevent any error by EXPLAIN. Example: EXPLAIN SHOW CHARACTER SET; <-- this will throw errors!
        try {
            $sth = $dbh->prepare('EXPLAIN '.$data_values['data']);
            $sth->execute();
            if ($sth) {
                echo '<div class="rdprofiler-log-newrow">'."\n";
                echo '<div class="rdprofiler-log-db-explain">'."\n";
                if (isset($exp_data) && is_array($exp_data)) {
                    foreach ($exp_data as $key => $sqldata) {
                        if ($key != 0 && !empty($sqldata)) {
                            echo htmlspecialchars((string) $sqldata, ENT_QUOTES).' cannot be explain due to it might be SQL injection!<br>'."\n";
                        }
                    }// endforeach;
                    unset($key, $sqldata);
                }
                $result = $sth->fetchAll();
                $sth->closeCursor();
                if ($result) {
                    foreach ($result as $row) {
                        if (is_array($row) || is_object($row)) {
                            foreach ($row as $key => $val) {
                                echo $key . ' = ' . $val;
                                if (end($result) != $val) {
                                    echo ', ';
                                }
                            }// endforeach;
                        }
                        echo '<br>'."\n";
                    }// endforeach;
                }
                unset($key, $result, $row, $val);
                echo '</div>'."\n";
                echo '</div>'."\n";
            }
            unset($sth);
        } catch (\Exception $e) {
            echo '<div class="rdprofiler-log-newrow">'."\n";
            echo '<div class="rdprofiler-log-db-explain">'."\n";
            echo '</div>'."\n";
            echo '</div>'."\n";
        }
        unset($exp_data);

        if (is_array($data_values) && array_key_exists('call_trace', $data_values)) {
            echo '<div class="rdprofiler-log-newrow">'."\n";
            echo '<div class="rdprofiler-log-db-trace">'."\n";
            echo '<strong>Call trace:</strong><br>'."\n";
            foreach ($data_values['call_trace'] as $trace_item) {
                echo $trace_item['file'].', line '.$trace_item['line'].'<br>'."\n";
            }
            unset($trace_item);
            echo '</div>'."\n";
            echo '</div>'."\n";
        }

        unset($data_values, $dbh, $profiler);
    }// display


}