<?php

use Nicklasos\ProcessPool;

require dirname(__DIR__) . '/vendor/autoload.php';

$file = __DIR__ . '/child.php';

$pool = new ProcessPool(
    "php $file", // command
    [ // arguments
        'one',
        'two',
        'three',
        'four',
    ],
    2 // number processes (running at same time)
);

$pool->run(function ($arg, $result) {
    echo "{$arg}: {$result}\n";
});
