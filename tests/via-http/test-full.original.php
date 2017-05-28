<?php
/*// remove this line and read important guide below to start test db profiling.
session_start();

// !important!
// please follow these step before you begins test in full options.
// 1. enter your configuration here. this is for connect to db.
$configdb = [];
$configdb['host'] = 'localhost';
$configdb['dbname'] = 'github_rundiz_profiler';
$configdb['username'] = 'user';
$configdb['password'] = 'pass';
// 2. create database where you specify in the config above.
// 3. import data that contain create table and insert in the file 'test-db.sql' into mysql, mariadb server.


// require the class files. you may no need these if you install via composer.
require dirname(dirname(__DIR__)).'/Rundiz/Profiler/ProfilerBase.php';
require dirname(dirname(__DIR__)).'/Rundiz/Profiler/Profiler.php';

$profiler = new \Rundiz\Profiler\Profiler();
$profiler->Console->registerLogSections(['Logs', 'Time Load', 'Memory Usage', 'Database', 'Files', 'Session', 'Get', 'Post']);

// -----------------------------------------------------------------------------------------------------
// lazy to write same test on every page, use common test functions
// you can change this to other coding style in your real project.
require __DIR__.'/common-test-functions.php';

// -----------------------------------------------------------------------------------------------------
// db profiling test.
// include file for test db.
require __DIR__.'/db-functions.php';
// connect to db.
$dbh = rdpConnectDb($configdb);
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
            <li>Time Load</li>
            <li>Memory usage</li>
            <li>Database</li>
            <li>Files</li>
            <li>Session</li>
            <li>Get</li>
            <li>Post</li>
        </ul>

        <?php
        if ($_POST) {
        ?> 
        <h2>Test database</h2>
        <?php
            $sql = 'SELECT * FROM `people` LIMIT 0, 20';
            $sth = rdpDbQuery($dbh, $sql);
            $results = $sth->fetchAll(\PDO::FETCH_OBJ);
        ?> 
        <p>Basic listing; Limits <?php echo $sth->rowCount(); ?> items.</p>
        <?php 
            // list data test.
            if (is_array($results) && !empty($results)) {
        ?> 
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
        <?php
                foreach ($results as $row) {
        ?> 
                <tr>
                    <td><?php echo $row->id; ?></td>
                    <td><?php echo $row->name; ?></td>
                    <td><?php echo $row->address; ?></td>
                    <td><?php echo date('Y-m-d', $row->date); ?></td>
                </tr>
        <?php
                }// endforeach;
        ?> 
            </tbody>
        </table>
        <?php
            }// endif list data test.
            unset($results, $sql, $sth);
        ?> 
        <h3>Test queries</h3>
        <?php
        $queries = [
            'SELECT * FROM people',
            'SELECT * FROM people WHERE name LIKE \'%ben%\'',
            'SELECT * FROM people WHERE id > 10',
            'SELECT * FROM people ORDER BY id DESC',
            'SELECT * FROM people ORDER BY RAND()',
            'SELECT * FROM people WHERE name LIKE \'%no-one-have-this-name%\'',
            'SHOW CHARACTER SET',
            'SHOW TABLES',
            'SET CHARACTER SET utf8',
            'DESCRIBE people',
            'EXPLAIN people',
        ];
        foreach ($queries as $statement) {
            $sth = rdpDbQuery($dbh, $statement);
            echo '<p>Querying: <code>'.htmlspecialchars($statement).'</code> have '.$sth->rowCount().' rows.</p>'."\n";
        }
        unset($queries, $statement);
        ?> 
        <p>End test db profiling.</p>
        <?php
        }// endif $_POST db profiling test.
        ?> 

        <!-- normal profiling (anything else that is not db profiling. -->
        <hr>

        <?php

        rdpBasicLogs($profiler);
        rdpTimeLoadLogs($profiler);
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

        // include class for display profiler db.
        include __DIR__.'/display-profiler-db.php';
        $rdpDisplayProfilerDb = new rdpDisplayProfilerDb();
        // display profiler window.
        echo $profiler->display($dbh, [$rdpDisplayProfilerDb, 'display']);
        // in case that display profiler db is function, you can use the line below.
        // echo $profiler->display($dbh, 'displayProfilerDbFunction');
        unset($dbh, $rdpDisplayProfilerDb);
        ?>
    </body>
</html>
/**/