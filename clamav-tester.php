<?php
require_once __DIR__ . '/vendor/autoload.php';

$clamAvStream = new \Suez\ClamAV\AppWrite\ClamAV\NetworkStream('localhost');

if ($argc < 2) {
    echo "No enough arguments\n";
    exit;
}

\Symfony\Component\VarDumper\VarDumper::dump($argv[1]);
\Symfony\Component\VarDumper\VarDumper::dump($clamAvStream->instreamFileScan($argv[1]));