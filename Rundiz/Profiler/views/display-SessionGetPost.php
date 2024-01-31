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
                    <?php 
                    if (is_array($data_array) && !empty($data_array)) {
                        foreach ($data_array as $data_key => $data_values) {
                            if (is_numeric($data_key) && is_array($data_values) && array_key_exists('data', $data_values)) {
                                // if contain data then display, otherwise just skip it.
                    ?> 
                    <li>
                        <?php
                        echo "\n";
                        echo rdProfilerIndent(6).'<pre class="rdprofiler-log-data">'."\n".htmlspecialchars(trim(print_r($data_values['data'], true)), ENT_QUOTES)."\n".rdProfilerIndent(6).'</pre>'."\n";

                        if (isset($data_values['inputvalue'])) {
                            echo rdProfilerIndent(6).'<pre class="rdprofiler-log-inputs-value">'."\n".htmlspecialchars(print_r($data_values['inputvalue'], true), ENT_QUOTES)."\n".rdProfilerIndent(6).'</pre>';
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