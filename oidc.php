<?php

require __DIR__ . '/vendor/autoload.php';

use Facile\OpenIDClient\Client\ClientBuilder;
use Facile\OpenIDClient\Client\Metadata\ClientMetadata;
use Facile\OpenIDClient\Issuer\IssuerBuilder;
use Facile\OpenIDClient\Service\Builder\AuthorizationServiceBuilder;
use Nyholm\Psr7\ServerRequest;
use Sabre\DAV\Client;

$issuer = (new IssuerBuilder())
    ->build('https://localhost:9200/.well-known/openid-configuration');
$clientMetadata = ClientMetadata::fromArray([
    'client_id' => 'xdXOt13JKxym1B1QcEncf2XDkLAexMBFwiT9j6EfhhHFJhs2KM9jbjTmf8JBXE69',
    'client_secret' => 'UBntmLjC2yYCeHwsyj73Uwo9TAaecAetRwMw0xYcvNL9yRdLSUi0hUAHfvCHFeFh',
    'token_endpoint_auth_method' => 'client_secret_basic', // the auth method tor the token endpoint
    'redirect_uris' => [
        'http://localhost/facile/oidc.php',
    ],
]);
$client = (new ClientBuilder())
    ->setIssuer($issuer)
    ->setClientMetadata($clientMetadata)
    ->build();
$tokenSet=null;
// Authorization
$authorizationService = (new AuthorizationServiceBuilder())->build();
$redirectAuthorizationUri = $authorizationService->getAuthorizationUri(
    $client,
    ['scope' => "openid email profile offline_access"]// custom params
);
if (!isset($_REQUEST["code"])) {
    system("xdg-open " . escapeshellarg($redirectAuthorizationUri));
} else {
//    $serverRequest = $_SERVER; // get your server request
    $serverRequest = new ServerRequest($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI']);
//    $factory = new RequestObjectFactory();
//    $requestObject = $factory->create($client, []);
    $callbackParams = $authorizationService->getCallbackParams($serverRequest, $client);
    $tokenSet = $authorizationService->callback($client, $callbackParams);

//    $idToken = $tokenSet->getIdToken(); // Unencrypted id_token, if returned
//    $accessToken = $tokenSet->getAccessToken(); // Access token, if returned
//    $refreshToken = $tokenSet->getRefreshToken(); // Refresh token, if returned
}
$idToken = $tokenSet->getIdToken(); // Unencrypted id_token, if returned
$accessToken = $tokenSet->getAccessToken(); // Access token, if returned
$refreshToken = $tokenSet->getRefreshToken(); // Refresh token, if returned
var_dump($idToken);
var_dump("new line");
var_dump($refreshToken);
var_dump("new line");
var_dump($accessToken);

//$accessToken= 'eyJhbGciOiJQUzI1NiIsImtpZCI6InByaXZhdGUta2V5IiwidHlwIjoiSldUIn0.eyJhdWQiOiJ4ZFhPdDEzSkt4eW0xQjFRY0VuY2YyWERrTEFleE1CRndpVDlqNkVmaGhIRkpoczJLTTlqYmpUbWY4SkJYRTY5IiwiZXhwIjoxNjkzODAwNDY5LCJpYXQiOjE2OTM4MDAxNjksImlzcyI6Imh0dHBzOi8vbG9jYWxob3N0OjkyMDAiLCJqdGkiOiJXNlhjX1JfTGU1VVJjVUdLYWx1Qjh4dVhzZXhTcE9OYyIsImxnLmkiOnsiZG4iOiJBZG1pbiIsImlkIjoib3duQ2xvdWRVVUlEPTE4ZjI4NTgwLWJlNDgtNGY4Yy1hZWRjLTE5ZTJlMjY0NGY0NiIsInVuIjoiYWRtaW4ifSwibGcucCI6ImlkZW50aWZpZXItbGRhcCIsImxnLnQiOiIxIiwic2NwIjoib2ZmbGluZV9hY2Nlc3Mgb3BlbmlkIGVtYWlsIHByb2ZpbGUiLCJzdWIiOiJjZGM2dVh2NWdIdmxJcFRQQHF1UGkxMThPVlhWZURrWk5mSkdLS3VZQ2w1ZF8xaVBGbm1zRnBxN3J4N2NlbFpJMkpaVHplcy1QMG05aW5BZTJRUFF5ZWcifQ.Tnu6Pb8YCtYQUF3TrQAfuaX5FNLvPIdyMZDSHsv-0oAtwtPtADglB4dNchg3h97qeIvyGf2DiiVuR8AmLFLnhKsUcbpxBy89FLWijW1By_NMwl8cpGAexD3584cqdm4yq25BWXZqnx2CTo1kwYE0OcNQjDJFfTOf2WsZkSAc-NICOdYj8YwH0kSkK9BDeG49WDtfa0gho5ZtRgsXv06SEeF61qU8suVXyZxQF40agxaki03HWh0Rx4WxE0IYegy27Bv8zDxWwrvG48ksParyhegbjrTFsOksLsPwfrjF74LduOBZ3iWCy2F3Mk5DPY2IO7WhnMEBJe5JlCAJ89aMCrA-PDwyJfjhg4rNIXoK1IA5wPXfsxS5EzHtNQExHZ9TWYfHsatz1dYS3VxIFnEM48uwefJMy-zWD840GtP3k2G6kXisBF8MamOcAiNOVOuGMzmE_FS4wL4uoXl0YaZ6cBuYe6b5DbJGuhVZthc1hCNTftzyCKPQvcP0VyU0Ubo9KVGc66lCuUIduZMGGzDQmKyzqzNw3swVmKdN4-VwWVhTZjzRt06FEJm3EP5VuusCdswYofr4RXD4yJBXCgk4Lxlaho_WtdP5j7jO8o1QGCES1cIjCshq3m8BWPhNc-htp4g5n6gFp5mXOW-kgEp6zpPyKXpXg2GI4yB6rbacr-M';
$url = 'https://localhost:9200/remote.php/webdav/';
$userClient = new Client([
    'baseUri' => $url,
    'accessToken' => $accessToken,
]);
$httpClient = $userClient->getHttpClient();