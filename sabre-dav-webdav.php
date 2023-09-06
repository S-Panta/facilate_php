<?php
require __DIR__ . '/vendor/autoload.php';
use Sabre\DAV\Client;
$settings = array(
    'baseUri' => 'https://localhost:9200/remote.php/dav',
    'userName' => 'admin',
    'password' => 'admin',
);

$client = new Client($settings);
$client->addCurlSetting(CURLOPT_SSL_VERIFYHOST,0);
$client->addCurlSetting(CURLOPT_SSL_VERIFYPEER,0);
$response = $client->request('PROPFIND');
var_dump($response);