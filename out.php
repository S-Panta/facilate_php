<?php


require __DIR__ . '/vendor/autoload.php';

use Facile\OpenIDClient\Client\ClientBuilder;
use Facile\OpenIDClient\Client\Metadata\ClientMetadata;
use Facile\OpenIDClient\Issuer\IssuerBuilder;
use Facile\OpenIDClient\Service\Builder\AuthorizationServiceBuilder;
use Nyholm\Psr7\ServerRequest;
use Sabre\DAV\Client;

$accessToken = null;
$issuer = (new IssuerBuilder())
    ->build('https://localhost:9200/.well-known/openid-configuration');
$clientMetadata = ClientMetadata::fromArray([
    'client_id' => 'xdXOt13JKxym1B1QcEncf2XDkLAexMBFwiT9j6EfhhHFJhs2KM9jbjTmf8JBXE69',
    'client_secret' => 'UBntmLjC2yYCeHwsyj73Uwo9TAaecAetRwMw0xYcvNL9yRdLSUi0hUAHfvCHFeFh',
    'token_endpoint_auth_method' => 'client_secret_basic', // the auth method tor the token endpoint
    'redirect_uris' => [
        'http://localhost/facile/out.php',
    ],
]);

$client = (new ClientBuilder())
    ->setIssuer($issuer)
    ->setClientMetadata($clientMetadata)
    ->build();

$authorizationService = (new AuthorizationServiceBuilder())->build();
$redirectAuthorizationUri = $authorizationService->getAuthorizationUri(
    $client,
    ['scope' => "openid email profile offline_access"]// custom params
);

if (isset($_REQUEST["code"])) {
    $serverRequest = new ServerRequest($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI']);
    $callbackParams = $authorizationService->getCallbackParams($serverRequest, $client);
    $tokenSet = $authorizationService->callback($client, $callbackParams);
    $idToken = $tokenSet->getIdToken(); // Unencrypted id_token, if returned
    $accessToken = $tokenSet->getAccessToken(); // Access token, if returned
    $refreshToken = $tokenSet->getRefreshToken(); // Refresh token, if returned
}
$url = 'https://localhost:9200/remote.php/webdav/';
$userClient = new Client([
    'baseUri' => $url,
    'accessToken' => $accessToken,
]);

$userClient->addCurlSetting(CURLOPT_SSL_VERIFYHOST, 0);
$userClient->addCurlSetting(CURLOPT_SSL_VERIFYPEER, 0);
$userClient->addCurlSetting(CURLINFO_HEADER_OUT, true);

$response = $userClient->request('MKCOL', "hello");
$responseStatus = $response['statusCode'];
var_dump($responseStatus);