<?php
/**
 * This file is for test database profiler functions.
 */


/**
 * connect to db.
 * 
 * @param array $config
 * @return \PDO
 */
function rdpConnectDb(array $config)
{
    // DO NOT enter db config here!
    $host = '';
    $dbname = '';
    $username = '';
    $password = '';

    extract($config);

    try {
        $dbh = new \PDO(
            'mysql:host='.$host.';dbname='.$dbname, $username, $password,
            [
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,// always use exception mode because it is working with try..catch. otherwise the warning mode will not work with try..catch.
            ]
        );

        return $dbh;
    } catch (\PDOException $e) {
        echo '<div class="alert-error">Connection failed: ' . $e->getMessage(). '</div>' . "\n";
    }
}// rdpConnectDb


/**
 * query the data in db. fetch mode is class.
 * 
 * @param \PDO $dbh
 * @param string $statement
 * @param array $prepare_data
 * @return \PDOStatement
 */
function rdpDbQuery(\PDO $dbh, $statement, array $prepare_data = [], $console_log = true)
{
    // try to get profiler variable into this function for profiling.
    /* @var $profiler \Rundiz\Profiler\Profiler */
    $profiler = $GLOBALS['profiler'];
    // set time start.
    $time_start = $profiler->getMicrotime();
    // set memory start.
    $memory_start = memory_get_usage();

    // normal db query.
    $sth = $dbh->prepare($statement);
    $sth->execute([]);

    if ($console_log === true) {
        // call profiler->Console->database() to profiling this. that's all for database profiling!
        $profiler->Console->database($statement, $time_start, $memory_start);
    }

    // clear and return query result.
    unset($memory_start, $time_start);
    return $sth;
}// rdpDbQuery