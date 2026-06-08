<?php

namespace Api\Wame;

class HttpClient
{
    private string $baseUrl;
    private string $key;

    public function __construct(string $baseUrl, string $key)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->key = $key;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function request(string $method, string $path, ?array $body = null, array $query = []): ?string
    {
        $url = $this->baseUrl . '/' . $this->key . $path;

        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        $headers = ['Content-Type: application/json'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $upperMethod = strtoupper($method);
        if (in_array($upperMethod, ['POST', 'PUT', 'PATCH', 'DELETE'], true) && $body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            $errorMessage = curl_error($ch);
            curl_close($ch);
            throw new \RuntimeException("cURL Error: $errorMessage");
        }

        curl_close($ch);

        return $result !== false ? $result : null;
    }

    public function get(string $path, array $query = []): ?string
    {
        return $this->request('GET', $path, null, $query);
    }

    public function post(string $path, ?array $body = null): ?string
    {
        return $this->request('POST', $path, $body);
    }

    public function put(string $path, ?array $body = null): ?string
    {
        return $this->request('PUT', $path, $body);
    }

    public function patch(string $path, array $query = [], ?array $body = null): ?string
    {
        return $this->request('PATCH', $path, $body, $query);
    }

    public function delete(string $path, ?array $body = null, array $query = []): ?string
    {
        return $this->request('DELETE', $path, $body, $query);
    }
}
