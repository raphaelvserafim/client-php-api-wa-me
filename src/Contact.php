<?php

namespace Api\Wame;

class Contact
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    public function list(): ?string
    {
        return $this->http->get('/contacts');
    }

    public function add(string $number, string $name): ?string
    {
        return $this->http->post('/contacts', ['number' => $number, 'name' => $name]);
    }

    public function getProfile(string $number): ?string
    {
        return $this->http->get('/contacts/' . $number);
    }

    public function remove(string $number): ?string
    {
        return $this->http->delete('/contacts/' . $number);
    }

    public function blockAction(string $number, string $action): ?string
    {
        return $this->http->patch('/contacts/' . $number, ['action' => $action]);
    }

    public function block(string $number): ?string
    {
        return $this->blockAction($number, 'block');
    }

    public function unblock(string $number): ?string
    {
        return $this->blockAction($number, 'unblock');
    }

    public function clearSession(string $number): ?string
    {
        return $this->http->delete('/contacts/' . $number . '/session');
    }

    public function getStatus(string $number): ?string
    {
        return $this->http->get('/contacts/' . $number . '/status');
    }

    public function listBlocked(): ?string
    {
        return $this->http->get('/contacts/blocked');
    }

    public function resolveLids(array $lids): ?string
    {
        return $this->http->post('/contacts/resolve-lids', ['lids' => $lids]);
    }

    public function isRegistered(string $number): ?string
    {
        return $this->http->get('/actions/registered', ['number' => $number]);
    }
}
