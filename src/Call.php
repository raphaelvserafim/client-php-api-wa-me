<?php

namespace Api\Wame;

class Call
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    public function make(string $to): ?string
    {
        return $this->http->post('/call', ['to' => $to]);
    }

    public function reject(string $id, string $from): ?string
    {
        return $this->http->delete('/call/' . $id . '/' . $from);
    }

    public function accept(string $callId, string $callFrom): ?string
    {
        return $this->http->post('/call/accept', ['callId' => $callId, 'callFrom' => $callFrom]);
    }

    public function end(string $callId, string $peerJid): ?string
    {
        return $this->http->post('/call/end', ['callId' => $callId, 'peerJid' => $peerJid]);
    }
}
