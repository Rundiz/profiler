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
            array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'')
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
function rdpDbQuery(\PDO $dbh, $statement, array $prepare_data = array(), $console_log = true)
{
    // try to get profiler variable into this function for profiling.
    $profiler = $GLOBALS['profiler'];
    // set time start.
    $time_start = $profiler->getMicrotime();

    // normal db query.
    $sth = $dbh->prepare($statement);
    $sth->execute(array());

    if ($console_log === true) {
        // call tp profiler->Console->database() to profiling this. that's all for database profiling!
        $profiler->Console->database($statement, $time_start);
    }

    // clear and return query result.
    unset($time_start);
    return $sth;
}// rdpDbQuery