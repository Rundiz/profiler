<?php


require (dirname(__DIR__)).'/Rundiz/Profiler/ProfilerBase.php';
require (dirname(__DIR__)).'/Rundiz/Profiler/Profiler.php';


$profiler = new \Rundiz\Profiler\Profiler();
$profiler->Console->registerLogSections(array('Logs', 'Time Load', 'Memory Usage', 'Files'));
$profiler->Console->log('debug', 'Debug log or normal log data.');
$profiler->Console->log('info', array('Simple' => 'This array', 'Array' => 'Only have 1 level.'));
$profiler->Console->log('notice', 'Notice level of log.');
$profiler->Console->log('warning', 'Warning level of log.');
$profiler->Console->log('error', 'Error level of log.');
$profiler->Console->log('critical', 'Critical level of log.');
$profiler->Console->log('alert', 'Alert level of log.');
$profiler->Console->log('emergency', 'Emergency level of log.');

$profiler->Console->timeload('Time taken to this line '.__FILE__.': '.__LINE__);
usleep(100000);
$profiler->Console->timeload('After sleep. Time taken to this line '.__FILE__.': '.__LINE__);

$data = '';
$long_str = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam gravida lacinia sapien id interdum. Ut diam lacus, maximus non augue vel, malesuada tristique turpis. Donec non porta ipsum. Suspendisse non tincidunt leo. Aliquam sed volutpat odio, et suscipit velit. Mauris fermentum ut ex sit amet sagittis. Mauris egestas molestie eleifend. In condimentum eu diam gravida scelerisque. Fusce nec erat vel augue commodo pharetra efficitur quis orci. Donec vitae libero venenatis, consequat velit quis, convallis libero. Cras lectus magna, consectetur vitae dolor ac, luctus ornare felis. Integer nec auctor nulla. Nulla facilisi. Pellentesque commodo interdum felis, ac sollicitudin tellus cursus lobortis. Nulla porta, erat eu aliquam auctor, mi ipsum egestas risus, volutpat vehicula sapien ipsum eu quam. Nunc ut neque sit amet elit dapibus elementum sed ornare tellus. Phasellus blandit turpis risus, eget condimentum odio luctus in. Nullam consectetur lacus sodales nunc tincidunt, eget malesuada eros tristique. Suspendisse elementum augue a quam ultrices sodales. Donec porta augue gravida tortor consectetur, non congue tellus porttitor. In hac habitasse platea dictumst. Maecenas non imperdiet ipsum. Suspendisse auctor luctus ligula nec vestibulum. Sed feugiat magna ac purus consectetur, ac ultricies augue convallis. Donec vel felis et libero eleifend volutpat. Mauris pellentesque nec sem vitae cursus. Vestibulum egestas, risus vel tincidunt vehicula, est dui pellentesque nisi, in facilisis erat odio ac arcu. Vivamus ac ante metus. Proin pulvinar rutrum ligula, eget rutrum velit mollis id. Praesent lacinia purus et risus consectetur sodales. Duis lectus mauris, varius eu pellentesque sit amet, ultrices maximus est. Mauris mattis dapibus nisl ut placerat. Aliquam maximus egestas commodo. Vestibulum commodo mi magna, ut suscipit elit dapibus eget. Fusce vitae est quis ex feugiat maximus ac eget massa. Vestibulum porttitor urna eget diam finibus, nec porttitor est egestas. Sed sem massa, mattis sit amet pretium et, laoreet id ante.';
$long_str = $long_str . "\r\n" . $long_str . "\r\n" . $long_str . "\r\n" . $long_str . "\r\n" . $long_str . "\r\n" . $long_str . "\r\n" . $long_str . "\r\n" . $long_str . "\r\n" . $long_str . "\r\n" . $long_str;
for ($i = 0; $i <= 2; $i++) {
    $data .= $long_str . "\r\n\r\n";
    $data .= $long_str . "\r\n\r\n";
    $data .= $long_str . "\r\n\r\n";
    $data .= $long_str . "\r\n\r\n";
    $profiler->Console->memoryUsage('Loop round '.$i.' memory usage log at file:'.__FILE__.': '.__LINE__);
}
unset($data, $long_str);

$profiler->gatherAll();
$re_formatted_profiler_results = array();
$dump_results = $profiler->dumptest();
if (is_array($dump_results) && array_key_exists('log_sections', $dump_results)) {
    foreach ($dump_results['log_sections'] as $section => $section_items) {
        $re_formatted_profiler_results[$section] = array();
            if (is_array($section_items)) {
                foreach ($section_items as $key => $items) {
                    if ($section == 'Logs') {
                        $re_formatted_profiler_results[$section][$key]['logtype'] = $items['logtype'];
                    } elseif ($section == 'Time Load') {
                        $re_formatted_profiler_results[$section][$key]['time'] = $items['time'];
                    } elseif ($section == 'Memory Usage') {
                        $re_formatted_profiler_results[$section][$key]['memory'] = $items['memory'];
                    } elseif ($section == 'Files') {
                        if (is_numeric($key)) {
                            $re_formatted_profiler_results[$section][$key]['file'] = $items['data'];
                        }
                    }
                }
                unset($items, $key);
            }
        
    }
    unset($section, $section_items);
}
unset($dump_results);
print_r($re_formatted_profiler_results);


unset($profiler);