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

// css doc https://cdnjs.com/libraries/bulma
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Rundiz\Profiler test</title>
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.5/css/bulma.min.css" integrity="sha256-vK3UTo/8wHbaUn+dTQD0X6dzidqc5l7gczvH+Bnowwk=" crossorigin="anonymous" />
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