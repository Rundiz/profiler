<?php
/**
 * This file is for common test functions that will work in other pages.
 */


/**
 * test basic logs
 * 
 * @param \Rundiz\Profiler\Profiler $profiler
 * @throws Exception
 */
function rdpBasicLogs($profiler)
{
    try {
        $profiler->Console->log('info', ['Test' => 'Test log array data', 'Values' => 'Only have 1 level.']);
        $profiler->Console->log('debug', 'Debug log or normal log data.');
        $profiler->Console->log('info', ['Test' => 'Test log array data', 'Values' => ['Name' => 'Vee', 'Last' => 'W']]);

        $testobj = new \stdClass();
        $testobj->newprop = 'new_val';
        $testobj->arrprop = ['this' => 'is array', 'value' => ['array' => 'value of object property']];
        $profiler->Console->log('notice', $testobj, __FILE__, __LINE__);
        unset($testobj);

        $profiler->Console->log('warning', 'End log test. Start throwing errors.');
        throw new Exception('Some thing error happens');
    } catch (Exception $ex) {
        $profiler->Console->log('error', 'Something error here. '.$ex->getMessage().' '.$ex->getFile().': '.$ex->getLine(), __FILE__, __LINE__);
        $profiler->Console->log('critical', 'Something critical here. '.$ex->getMessage().' '.$ex->getFile().': '.$ex->getLine(), __FILE__, __LINE__);
        $profiler->Console->log('alert', 'Something alert here. '.$ex->getMessage().' '.$ex->getFile().': '.$ex->getLine(), __FILE__, __LINE__);
        $profiler->Console->log('emergency', 'Something emergency here. '.$ex->getMessage().' '.$ex->getFile().': '.$ex->getLine(), __FILE__, __LINE__);
    }
}// rdpBasicLogs


/**
 * test memory usage logs
 * 
 * @param \Rundiz\Profiler\Profiler $profiler
 */
function rdpMemoryUsage($profiler)
{
    $data = '';
    $long_str = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam gravida lacinia sapien id interdum. Ut diam lacus, maximus non augue vel, malesuada tristique turpis. Donec non porta ipsum. Suspendisse non tincidunt leo. Aliquam sed volutpat odio, et suscipit velit. Mauris fermentum ut ex sit amet sagittis. Mauris egestas molestie eleifend. In condimentum eu diam gravida scelerisque. Fusce nec erat vel augue commodo pharetra efficitur quis orci. Donec vitae libero venenatis, consequat velit quis, convallis libero. Cras lectus magna, consectetur vitae dolor ac, luctus ornare felis. Integer nec auctor nulla. Nulla facilisi. Pellentesque commodo interdum felis, ac sollicitudin tellus cursus lobortis. Nulla porta, erat eu aliquam auctor, mi ipsum egestas risus, volutpat vehicula sapien ipsum eu quam. Nunc ut neque sit amet elit dapibus elementum sed ornare tellus. Phasellus blandit turpis risus, eget condimentum odio luctus in. Nullam consectetur lacus sodales nunc tincidunt, eget malesuada eros tristique. Suspendisse elementum augue a quam ultrices sodales. Donec porta augue gravida tortor consectetur, non congue tellus porttitor. In hac habitasse platea dictumst. Maecenas non imperdiet ipsum. Suspendisse auctor luctus ligula nec vestibulum. Sed feugiat magna ac purus consectetur, ac ultricies augue convallis. Donec vel felis et libero eleifend volutpat. Mauris pellentesque nec sem vitae cursus. Vestibulum egestas, risus vel tincidunt vehicula, est dui pellentesque nisi, in facilisis erat odio ac arcu. Vivamus ac ante metus. Proin pulvinar rutrum ligula, eget rutrum velit mollis id. Praesent lacinia purus et risus consectetur sodales. Duis lectus mauris, varius eu pellentesque sit amet, ultrices maximus est. Mauris mattis dapibus nisl ut placerat. Aliquam maximus egestas commodo. Vestibulum commodo mi magna, ut suscipit elit dapibus eget. Fusce vitae est quis ex feugiat maximus ac eget massa. Vestibulum porttitor urna eget diam finibus, nec porttitor est egestas. Sed sem massa, mattis sit amet pretium et, laoreet id ante.';

    // use debug back trace just for set file and line where it call this function.
    $backtrace = debug_backtrace();
    if (is_array($backtrace)) {
        foreach ($backtrace as $items) {
            if (is_array($items) && array_key_exists('file', $items) && array_key_exists('line', $items)) {
                $file = $items['file'];
                $line = $items['line'];
                break;
            }
        }
    }
    unset($backtrace, $items);

    $profiler->Console->memoryUsage('Before loop memory usage at file:'.__FILE__.': '.__LINE__, $file, $line, 'commontest_memoryusage_loop');

    for ($i = 0; $i <= 10; $i++) {
        $data .= $long_str . "\r\n\r\n";
        $profiler->Console->memoryUsage('Loop round '.$i.' memory usage log at file:'.__FILE__.': '.__LINE__, $file, $line);
    }

    $profiler->Console->memoryUsage('After loop memory usage at file:'.__FILE__.': '.__LINE__, $file, $line, 'commontest_memoryusage_loop');


    unset($data);
}// rdpMemoryUsage


/**
 * test time load logs
 * 
 * @param \Rundiz\Profiler\Profiler $profiler
 */
function rdpTimeLoadLogs($profiler)
{
    $profiler->Console->timeload('Time at begins of time load logs test.');

    $file = '';
    $line = '';
    $backtrace = debug_backtrace();
    if (is_array($backtrace)) {
        foreach ($backtrace as $items) {
            if (is_array($items) && array_key_exists('file', $items) && array_key_exists('line', $items)) {
                $file = $items['file'];
                $line = $items['line'];
                break;
            }
        }
    }
    unset($backtrace, $items);

    $profiler->Console->timeload('Time taken to this line '.__FILE__.': '.__LINE__, $file, $line);
    $profiler->Console->timeload('Time taken to this line '.__FILE__.': '.__LINE__, $file, $line);
    $profiler->Console->timeload('Time taken to this line '.__FILE__.': '.__LINE__, $file, $line);
    $profiler->Console->timeload('Before usleep. Time taken to this line '.__FILE__.': '.__LINE__, $file, $line, 'commontest_usleep');
    usleep(100000);
    $profiler->Console->timeload('After usleep. Time taken to this line '.__FILE__.': '.__LINE__, $file, $line, 'commontest_usleep');
    $profiler->Console->timeload('Time taken to this line '.__FILE__.': '.__LINE__, $file, $line);
}// rdpTimeLoadLogs