<?php

$argument = $argv[1];

$params = [
    'one' => 'Hello',
    'two' => ', ',
    'three' => 'World',
];

echo isset($params[$argument]) ? $params[$argument] : '!';
