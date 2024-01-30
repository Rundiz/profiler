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

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Rundiz\Profiler test</title>
        
        <link rel="stylesheet" href="../reset.css">
        <link rel="stylesheet" href="../main.css">
        <style>
            html {
                box-sizing: border-box;
                height: 100%;
            }
            body {
                box-sizing: border-box;
            }
            .page-footer {
                background-color: #ffffcc;
                box-sizing: border-box;
                display: flex;
                grid-column-start: 2;
                grid-row-end: 3;
                grid-row-start: 2;
            }
            .page-wrapper {
                box-sizing: border-box;
                display: grid;
                grid-template-columns: auto;
                grid-template-rows: auto 1.656rem;
                min-height: calc(100vh - 52px);/* 52 is (20 on top + 32 on bottom) */
                position: relative;
            }
            .page-wrapper > main {
                grid-column-start: 1;
                grid-row-start: 1;
            }
            .page-wrapper > .page-footer {
                grid-column-start: 1;
                grid-row-start: 2;
            }
        </style>
    </head>
    <body>
        <div class="page-wrapper">
            <main>
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
                ?> 
                <div id="toggle-height-element">
                    Toggle this <button type="button" onclick="return rdprofilerDemoFooterToggleHeight();">button</button> to make this element very height.
                </div>
            </main>
            <footer class="page-footer">
                This page footer should be always appear at the bottom. It is not sticky bottom but will not be covered by the profiler bar.
            </footer>
        </div><!--.page-wrapper-->
        <?php
        // display profiler window.
        echo $profiler->display();
        ?>
        <script>
            function rdprofilerDemoFooterToggleHeight() {
                let $ = jQuery.noConflict();
                var active = $('#toggle-height-element').toggleClass('active').hasClass('active');
                $('#toggle-height-element').css('height', !active ? 'auto' : '900px');
            }// rdprofilerDemoFooterToggleHeight
        </script>
    </body>
</html>