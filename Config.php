
<?php

class Config
{
    private $settings;

    public function __construct()
    {
        $this->settings = [
            'api' => [
                'baseUrl' => 'https://sandboxapi.vendifydigital.com',
                'apiKey' => 'Wh27QPVOV/W1R9r+1Pat3vJ5JgvUjwDnQ4nlqdFjoiM=',
                'privateKeyPath' => realpath(__DIR__ . '/privateKey.pem'),

                'currencyId' => null, // Optional
                'currencyCode' => null, // Optional
            ]
        ];
    }

    public function get($key)
    {
        $keys = explode('.', $key);
        $value = $this->settings;

        foreach ($keys as $key) {
            if (isset($value[$key])) {
                $value = $value[$key];
            } else {
                return null;
            }
        }

        return $value;
    }
}
