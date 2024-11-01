<?php

const CACHE_FILE_NAME = 'cache.txt';
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
date_default_timezone_set('Europe/Vilnius');

require_once('vendor/autoload.php');


// arunas pangonis
getData(
    'Arunas Pagonis Endokrinologas',
    'https://ipr.esveikata.lt/api/searches/appointments/times?municipalityId=7&organizationId=1000098802&specialistId=1000107565&page=0&size=50'
);

// kardiologai
getData(
    'Kardialogas Tijunaite',
    'https://ipr.esveikata.lt/api/searches/appointments/times?municipalityId=7&organizationId=1000098802&specialistId=1000122226&professionCode=221221&page=0&size=50'
);
getData(
    'Kardialogas Orda',
    'https://ipr.esveikata.lt/api/searches/appointments/times?municipalityId=7&specialistId=1335407585&professionCode=221221&page=0&size=50'
);
getData(
    'Kardialogas Vaitkevicius',
    'https://ipr.esveikata.lt/api/searches/appointments/times?municipalityId=7&specialistId=2508785333&professionCode=221221&page=0&size=50'
);
//
//getData('');


function getData(string $info, string $url)
{
    $cacheFile = md5($url);
    $client = new GuzzleHttp\Client();
    $headers = [
        'Accept' => 'application/json, text/plain, */*',
        'Accept-Language' => 'lt-LT,lt;q=0.9,en-US;q=0.8,en;q=0.7,ru;q=0.6,pl;q=0.5,no;q=0.4',
        'Connection' => 'keep-alive',
        'Cookie' => 'TS01824138=01701735f1ebe336e34a416c1691e0680326e56f5f9b5a6bb841a302954cc4d5d9ae65421827d45ec67615124d0ad1adfdec5b9414; TS01824138=01701735f1ed9d270b05b52c263cc12bca64d2b605f2e9ab3233304eb764f1ce46b07e49ad5305d024b3fb84ad4f80e300ba8424d0',
        'Referer' => 'https://ipr.esveikata.lt/?municipality=7&organization=1000098867',
        'Sec-Fetch-Dest' => 'empty',
        'Sec-Fetch-Mode' => 'cors',
        'Sec-Fetch-Site' => 'same-origin',
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
        'sec-ch-ua' => '"Not/A)Brand";v="8", "Chromium";v="126", "Google Chrome";v="126"',
        'sec-ch-ua-mobile' => '?0',
        'sec-ch-ua-platform' => '"Windows"'
    ];

    $request = $client->request('GET', $url, $headers);
    $response = json_decode($request->getBody(), false);

    $hash = md5(json_encode($response->data));
    $cacheHash = file_exists($cacheFile) ? file_get_contents($cacheFile) : file_put_contents($cacheFile, '');

    if (isset($_GET['test'])) {
        $cacheHash = 'test';
        $response->data = [1, 2];
    }

    if ($hash !== $cacheHash && count($response->data) > 0) {
        file_put_contents($cacheFile, $hash);

        mail(
            'nerjuz@gmail.com',
            'Laisvas laikas [' . $info . '] [' . date('Y-m-d H:i:s') . ']',
            date('Y-m-d H:i:s') . ' Yra laisvu laiku pas gyditoja [' . $info . ']: ' . $url
        );
    }
}

function sendMessage(string $to)
{
    $sid = "AC13630a3d16e9a2fd60047d55b390d8eb";
    $token = "565d785114d4c9129333b93596222d14";
    $client = new Twilio\Rest\Client($sid, $token);

    $client->messages->create(
        $to,
        [
            'from' => 'whatsapp:+14155238886',
            'body' => "Yra laisvu laiku Rojui pas gyditoja: "
        ]
    );
}