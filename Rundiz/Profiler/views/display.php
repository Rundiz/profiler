<?php
if (file_exists(__DIR__.DIRECTORY_SEPARATOR.'functions.php')) {
    include_once __DIR__.DIRECTORY_SEPARATOR.'functions.php';
}

if (class_exists('\\Rundiz\\Number\\Number')) {
    $number = new \Rundiz\Number\Number();
}
?>

<script>
<?php rdprofilerLoadCss(); ?> 

<?php 
echo 'if (typeof jQuery == \'undefined\') {'."\n";
rdprofilerLoadJs('jquery');
echo '}'."\n\n";
rdprofilerLoadJs(); 
?> 
</script>
<div class="rdprofiler">
    <div class="rdprofiler-container">
        <ul class="rdprofiler-log-sections">
            <li><strong><a href="http://rundiz.com" target="vendor" class="highlight">Rundiz</a></strong>\Profiler</li>
            <li title="<?php echo phpversion(); ?>"><strong>PHP</strong> <?php echo phpversion(); ?></li>
            <?php 
            if (is_array($this->log_sections)) {
                foreach ($this->log_sections as $section => $data_array) {
                    if (!is_array($data_array)) {
                        break;
                    }

                    if ($section == 'Logs') {
                        $summary = count($data_array);
                    } elseif ($section == 'Time Load') {
                        $summary = $this->getReadableTime(($this->end_time-$this->start_time)*1000);
                    } elseif ($section == 'Memory Usage') {
                        $summary = $this->max_memory_usage;
                        if (isset($number)) {
                            $summary = $number->fromBytes($summary);
                        } else {
                            $summary = $this->getReadableFileSize($summary);
                        }
                    } elseif ($section == 'Database') {
                        $summary = count($data_array);
                    } elseif ($section == 'Files') {
                        $summary = (count($data_array)-2);
                    } elseif ($section == 'Get' || $section == 'Post' || $section == 'Session') {
                        $summary = (count($data_array));
                    } else {
                        $summary = '';
                    }

                    // li of each section.
                    echo '<li class="rdprofiler-see-details">'."\n";
                    // display section tabs.
                    echo "\t".'<a title="'.$summary.'"><strong>'.$section.'</strong> '.$summary.'</a>'."\n";
                    // ul of section details.
                    echo "\t".'<ul>'."\n";

                    // file have summaries, display them first.
                    if ($section == 'Files' && isset($data_array['total_size'])) {
                        echo "\t\t".'<li class="rdprofiler-log-sumary-row">'."\n";
                        echo "\t\t\t".'<div class="rdprofiler-log-file-totalsize">Total size</div>'."\n";
                        echo "\t\t\t".'<div class="rdprofiler-log-file-totalsize-value">';
                        if (isset($number)) {
                            echo $number->fromBytes($data_array['total_size']);
                        } else {
                            echo $this->getReadableFileSize($data_array['total_size']);
                        }
                        echo '</div>'."\n";
                        echo "\t\t".'</li>'."\n";
                    }
                    if ($section == 'Files' && isset($data_array['largest_size'])) {
                        echo "\t\t".'<li class="rdprofiler-log-sumary-row">'."\n";
                        echo "\t\t\t".'<div class="rdprofiler-log-file-largestsize">Largest size</div>'."\n";
                        echo "\t\t\t".'<div class="rdprofiler-log-file-largestsize-value">';
                        if (isset($number)) {
                            echo $number->fromBytes($data_array['largest_size']);
                        } else {
                            echo $this->getReadableFileSize($data_array['largest_size']);
                        }
                        echo '</div>'."\n";
                        echo "\t\t".'</li>'."\n";
                    }

                    if (is_array($data_array) && !empty($data_array)) {
                        // loop display each section's log detail.
                        foreach ($data_array as $data_key => $data_values) {
                            if (is_numeric($data_key) && is_array($data_values) && array_key_exists('data', $data_values)) {
                                echo "\t\t".'<li>'."\n";

                                if (isset($data_values['logtype'])) {
                                    echo "\t\t\t".'<div class="rdprofiler-log-logtype '.strip_tags($data_values['logtype']).'">'.strip_tags(ucfirst($data_values['logtype'])).'</div>'."\n";
                                }

                                echo "\t\t\t".'<pre class="rdprofiler-log-data">'.  htmlspecialchars(trim(print_r($data_values['data'], true))).'</pre>'."\n";

                                if ((isset($data_values['file']) && $data_values['file'] != null) || (isset($data_values['line']) && $data_values['line'] != null)) {
                                    echo "\t\t\t";
                                    echo '<div class="rdprofiler-log-fileline">';
                                    if (isset($data_values['file']) && $data_values['file'] != null) {
                                        echo htmlspecialchars($data_values['file']);
                                    }
                                    if ((isset($data_values['file']) && $data_values['file'] != null) && (isset($data_values['line']) && $data_values['line'] != null)) {
                                        echo ', line ';
                                    }
                                    if (isset($data_values['line']) && $data_values['line'] != null) {
                                        echo strip_tags($data_values['line']);
                                    }
                                    echo '</div>';
                                    echo "\n";
                                }

                                if ($section == 'Time Load' && isset($data_values['time'])) {
                                    echo "\t\t\t".'<div class="rdprofiler-log-time">'.$this->getReadableTime(($data_values['time']-$this->start_time)*1000).'</div>'."\n";
                                }

                                if ($section == 'Memory Usage' && isset($data_values['memory'])) {
                                    echo "\t\t\t".'<div class="rdprofiler-log-memory">';
                                    if (isset($number)) {
                                        echo $number->fromBytes($data_values['memory']);
                                    } else {
                                        echo $this->getReadableFileSize($data_values['memory']);
                                    }
                                    echo '</div>'."\n";
                                }

                                if ($section == 'Database') {
                                    call_user_func($display_db_function, $this, $dbh, $data_values);
                                }

                                if ($section == 'Files' && isset($data_values['size'])) {
                                    echo "\t\t\t".'<div class="rdprofiler-log-filesize">';
                                    if (isset($number)) {
                                        echo $number->fromBytes($data_values['size']);
                                    } else {
                                        echo $this->getReadableFileSize($data_values['size']);
                                    }
                                    echo '</div>'."\n";
                                }

                                if (($section == 'Get' || $section == 'Post' || $section == 'Session') && isset($data_values['inputvalue'])) {
                                    echo "\t\t\t".'<pre class="rdprofiler-log-inputs-value">'.htmlspecialchars(print_r($data_values['inputvalue'], true)).'</pre>';
                                }

                                echo "\t\t".'</li>'."\n";
                            }
                        }// endforeach $data_array;
                        // endforeach of loop display each section's log detail.
                        unset($data_key, $data_values);
                    } else {
                        echo "\t\t".'<li><pre class="rdprofiler-log-data">There is no data to display.</pre></li>'."\n";
                    }

                    // end ul of section details.
                    echo "\t".'</ul>'."\n";
                    // end li of each section.
                    echo '</li>'."\n";
                }// endforeach $this->log_sections;
                unset($data_array, $section, $summary);
            }
            ?> 
        </ul><!--.rdprofiler-log-sections-->
    </div><!--.rdprofiler-container-->
</div><!--.rdprofiler-->
<?php
unset($number);