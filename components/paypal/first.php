<?php

// Suppress DateTime warnings, if not set already
date_default_timezone_set(@date_default_timezone_get());

// Adding Error Reporting for understanding errors properly
error_reporting(E_ALL);
ini_set('display_errors', '1');


// 1. Autoload the SDK Package. This will include all the files and classes to your autoloader
require __DIR__ . '/../../vendor/autoload.php';


// 2. Provide your Secret Key. Replace the given one with your app clientId, and Secret
// https://developer.paypal.com/webapps/developer/applications/myapps
$apiContext = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential(
        //'AWVh9GfpwDqPi69yDKdtQRn46R6XOvfQl3vgM96cv5WB3h7CHWpFMkzMkmiSZJWdseTzDcYTAx1ueN98', // ClientID
        //'EHvpEnid8aKEYVEIAmhlXqA1GAZMuwbwoFrC6NhiKVuSX03FGNo8xWok9jjIb5vBvLZbhWg4OKXXHKc9'      // ClientSecret
        // Sandbox account: daotran210-facilitator@gmail.com
        'AQoBzeGrkzeERt8dk224bokoCpB_8dnlxMm07d8HdIXTdbuCWxQHkhPQyRHyS4aTaYScG4UnDC8CGQUZ', // ClientID
        'EE0Itlb8NeeCDJ3PpMmorgC7ZkZjnRah0BHB-KoaJOWaK20s6_wkdCUiTg6Q4HjWS842KKhRWGog3Dc-' // Client Secret
        )
);

// Step 2.1 : Between Step 2 and Step 3
$apiContext->setConfig(
        array(
            'mode' => 'sandbox', // or use 'live' in production
            'log.LogEnabled' => true,
            'log.FileName' => __DIR__ . '/../../storage/logs/paypal.log',
            'log.LogLevel' => 'DEBUG', // or use 'FINE, INFO, ERROR or WARN' for logging in live environments
            'cache.enabled' => true, // use cache Access Tokens for multiple request uses
        // 'http.CURLOPT_CONNECTTIMEOUT' => 30  // connection timeout
        )
);


// Partner Attribution Id
// Use this header if you are a PayPal partner. Specify a unique BN Code to receive revenue attribution.
// To learn more or to request a BN Code, contact your Partner Manager or visit the PayPal Partner Portal
// $apiContext->addRequestHeader('PayPal-Partner-Attribution-Id', '123123123');


// 3. Lets try to save a credit card to Vault using Vault API mentioned here
// https://developer.paypal.com/webapps/developer/docs/api/#store-a-credit-card
$creditCard = new \PayPal\Api\CreditCard();
$creditCard->setType("visa")
        ->setNumber("4417119669820331")
        ->setExpireMonth("11")
        ->setExpireYear("2019")
        ->setCvv2("012")
        ->setFirstName("Joe")
        ->setLastName("Shopper");


// 4. Make a Create Call and Print the Card
try {
    $creditCard->create($apiContext);
    echo $creditCard;
} catch (\PayPal\Exception\PayPalConnectionException $ex) {
    // This will print the detailed information on the exception. 
    //REALLY HELPFUL FOR DEBUGGING
    echo $ex->getData();
}