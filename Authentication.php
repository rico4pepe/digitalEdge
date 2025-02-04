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
        // Generate a 24-character numeric nonce (can be any unique value)
        $noncePrefix = '1039';
        $date = date('ymd'); // Current date in yyMMdd format
        $time = date('Hi'); // Current time in HHmm format
        $random = rand(1000000000, 9999999999); // Random 10-digit number

        return $noncePrefix . $date . $time . $random;
    }

    public function generateSignature($nonce, $apiKey, $privateKeyPath)
    {
        $data = $apiKey . $nonce;
        openssl_sign($data, $signature, file_get_contents($privateKeyPath), OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }
}
