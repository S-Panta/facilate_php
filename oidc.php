<?php

use Facile\OpenIDClient\Client\ClientBuilder;
use Facile\OpenIDClient\Issuer\IssuerBuilder;
use Facile\OpenIDClient\Client\Metadata\ClientMetadata;
use Facile\OpenIDClient\Service\Builder\AuthorizationServiceBuilder;
use Facile\OpenIDClient\Service\Builder\UserInfoServiceBuilder;
use Nyholm\Psr7\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;
use Facile\OpenIDClient\RequestObject\RequestObjectFactory;

require __DIR__ . '/vendor/autoload.php';
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

    // Authorization
    $authorizationService = (new AuthorizationServiceBuilder())->build();
    $redirectAuthorizationUri = $authorizationService->getAuthorizationUri(
        $client,
        [] // custom params
    );

if(!isset($_REQUEST["code"])){
    system("xdg-open " . escapeshellarg($redirectAuthorizationUri));
} else {
    $ret = 'https://localost:9200';
    $ret .= $_SERVER['REQUEST_URI'];
//    $serverRequest = $_SERVER; // get your server request
    $serverRequest = new ServerRequest($_SERVER['REQUEST_METHOD'] ?? 'GET', $ret);
//    $factory = new RequestObjectFactory();
//    $requestObject = $factory->create($client, []);
    $callbackParams = $authorizationService->getCallbackParams($serverRequest, $client);
    $tokenSet = $authorizationService->callback($client, $callbackParams);

    $idToken = $tokenSet->getIdToken(); // Unencrypted id_token, if returned
    $accessToken = $tokenSet->getAccessToken(); // Access token, if returned
    $refreshToken = $tokenSet->getRefreshToken(); // Refresh token, if returned

// check if we have an authenticated user
    if ($idToken) {
        $claims = $tokenSet->claims(); // IdToken claims
    } else {
        throw new \RuntimeException('Unauthorized');
}

var_dump($accessToken);
// Refresh token
//    $tokenSet = $authorizationService->refresh($client, $tokenSet->getRefreshToken());


// Get user info
//    $userInfoService = (new UserInfoServiceBuilder())->build();
//    $userInfo = $userInfoService->getUserInfo($client, $tokenSet);
}


//// you can use this uri to redirect the user
//echo "<div id='asa'>".($redirectAuthorizationUri)."</div>";
//
//// Get access token
///*
// * php -> java
// * jvo -> new tab
// *
// *
// *
// *
// */
//
//$randomvar="\nsadasd";
///** @var ServerRequestInterface::class $serverRequest */
//echo '<script type="text/javascript">
////console.log(document.querySelector("body").textContent)
//window.open(document.getElementById("asa").innerText);
//</script>';




