<?php

// Require all dependencies
require_once 'Config.php';
require_once 'Logger.php';
require_once 'MyHttpClient.php';
require_once 'ApiClient.php';
require_once 'Authentication.php';

// Initialize componentsph
$config = new Config();
$logger = new Logger();
$myhttpClient = new MyHttpClient();
$auth = new Authentication(apiKey: $apiKey, privateKeyPath: $privateKeyPath);
$apiClient = new ApiClient($myhttpClient, $auth, $logger, $config);

// Get merchantCode from request (if provided), otherwise use a default
$merchantCode = isset($_GET['merchantCode']) ? $_GET['merchantCode'] : '12345';

try {
    // Make the GET request
    $response = $apiClient->request('GET', 'api/v1/Product/Merchant/{code}', ['code' => $merchantCode]);

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
