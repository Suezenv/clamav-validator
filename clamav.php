<?php

use Suez\ClamAV\AppWrite\ClamAV\NetworkStream;
use Symfony\Component\VarDumper\VarDumper;

include __DIR__.'/vendor/autoload.php';

$n = new NetworkStream();

if ($argv[1] ?? false) {
    VarDumper::dump($n->instreamFileScan($argv[1]));
}

