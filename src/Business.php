<?php

namespace Api\Wame;

class Business
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    public function listCatalog(int $limit = 10, string $cursor = ''): ?string
    {
        $query = ['limit' => $limit];
        if ($cursor !== '') {
            $query['cursor'] = $cursor;
        }
        return $this->http->get('/business/catalog', $query);
    }

    public function createProduct(array $body): ?string
    {
        return $this->http->post('/business/catalog/product', $body);
    }

    public function updateProduct(string $productId, array $body): ?string
    {
        return $this->http->put('/business/catalog/product/' . $productId, $body);
    }

    public function deleteProduct(string $productId): ?string
    {
        return $this->http->delete('/business/catalog/product/' . $productId);
    }
}
