<?php
session_start();

// require the class files. you may no need these if you install via composer.
require dirname(dirname(__DIR__)).'/Rundiz/Profiler/ProfilerBase.php';
require dirname(dirname(__DIR__)).'/Rundiz/Profiler/Profiler.php';

$profiler = new \Rundiz\Profiler\Profiler();
$profiler->Console->registerLogSections(['Logs', 'Memory Usage', 'Session', 'Get', 'Post']);

// -----------------------------------------------------------------------------------------------------
// lazy to write same test on every page, use common test functions
// you can change this to other coding style in your real project.
require __DIR__.'/common-test-functions.php';

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Rundiz\Profiler test</title>
        
        <link rel="stylesheet" href="reset.css">
        <link rel="stylesheet" href="main.css">
    </head>
    <body>
        <h1>Rundiz\Profiler test</h1>
        <p>This page test followings:</p>
        <ul>
            <li>Log</li>
            <li>Memory usage</li>
            <li>Session</li>
            <li>Get</li>
            <li>Post</li>
        </ul>

        <?php
        rdpBasicLogs($profiler);
        rdpMemoryUsage($profiler);

        $profiler->gatherAll();

        if ($_POST) {
            // just checking.
            echo '<pre class="profiler-data-dump-test">'.htmlspecialchars(print_r($profiler->dumptest(), true)).'</pre>';
            echo "\n\n\n";
        } else {
            $_SESSION['session1'] = 'val1';
            $_SESSION['session2'] = 'val2';
            $_SESSION['session3'] = [
                'sub1' => [
                    'sub1.1' => 'val1.1',
                    'sub1.2' => 'val1.2',
                ],
                'sub2' => 'val2',
            ];
        ?> 
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?query1=val1&amp;query2=val2&amp;query3[1]=val3-1&amp;query3[2]=val3-2&amp;query4[1]=val4[1]&amp;query4[2]=val4[2]&amp;query4[2][1]=val4[2][1]">
            <input type="hidden" name="post1" value="val1">
            <input type="hidden" name="post2" value="val2">
            <input type="hidden" name="post3[]" value="val3-1">
            <input type="hidden" name="post3[]" value="val3-2">
            <input type="hidden" name="post4[]" value="val4[1]">
            <input type="hidden" name="post4[]" value="val4[2]">
            <input type="hidden" name="post5[1][1]" value="val5[1][1]">
            <input type="hidden" name="post5[1][2]" value="val5[1][2]">
            <input type="hidden" name="post5[2][1]" value="val5[2][1]">
            <input type="hidden" name="post5[2][2]" value="val5[2][2]">
            <input type="hidden" name="post_html" value="&lt;div&gt;">
            <button type="submit">Begin test!</button>
        </form>
        <?php
        }

        // display profiler window.
        echo $profiler->display();
        ?>
    </body>
</html>