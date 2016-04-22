<?php


require __DIR__.'/Autoload.php';

$Autoload = new \Rundiz\Profiler\Tests\Autoload();
$Autoload->addNamespace('Rundiz\\Profiler\\Tests', __DIR__);
$Autoload->addNamespace('Rundiz\\Profiler', dirname(dirname(__DIR__)).'/Rundiz/Profiler');
$Autoload->register();