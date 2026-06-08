<?php

namespace Api\Wame;

class Chat
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    public function getAll(): ?string
    {
        return $this->http->get('/chat');
    }

    public function modify(string $id, string $action, bool $value): ?string
    {
        return $this->http->patch('/chat', ['id' => $id, 'action' => $action, 'value' => $value ? 'true' : 'false']);
    }

    public function delete(string $chatId): ?string
    {
        return $this->http->delete('/chat', null, ['chatId' => $chatId]);
    }

    public function getMessages(string $chatId, int $page = 1, int $limit = 20): ?string
    {
        return $this->http->get('/chat/messages', ['chatId' => $chatId, 'page' => $page, 'limit' => $limit]);
    }

    public function subscribePresence(string $jid): ?string
    {
        return $this->http->post('/chat/presence/subscribe', ['jid' => $jid]);
    }

    public function setDisappearing(string $jid, int $expiration): ?string
    {
        return $this->http->post('/chat/disappearing', ['jid' => $jid, 'expiration' => $expiration]);
    }

    public function getPrivacy(): ?string
    {
        return $this->http->get('/chat/privacy');
    }
}
