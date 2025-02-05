<?php

// Require all dependencies
require_once 'Config.php';
require_once 'Logger.php';
require_once 'MyHttpClient.php';
require_once 'ApiClient.php';
require_once 'Authentication.php';

// Initialize components
$config = new Config();
$logger = new Logger();
$myhttpClient = new MyHttpClient();

// Get API credentials from Config
$apiKey = $config->get('api.apiKey');
$privateKeyPath = $config->get('api.privateKeyPath');

$auth = new Authentication($apiKey, $privateKeyPath, $logger);
$apiClient = new ApiClient($myhttpClient, $auth, $logger, $config);

// Generate the nonce
$nonce = $auth->generateNonce();

// Initialize parameters from the request (with default values)
$currencyId = $_GET['currencyId'] ?? null;
$currencyCode = $_GET['currencyCode'] ?? null;

// Build request data (only currency headers, since X-Nonce goes in headers)
$requestData = [
    'currencyId' => $currencyId,
    'currencyCode' => $currencyCode
];

// Build the endpoint with the actual X-Nonce value
$endpoint = "api/v1/Voucher/Nonce/{$nonce}";

try {
    // Make the GET request
    $response = $apiClient->request('GET', $endpoint, $requestData);

    // Return API response
    header('Content-Type: application/json');
    echo $response;
} catch (Exception $e) {
    // Return error response
    header('Content-Type: application/json');
    echo json_encode([
        "success" => false,
        "statusCode" => 500,
        "message" => "An error occurred: " . $e->getMessage(),
        "data" => null
    ]);
}
