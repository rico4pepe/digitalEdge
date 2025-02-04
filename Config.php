
<?php

class Config
{
    private $settings;

    public function __construct()
    {
        $this->settings = [
            'api' => [
                'baseUrl' => 'https://sandboxapi.vendifydigital.com',
                'apiKey' => 'q7H9m6bX/7eXs5KoXhH2QlffyfTWfOy9HoixzCwZHng=',
                'privateKeyPath' => 'privatekey.pem',
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
