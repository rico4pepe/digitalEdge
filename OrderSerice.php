<?php

class OrderService
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Sanitize and validate input data.
     *
     * @param array $data
     * @return array
     */
    public function sanitizeAndValidate(array $data): array
    {
        $sanitizedData = [];
        $errors = [];

        // Define required fields and their validation rules
        $rules = [
            'productId' => ['required' => true, 'type' => 'string'],
            'providerId' => ['required' => true, 'type' => 'string'],
            'amount' => ['required' => true, 'type' => 'numeric'], // Ensure amount is numeric

            'transactionTitle' => ['required' => true, 'type' => 'string'],
            'transactionComment' => ['required' => true, 'type' => 'string'],
            'productCode' => ['required' => true, 'type' => 'string'],

            'uniqueAccountId' => ['required' => true, 'type' => 'string'],
            'buyerFirstname' => ['required' => true, 'type' => 'string'],
            'buyerLastname' => ['required' => true, 'type' => 'string'],

            'buyerTelephone' => ['required' => true, 'type' => 'string'],
            'buyerEmail' => ['required' => true, 'type' => 'string'],
            'buyerCountryCode' => ['required' => true, 'type' => 'string'],

            'beneficiaryFirstname' => ['required' => true, 'type' => 'string'],
            'beneficiaryLastname' => ['required' => true, 'type' => 'string'],
            'beneficiaryEmail' => ['required' => true, 'type' => 'string'],

            'currencyCode' => ['required' => true, 'type' => 'string'], // ISO 4217 (e.g., NGN, USD)
            'countryCode' => ['required' => true, 'type' => 'numeric'] // Typically 2-3 digit country codes
        ];

        // Loop through rules to sanitize and validate data
        foreach ($rules as $field => $rule) {
            if (!isset($data[$field])) {
                if ($rule['required']) {
                    $errors[$field] = "{$field} is required.";
                }
                continue;
            }

            $value = trim($data[$field]); // Sanitize: Trim whitespace

            // Amount should be a valid number
            if ($field === 'amount') {
                if (!is_numeric($value) || floatval($value) <= 0) {
                    $errors[$field] = "Invalid amount: must be a positive number.";
                } else {
                    $sanitizedData[$field] = floatval($value); // Convert to float
                }
                continue;
            }

            $sanitizedData[$field] = $value;

            // Type validation
            if ($rule['type'] === 'string' && !is_string($value)) {
                $errors[$field] = "{$field} must be a string.";
            } elseif ($rule['type'] === 'numeric' && !ctype_digit($value)) {
                $errors[$field] = "{$field} must be a numeric value.";
            }

            // Length validation (if applicable)
            if (isset($rule['length']) && strlen($value) !== $rule['length']) {
                $errors[$field] = "{$field} must be exactly {$rule['length']} characters long.";
            }
        }

        if (!empty($errors)) {
            $this->logger->log("Validation errors: " . json_encode($errors));
            return ['success' => false, 'errors' => $errors];
        }

        return ['success' => true, 'data' => $sanitizedData];
    }
}

?>
