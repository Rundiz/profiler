<?php
// This file is included by display.php
// Set document for helper in IDE.
/* @var $this \Rundiz\Profiler\Profiler */

$summary = (count($data_array)-2);

echo "\n";
?>
            <li id="Section<?php echo $section_to_id; ?>" class="rdprofiler-see-details">
                <a class="rdprofiler-see-details-link" title="<?php echo $summary; ?>"><strong><?php echo $section; ?></strong> <?php echo $summary; ?></a>
                <ul>
                    <li class="rdprofiler-log-summary-row">
                        <div class="rdprofiler-log-file-totalsize">File</div>
                        <div class="rdprofiler-log-file-totalsize-value">Size</div>
                    </li><!--.rdprofiler-log-summary-row-->
                    <?php if (isset($data_array['total_size'])) { ?> 
                    <li class="rdprofiler-log-summary-row">
                        <div class="rdprofiler-log-file-totalsize">Total size</div>
                        <div class="rdprofiler-log-file-totalsize-value"><?php
                            echo "\n";
                            if (isset($number)) {
                                echo rdProfilerIndent(7).$number->fromBytes($data_array['total_size']);
                            } else {
                                echo rdProfilerIndent(7).$this->getReadableFileSize($data_array['total_size']);
                            }
                            ?> 
                        </div>
                    </li><!--.rdprofiler-log-summary-row-->
                    <?php }// endif; total size ?> 
                    <?php if (isset($data_array['largest_size'])) { ?> 
                    <li class="rdprofiler-log-summary-row">
                        <div class="rdprofiler-log-file-largestsize">Largest size</div>
                        <div class="rdprofiler-log-file-largestsize-value"><?php
                            echo "\n";
                            if (isset($number)) {
                                echo rdProfilerIndent(7).$number->fromBytes($data_array['largest_size']);
                            } else {
                                echo rdProfilerIndent(7).$this->getReadableFileSize($data_array['largest_size']);
                            }
                            ?> 
                        </div>
                    </li><!--.rdprofiler-log-summary-row-->
                    <?php }// endif; largest size ?> 
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

                        if (isset($data_values['size'])) {
                            echo rdProfilerIndent(6).'<div class="rdprofiler-log-filesize">';
                            if (isset($number)) {
                                echo $number->fromBytes($data_values['size']);
                            } else {
                                echo $this->getReadableFileSize($data_values['size']);
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