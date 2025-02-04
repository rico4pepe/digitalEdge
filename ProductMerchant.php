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

$auth = new Authentication($apiKey, $privateKeyPath);
$apiClient = new ApiClient($myhttpClient, $auth, $logger, $config);

// Get parameters from request (GET or POST)
$merchantCode = isset($_REQUEST['merchantCode']) ? $_REQUEST['merchantCode'] : '12345';
$currencyId = isset($_SERVER['HTTP_X_CURRENCY']) ? $_SERVER['HTTP_X_CURRENCY'] : null;
$currencyCode = isset($_SERVER['HTTP_X_CODE']) ? $_SERVER['HTTP_X_CODE'] : null;

// Build request headers
$headers = [];
if ($currencyId) {
    $headers['X-Currency-Id'] = $currencyId;
}
if ($currencyCode) {
    $headers['X-Currency-Code'] = $currencyCode;
}

try {
    // Make the GET request with headers
    $response = $apiClient->request('GET', "api/v1/Product/Merchant/{$merchantCode}", $headers);

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
