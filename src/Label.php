<?php

namespace Api\Wame;

class Label
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    public function list(): ?string
    {
        return $this->http->get('/labels');
    }

    public function create(string $name, string $labelId = ''): ?string
    {
        return $this->http->post('/labels', ['name' => $name, 'labelId' => $labelId]);
    }

    public function getChats(string $id): ?string
    {
        return $this->http->get('/labels/' . $id);
    }

    public function addToChat(string $id, string $to): ?string
    {
        return $this->http->post('/labels/' . $id, ['to' => $to]);
    }

    public function delete(string $id): ?string
    {
        return $this->http->delete('/labels/' . $id);
    }

    public function removeFromChat(string $id, string $to): ?string
    {
        return $this->http->delete('/labels/' . $id . '/chat/' . $to);
    }
}
