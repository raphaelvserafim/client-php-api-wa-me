<?php

namespace Api\Wame;

class Status
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    public function sendText(string $text, array $statusJidList = []): ?string
    {
        $body = ['text' => $text];
        if (!empty($statusJidList)) {
            $body['statusJidList'] = $statusJidList;
        }
        return $this->http->post('/status/text', $body);
    }

    public function sendImage(string $url, string $caption = '', array $statusJidList = []): ?string
    {
        $body = ['url' => $url, 'caption' => $caption];
        if (!empty($statusJidList)) {
            $body['statusJidList'] = $statusJidList;
        }
        return $this->http->post('/status/image', $body);
    }

    public function sendVideo(string $url, string $caption = '', array $statusJidList = []): ?string
    {
        $body = ['url' => $url, 'caption' => $caption];
        if (!empty($statusJidList)) {
            $body['statusJidList'] = $statusJidList;
        }
        return $this->http->post('/status/video', $body);
    }

    public function sendAudio(string $url, array $statusJidList = []): ?string
    {
        $body = ['url' => $url];
        if (!empty($statusJidList)) {
            $body['statusJidList'] = $statusJidList;
        }
        return $this->http->post('/status/audio', $body);
    }

    public function sendMention(string $jid, string $statusMsgId): ?string
    {
        return $this->http->post('/status/mention', ['jid' => $jid, 'statusMsgId' => $statusMsgId]);
    }
}
