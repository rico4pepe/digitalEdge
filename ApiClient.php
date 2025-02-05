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

            // Build headers array
            $headers = [
                'X-Api-Key' => $apiKey,
                'X-Nonce' => $nonce,
                'X-Signature' => $signature,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];

            // Handle URL parameters
            $processedEndpoint = $endpoint;
            if (isset($data['merchantCode'])) {
                $processedEndpoint = str_replace('{$merchantCode}', $data['merchantCode'], $processedEndpoint);
                unset($data['merchantCode']); // Remove from data after using it
            }

            // Add optional currency headers if provided
            if (!empty($data['currencyId'])) {
                $headers['X-Currency-Id'] = $data['currencyId'];
                unset($data['currencyId']); // Remove from data to avoid sending in body
            }

            if (!empty($data['currencyCode'])) {
                $headers['X-Currency-Code'] = $data['currencyCode'];
                unset($data['currencyCode']); // Remove from data to avoid sending in body
            }

            $fullUrl = $baseUrl . '/' . $processedEndpoint;

            // Log request details with the actual processed URL
            $this->logger->log("Making $method request to $fullUrl");
            $this->logger->log("Headers: " . json_encode($headers));
            
            if (!empty($data)) {
                $this->logger->log("Request parameters: " . json_encode($data));
            }

            // Make the request
            $response = $this->httpClient->request($method, $fullUrl, $headers, $data);

            // Log response
            $this->logger->log("Response Status: " . $response['statusCode']);
            $this->logger->log("Response Headers: " . json_encode($response['headers']));
            $this->logger->log("Response Body: " . $response['body']);

            return json_encode([
                "success" => $response['statusCode'] >= 200 && $response['statusCode'] < 300,
                "statusCode" => $response['statusCode'],
                "message" => "Request completed",
                "data" => $response['body']
            ]);

        } catch (InvalidArgumentException $e) {
            $this->logger->log("Header validation failed: " . $e->getMessage());
            return json_encode([
                "success" => false,
                "statusCode" => 400,
                "message" => "Invalid headers: " . $e->getMessage(),
                "data" => null
            ]);
        } catch (Exception $e) {
            $this->logger->log("Request failed: " . $e->getMessage());
            return json_encode([
                "success" => false,
                "statusCode" => 500,
                "message" => "Request failed: " . $e->getMessage(),
                "data" => null
            ]);
        }
    }
}