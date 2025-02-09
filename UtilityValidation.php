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
$validator = new UtilityValidationService();


    // Get raw JSON input from the request
    $rawInput = file_get_contents("php://input");
    $requestData = json_decode($rawInput);

        // Sanitize and validate input
        $validationResult = $validator->sanitizeAndValidate($requestData);
        if (!$validationResult['success']) {
            throw new InvalidArgumentException(json_encode($validationResult['errors']));
        }


try {
    // Make the Post request with data
    $response = $apiClient->request('POST', 'api/v1/Process/Transaction', $validationResult);

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
