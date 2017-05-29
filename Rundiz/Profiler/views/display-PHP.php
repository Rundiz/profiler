<?php
// get all loaded extensions to display.
$php_loaded_extensions = get_loaded_extensions();
if (isset($php_loaded_extensions) && is_array($php_loaded_extensions)) {
    if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
        sort($php_loaded_extensions, SORT_STRING|SORT_FLAG_CASE);
    } else {
        natsort($php_loaded_extensions);
    }
}

// get all php.ini values.
$all_ini = ini_get_all();
if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
    ksort($all_ini, SORT_NATURAL|SORT_FLAG_CASE);
} else {
    ksort($all_ini, SORT_STRING);
}
?> 
            <li id="SectionPhpVersion" class="rdprofiler-see-details" title="PHP <?php echo phpversion(); ?>">
                <a class="see-details"><strong>PHP</strong> <?php echo phpversion(); ?></a>
                <ul>
                    <?php 
                    echo "\n";
                    if (isset($php_loaded_extensions) && is_array($php_loaded_extensions) && !empty($php_loaded_extensions)) {
                        foreach ($php_loaded_extensions as $phpext) {
                            echo rdProfilerIndent(5).'<li>'."\n";
                            echo rdProfilerIndent(6).'<pre class="rdprofiler-log-data">'.$phpext.'</pre>'."\n";
                            echo rdProfilerIndent(6).'<div class="rdprofiler-log-phpextversion"><small>'.phpversion($phpext).'</small></div>'."\n";
                            echo rdProfilerIndent(5).'</li>'."\n";
                        }// endforeach;
                        unset($phpext);
                    } else {
                        echo rdProfilerIndent(5).'<li><pre class="rdprofiler-log-data">There is no data to display.</pre></li>';
                    }
                    ?> 
                    <li><div class="rdprofiler-log-summary-row"><strong>PHP.ini settings</strong></div></li>
                    <?php
                    echo "\n";
                    if (isset($all_ini) && is_array($all_ini) && !empty($all_ini)) {
                        foreach ($all_ini as $ini_name => $items) {
                            echo rdProfilerIndent(5).'<li>'."\n";
                            echo rdProfilerIndent(6).'<pre class="rdprofiler-log-data">'.$ini_name.'</pre>'."\n";
                            echo rdProfilerIndent(6).'<div class="rdprofiler-log-phpextversion">'."\n";
                            if (is_array($items) && array_key_exists('global_value', $items)) {
                                echo rdProfilerIndent(7).'<strong>Global value:</strong> '.gettype($items['global_value']).' \''.htmlspecialchars($items['global_value'], ENT_QUOTES).'\'<br>'."\n";
                            }
                            if (is_array($items) && array_key_exists('local_value', $items)) {
                                echo rdProfilerIndent(7).'<strong>Local value:</strong> '.gettype($items['local_value']).' \''.htmlspecialchars($items['local_value'], ENT_QUOTES).'\'<br>'."\n";
                            }
                            echo rdProfilerIndent(6).'</div>'."\n";
                            echo rdProfilerIndent(5).'</li>'."\n";
                        }// endforeach;
                        unset($ini_name, $items);
                    } else {
                        echo rdProfilerIndent(5).'<li><pre class="rdprofiler-log-data">There is no data to display.</pre></li>'."\n";
                    }
                    ?> 
                </ul>
            </li><!--#SectionPhpVersion-->
<?php
unset($all_ini, $php_loaded_extensions);