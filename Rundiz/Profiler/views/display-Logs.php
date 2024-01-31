<?php
// This file is included by display.php
// Set document for helper in IDE.
/* @var $this \Rundiz\Profiler\Profiler */

$summary = count($data_array);

echo "\n";
?>
            <li id="Section<?php echo $section_to_id; ?>" class="rdprofiler-see-details">
                <a class="rdprofiler-see-details-link" title="<?php echo $summary; ?>"><strong><?php echo $section; ?></strong> <?php echo $summary; ?></a>
                <ul>
                    <li class="rdprofiler-log-summary-row">
                        <table class="rdprofiler-log-logtypes">
                            <tr>
                                <td class="rdprofiler-log-logtype rdprofiler-logtype-debug">Debug (<?php echo $this->countTotalLogType('debug'); ?>)</td>
                                <td class="rdprofiler-log-logtype rdprofiler-logtype-info">Info (<?php echo $this->countTotalLogType('info'); ?>)</td>
                                <td class="rdprofiler-log-logtype rdprofiler-logtype-notice">Notice (<?php echo $this->countTotalLogType('notice'); ?>)</td>
                                <td class="rdprofiler-log-logtype rdprofiler-logtype-warning">Warning (<?php echo $this->countTotalLogType('warning'); ?>)</td>
                                <td class="rdprofiler-log-logtype rdprofiler-logtype-error">Error (<?php echo $this->countTotalLogType('error'); ?>)</td>
                                <td class="rdprofiler-log-logtype rdprofiler-logtype-critical">Critical (<?php echo $this->countTotalLogType('critical'); ?>)</td>
                                <td class="rdprofiler-log-logtype rdprofiler-logtype-alert">Alert (<?php echo $this->countTotalLogType('alert'); ?>)</td>
                                <td class="rdprofiler-log-logtype rdprofiler-logtype-emergency">Emergency (<?php echo $this->countTotalLogType('emergency'); ?>)</td>
                            </tr>
                        </table>
                    </li><!--.rdprofiler-log-summary-row-->
                    <?php 
                    if (is_array($data_array) && !empty($data_array)) {
                        foreach ($data_array as $data_key => $data_values) {
                            if (is_numeric($data_key) && is_array($data_values) && array_key_exists('data', $data_values)) {
                                // if contain data then display, otherwise just skip it.
                    ?> 
                    <li>
                        <?php
                        echo "\n";
                        if (isset($data_values['logtype'])) {
                            // if log type exists. this is only for Logs section.
                            echo rdProfilerIndent(6).'<div class="rdprofiler-log-logtype rdprofiler-logtype-'.strip_tags($data_values['logtype']).'">'.strip_tags(ucfirst($data_values['logtype'])).'</div>'."\n";
                        }

                        echo rdProfilerIndent(6).'<pre class="rdprofiler-log-data">'."\n".htmlspecialchars(trim(print_r($data_values['data'], true)), ENT_QUOTES)."\n".rdProfilerIndent(6).'</pre>'."\n";

                        if ((isset($data_values['file']) && $data_values['file'] != null) || (isset($data_values['line']) && $data_values['line'] != null)) {
                            // if contain file and line data then display it. this appears in these sections: Logs, Time Load, Memory Usage.
                            echo rdProfilerIndent(6).'<div class="rdprofiler-log-fileline">';
                            if (isset($data_values['file']) && $data_values['file'] != null) {
                                echo htmlspecialchars($data_values['file'], ENT_QUOTES);
                            }
                            if ((isset($data_values['file']) && $data_values['file'] != null) && (isset($data_values['line']) && $data_values['line'] != null)) {
                                echo ', line ';
                            }
                            if (isset($data_values['line']) && $data_values['line'] != null) {
                                echo strip_tags($data_values['line']);
                            }
                            echo '</div>'."\n";
                        }
                        ?> 
                    </li>
                    <?php 
                            }
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