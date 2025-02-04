<?php

class Authentication
{
    private $apiKey;
    private $privateKeyPath;

    public function __construct($apiKey, $privateKeyPath)
    {
        $this->apiKey = $apiKey;
        $this->privateKeyPath = $privateKeyPath;
    }

    public function generateNonce()
    {
        // Generate a 24-character numeric nonce
        $noncePrefix = '1039';
        $date = date('ymd'); // yyMMdd format
        $time = date('Hi'); // HHmm format
        $random = rand(1000000000, 9999999999); // Random 10-digit number

        return $noncePrefix . $date . $time . $random;
    }

    public function generateSignature($nonce)
    {
        $data = $this->apiKey . $nonce;

        // Ensure private key file exists
        if (!file_exists($this->privateKeyPath) || !is_readable($this->privateKeyPath)) {
            throw new Exception("Private key file not found or unreadable: " . $this->privateKeyPath);
        }

        $privateKey = file_get_contents($this->privateKeyPath);
        if (!$privateKey) {
            throw new Exception("Failed to read private key.");
        }

        $res = openssl_get_privatekey($privateKey);
        if (!$res) {
            throw new Exception("Invalid private key.");
        }

        // Generate signature
        if (!openssl_sign($data, $signature, $res, OPENSSL_ALGO_SHA256)) {
            throw new Exception("Failed to generate signature.");
        }

        openssl_free_key($res);

        return base64_encode($signature);
    }
}
