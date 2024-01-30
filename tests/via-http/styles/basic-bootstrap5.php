<?php

// require the class files. you may no need these if you install via composer.
require dirname(dirname(dirname(__DIR__))).'/Rundiz/Profiler/ProfilerBase.php';
require dirname(dirname(dirname(__DIR__))).'/Rundiz/Profiler/Profiler.php';

$profiler = new \Rundiz\Profiler\Profiler();
$profiler->Console->registerLogSections(['Logs', 'Time Load', 'Memory Usage', 'Files']);

// -----------------------------------------------------------------------------------------------------
// lazy to write same test on every page, use common test functions
// you can change this to other coding style in your real project.
require dirname(__DIR__).'/common-test-functions.php';

// css doc https://getbootstrap.com/docs/5.2/getting-started/introduction/
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Rundiz\Profiler test</title>
        
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    </head>
    <body>
        <h1>Rundiz\Profiler test</h1>
        <p>This page test followings:</p>
        <ul>
            <li>Log</li>
            <li>Time Load</li>
            <li>Memory usage</li>
            <li>Files</li>
        </ul>

        <?php
        rdpBasicLogs($profiler);
        rdpTimeLoadLogs($profiler);
        rdpMemoryUsage($profiler);

        $profiler->gatherAll();

        // just checking.
        echo '<pre class="profiler-data-dump-test">'.htmlspecialchars(print_r($profiler->dumptest(), true)).'</pre>';
        echo "\n\n\n";

        // display profiler window.
        echo $profiler->display();
        ?>
    </body>
</html>