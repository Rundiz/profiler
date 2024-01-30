<?php
session_start();

// require the class files. you may no need these if you install via composer.
require dirname(dirname(__DIR__)).'/Rundiz/Profiler/ProfilerBase.php';
require dirname(dirname(__DIR__)).'/Rundiz/Profiler/Profiler.php';

$profiler = new \Rundiz\Profiler\Profiler();
$profiler->Console->registerLogSections(['Logs', 'Memory Usage', 'Session', 'Get', 'Post']);

// -----------------------------------------------------------------------------------------------------
if (isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) === 'get') {
    $_SESSION['session1'] = 'val1';
    $_SESSION['session2'] = 'val2';
    $_SESSION['session3'] = [
        'sub1' => [
            'sub1.1' => 'val1.1',
            'sub1.2' => 'val1.2',
        ],
        'sub2' => 'val2',
    ];
}
unset($_SESSION['session4'], $_SESSION['session5']);

if (isset($_REQUEST['doing']) && 'ajax' === $_REQUEST['doing']) {
    // if request is AJAX.
    $_SESSION['session4'] = 'val4';
    $_SESSION['session5'] = [
        'cars' => ['brand A', 'brand B'],
        'fuel' => 'full',
    ];

    $profiler->gatherAll();

    header('Content-type: application/json');
    $output = [];
    $output['rundiz-profiler']['logSections'] = $profiler->getLogSectionsForResponse();
    echo json_encode($output);
    exit();
}

// lazy to write same test on every page, use common test functions
// you can change this to other coding style in your real project.
require __DIR__.'/common-test-functions.php';

if (isset($_SERVER['REQUEST_URI'])) {
    $ajaxURL = strtok($_SERVER['REQUEST_URI'], '?');
} else {
    $ajaxURL = '';
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Rundiz\Profiler Input test</title>
        
        <link rel="stylesheet" href="reset.css">
        <link rel="stylesheet" href="main.css">
    </head>
    <body>
        <h1>Rundiz\Profiler Input test</h1>
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
        ?> 
        <script>
            document.addEventListener('DOMContentLoaded', async () => {
                // try to make AJAX request and see profiler data via AJAX.
                const postBody = 'ajaxPost1=value1&ajaxPost2=value2&ajaxPosta[]=avalue1&ajaxPosta[]=avalue2';
                // use JS `fetch()`.
                const response = await fetch('<?php echo $ajaxURL; ?>?doing=ajax&ajax-using=fetch&array[]=item1&array[]=item2', {
                    'body': postBody,
                    'headers': {
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
                    },
                    'method': 'POST',
                })
                .then((response) => {
                    console.debug('JS fetch() completed.');
                    // continue your AJAX tasks.
                })
                .catch((ex) => {
                    // just prevent throwing errors.
                });

                // use `JQuery.ajax()`.
                jQuery.ajax({
                    'url': '<?php echo $ajaxURL; ?>?doing=ajax&ajax-using=jquery&array[]=item1&array[]=item2',
                    'method': 'POST',
                    'data': postBody,
                })
                .then((rawResponse) => {
                    console.log('jQuery.ajax() completed.');
                    // continue your AJAX tasks.
                });
            });
        </script>
        <?php
        } else {
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