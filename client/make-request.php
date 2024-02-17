<?php

/* @var array{url: string, token: string} $config */
$config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => $config['url'],
    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 0,
    CURLOPT_TIMEOUT => 5,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $config['token']
    ],
]);

$response = curl_exec($curl);

curl_close($curl);

file_put_contents(__DIR__ . '/debug.txt', date("Y-m-d H:i:s"));
