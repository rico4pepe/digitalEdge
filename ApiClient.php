<?php

class ApiClient
{
    private $httpClient;
    private $auth;
    private $currencyId;
    private $currencyCode;
    private $logger;
    private $config;

    public function __construct(MyHttpClient $httpClient, Authentication $auth, Logger $logger, Config $config)
    {
        $this->httpClient = $httpClient;
        $this->auth = $auth;
        $this->logger = $logger;
        $this->config = $config;

        // Optionally retrieve currencyId and currencyCode from config or query parameters
        $this->currencyId = $this->config->get('api.currencyId');
        $this->currencyCode = $this->config->get('api.currencyCode');
    }

    public function request(string $method, string $endpoint, array $data = []): string
    {
        $baseUrl = $this->config->get('api.baseUrl');
        $apiKey = $this->config->get('api.apiKey');

        try {
            $nonce = $this->auth->generateNonce();
            $signature = $this->auth->generateSignature($nonce);

            $headers = [
                'X-Api-Key' => $apiKey,
                'X-Nonce' => $nonce,
                'X-Signature' => $signature,
                'Content-Type' => 'application/json'
            ];

            // Add currency details from query parameters if provided
            if (!empty($data['currencyId'])) {
                $headers['X-Currency-Id'] = $data['currencyId'];
            }

            if (!empty($data['currencyCode'])) {
                $headers['X-Currency-Code'] = $data['currencyCode'];
            }

            $this->logger->log("Making $method request to $baseUrl/$endpoint with headers: " . json_encode($headers));
            if (!empty($data)) {
                $this->logger->log("Request payload: " . json_encode($data));
            }

            $response = $this->httpClient->request($method, $baseUrl . '/' . $endpoint, $headers, $data);

            $this->logger->log("Response: " . json_encode($response));

            return json_encode([
                "success" => true,
                "statusCode" => $response['statusCode'],
                "message" => "Request successful",
                "data" => $response['body']
            ]);
        } catch (Exception $e) {
            $this->logger->log("Error: " . $e->getMessage());
            return json_encode([
                "success" => false,
                "statusCode" => 500,
                "message" => "An error occurred: " . $e->getMessage(),
                "data" => null
            ]);
        }
    }
}
