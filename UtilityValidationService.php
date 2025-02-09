<?php

class UtilityValidationService
{
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
            'serviceCode' => ['required' => true, 'type' => 'string'],
            'uniqueAccountId' => ['required' => true, 'type' => 'string'],
            'brandCode' => ['required' => true, 'type' => 'string'],
            'currencyCode' => ['required' => true, 'type' => 'string', 'length' => 3], // ISO 4217 (e.g., NGN, USD)
            'countryCode' => ['required' => true, 'type' => 'numeric', 'length' => 2] // Typically 2-3 digit country codes
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
            return ['success' => false, 'errors' => $errors];
        }

        return ['success' => true, 'data' => $sanitizedData];
    }
}


?>
