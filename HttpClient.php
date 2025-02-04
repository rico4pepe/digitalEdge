<?php

class HttpClient
{
    public function request($method, $url, $headers, $params = [])
    {
        $ch = curl_init();

        if (strtoupper($method) == 'GET') {
            if (isset($params['code'])) {
                $url = str_replace('{code}', $params['code'], $url);
                unset($params['code']);
            }

            if (!empty($params)) {
                $url = $url . '?' . http_build_query($params);
            }
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if (strtoupper($method) == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }

        if (strtoupper($method) == 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'statusCode' => $statusCode,
            'body' => $response
        ];
    }
}
