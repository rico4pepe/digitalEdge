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

// Function to validate date format (YYYY-MM-DD)
function isValidDate($date) {
    return preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) && date('Y-m-d', strtotime($date)) === $date;
}

// Get parameters from request
$fromDate = $_GET['fromDate'] ?? null;
$toDate = $_GET['toDate'] ?? null;
$status = $_GET['status'] ?? null;
$currencyId = $_GET['currencyId'] ?? null;
$currencyCode = $_GET['currencyCode'] ?? null;

// Validate required parameters
if (!$fromDate || !$toDate || !$status) {
    header('Content-Type: application/json');
    echo json_encode([
        "success" => false,
        "statusCode" => 400,
        "message" => "Missing required parameters: fromDate, toDate, and status are required.",
        "data" => null
    ]);
    exit;
}

// Validate date format
if (!isValidDate($fromDate) || !isValidDate($toDate)) {
    header('Content-Type: application/json');
    echo json_encode([
        "success" => false,
        "statusCode" => 400,
        "message" => "Invalid date format. Use YYYY-MM-DD.",
        "data" => null
    ]);
    exit;
}

// Prepare request data
$requestData = [
    'fromDate' => $fromDate,
    'toDate' => $toDate,
    'status' => $status,
    'currencyId' => $currencyId,
    'currencyCode' => $currencyCode
];

// Define the endpoint
$endpoint = "api/v1/Report/Reseller/TransactionSummary";

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
