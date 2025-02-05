<?php

class UtilityValidationService
{
    private $apiClient;
    private $logger;
    private $config;

    public function __construct(ApiClient $apiClient, Logger $logger, Config $config)
    {
        $this->apiClient = $apiClient;
        $this->logger = $logger;
        $this->config = $config;
    }

    public function validateAccountId(array $requestData, string $endpoint): string
    {
        // Dynamically process the JSON body and sanitize it
        $sanitizedData = $this->sanitizeData($requestData);

        // Handle currency headers separately
        $currencyId = $sanitizedData['currencyId'] ?? null;
        $currencyCode = $sanitizedData['currencyCode'] ?? null;

        // Prepare the dynamic request body
        $requestData = $this->removeCurrencyFromBody($sanitizedData);

        // Handle placeholders dynamically
        $endpointWithPlaceholders = $this->handlePlaceholdersInEndpoint($endpoint, $sanitizedData);

        // Send the request to the API client
        return $this->apiClient->request('POST', $endpointWithPlaceholders, $requestData, $currencyId, $currencyCode);
    }

    private function sanitizeData(array $data): array
    {
        // Sanitize inputs (you can expand this with more sanitization logic)
        return [
            'serviceCode' => filter_var($data['serviceCode'], FILTER_SANITIZE_STRING),
            'uniqueAccountId' => filter_var($data['uniqueAccountId'], FILTER_SANITIZE_STRING),
            'brandCode' => filter_var($data['brandCode'], FILTER_SANITIZE_STRING),
            'currencyId' => $data['currencyId'] ?? null,
            'currencyCode' => $data['currencyCode'] ?? null
        ];
    }

    private function removeCurrencyFromBody(array $data): array
    {
        // Remove currency data from the request body, as it's sent in headers
        unset($data['currencyId']);
        unset($data['currencyCode']);
        return $data;
    }

    private function handlePlaceholdersInEndpoint(string $endpoint, array &$data): string
    {
        // Check for placeholders in the endpoint and replace them dynamically
        // Example: /api/v1/validations/{serviceCode}/{uniqueAccountId}
        foreach ($data as $key => $value) {
            $placeholder = "{" . $key . "}";
            if (strpos($endpoint, $placeholder) !== false) {
                $endpoint = str_replace($placeholder, $value, $endpoint);
                unset($data[$key]); // Remove the replaced key from data
            }
        }
        return $endpoint;
    }
}
