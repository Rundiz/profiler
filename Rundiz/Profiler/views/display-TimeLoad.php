<?php
// This file is included by display.php
// Set document for helper in IDE.
/* @var $this \Rundiz\Profiler\Profiler */

$summary = $this->getReadableTime(($this->end_time-$this->start_time)*1000);

echo "\n";
?>
            <li id="Section<?php echo $section_to_id; ?>" class="rdprofiler-see-details">
                <a class="rdprofiler-see-details-link" title="<?php echo $summary; ?>"><strong><?php echo $section; ?></strong> <?php echo $summary; ?></a>
                <ul>
                    <li class="rdprofiler-log-summary-row">
                        <div class="rdprofiler-log-data">Data</div>
                        <div class="rdprofiler-log-fileline">File</div>
                        <div class="rdprofiler-log-selftime">Self Time</div>
                        <div class="rdprofiler-log-time">Time</div>
                    </li><!--.rdprofiler-log-summary-row-->
                    <?php
                    if (is_array($data_array) && !empty($data_array)) {
                        foreach ($data_array as $data_key => $data_values) {
                            if (isset($section_to_id) && isset($data_values['matchKey'])) {
                                // if contain matchKey and section to id name. set the based section details id. this appears in these sections: Time Load, Memory Usage.
                                $section_matchKey_id = 'Section'.$section_to_id.'_'.strip_tags(str_replace(['   ', '  ', ' '], '', $data_values['matchKey']));
                            }

                            if (is_numeric($data_key) && is_array($data_values) && array_key_exists('data', $data_values)) {
                                // if contain data then display, otherwise just skip it.
                                if (isset($section_to_id) && isset($data_values['matchKey'])) {
                                    $section_details_id_class = ' id="'.$section_matchKey_id.'_'.$data_key.'" class="'.$section_matchKey_id.'"';
                                } else {
                                    $section_details_id_class = '';
                                }
                    ?> 
                    <li<?php echo $section_details_id_class; ?>>
                        <?php
                        echo "\n";
                        echo rdProfilerIndent(6).'<pre class="rdprofiler-log-data">'."\n".htmlspecialchars(trim(print_r($data_values['data'], true)), ENT_QUOTES)."\n".rdProfilerIndent(6).'</pre>'."\n";

                        if ((isset($data_values['file']) && $data_values['file'] != null) || (isset($data_values['line']) && $data_values['line'] != null)) {
                            // if contain file and line data then display it. this appears in these sections: Logs, Time Load, Memory Usage.
                            echo rdProfilerIndent(6).'<div class="rdprofiler-log-fileline">';
                            if (isset($data_values['file']) && $data_values['file'] != null) {
                                echo htmlspecialchars((string) $data_values['file'], ENT_QUOTES);
                            }
                            if ((isset($data_values['file']) && $data_values['file'] != null) && (isset($data_values['line']) && $data_values['line'] != null)) {
                                echo ', line ';
                            }
                            if (isset($data_values['line']) && $data_values['line'] != null) {
                                echo strip_tags($data_values['line']);
                            }
                            echo '</div>'."\n";
                        }

                        echo rdProfilerIndent(6).'<div class="rdprofiler-log-selftime">';
                        if (isset($data_values['matchKey'])) {
                            $summaryMatchKeyValue = $this->summaryMatchKey($section, $data_values['matchKey'], $data_key);
                            // if contain matchKey in array key then display it. this appears in these sections: Time Load, Memory Usage.
                            if ($summaryMatchKeyValue != null) {
                                echo $summaryMatchKeyValue;
                            }
                            unset($summaryMatchKeyValue);
                        }
                        echo '</div>'."\n";

                        if (isset($data_values['time'])) {
                            // if contain time in array key then display it. this appears only in Time Load section.
                            echo rdProfilerIndent(6).'<div class="rdprofiler-log-time">'.$this->getReadableTime(($data_values['time']-$this->start_time)*1000).'</div>'."\n";
                        }

                        if (isset($data_values['matchKey'])) {
                            // if contain matchKey, now display the matchKey name not their values.
                            echo rdProfilerIndent(6).'<div class="rdprofiler-log-newrow">'."\n";
                            echo rdProfilerIndent(7).'<div class="rdprofiler-log-matchkey-name">'."\n";
                            echo rdProfilerIndent(8).'Match Key is: ';
                            if (isset($section_to_id) && isset($section_matchKey_id)) {
                                echo '<a href="#'.$section_matchKey_id.'" onclick="return RundizProfiler.scrollTo(\'#Section'.$section_to_id.' ul\', \'.'.$section_matchKey_id.'\', jQuery(this));">';
                            }
                            echo htmlspecialchars((string) $data_values['matchKey'], ENT_QUOTES);// the match key name.
                            if (isset($section_to_id) && isset($section_matchKey_id)) {
                                echo '</a>.';
                            }
                            echo "\n";
                            echo rdProfilerIndent(7).'</div>'."\n";
                            echo rdProfilerIndent(6).'</div>'."\n";
                        }
                        ?> 
                    </li>
                    <?php
                                unset($section_details_id_class);
                            }

                            unset($section_matchKey_id);
                        }// endforeach; $data_array
                        // endforeach of loop display each section's log detail.
                        unset($data_key, $data_values);
                    } else {
                    ?> 
                    <li><pre class="rdprofiler-log-data">There is no data to display.</pre></li>
                    <?php } ?> 
                </ul>
            </li><!--#SectionXXXX-->
<?php
unset($summary);