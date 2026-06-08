<?php

namespace Api\Wame;

class Message
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    public function sendPresence(string $to, string $status): ?string
    {
        return $this->http->post('/message/presence', ['to' => $to, 'status' => $status]);
    }

    public function sendText(string $to, string $text): ?string
    {
        return $this->http->post('/message/text', ['to' => $to, 'text' => $text]);
    }

    public function sendButtonReply(array $body): ?string
    {
        return $this->http->post('/message/button_reply', $body);
    }

    public function sendButtonAction(array $body): ?string
    {
        return $this->http->post('/message/button_action', $body);
    }

    public function sendPix(array $body): ?string
    {
        return $this->http->post('/message/pix', $body);
    }

    public function sendList(array $body): ?string
    {
        return $this->http->post('/message/list', $body);
    }

    public function sendSurvey(string $to, string $name, array $options): ?string
    {
        return $this->http->post('/message/survey', ['to' => $to, 'name' => $name, 'options' => $options]);
    }

    public function sendTitle(string $to, string $title, string $text, string $footer): ?string
    {
        return $this->http->post('/message/title', ['to' => $to, 'title' => $title, 'text' => $text, 'footer' => $footer]);
    }

    public function sendAudio(string $to, string $url): ?string
    {
        return $this->http->post('/message/audio', ['to' => $to, 'url' => $url]);
    }

    public function sendImage(string $to, string $url, string $caption = ''): ?string
    {
        return $this->http->post('/message/image', ['to' => $to, 'url' => $url, 'caption' => $caption]);
    }

    public function sendVideo(string $to, string $url, string $caption = ''): ?string
    {
        return $this->http->post('/message/video', ['to' => $to, 'url' => $url, 'caption' => $caption]);
    }

    public function sendDocument(string $to, string $url, string $mimetype, string $fileName = '', string $caption = ''): ?string
    {
        return $this->http->post('/message/document', [
            'to' => $to, 'url' => $url, 'mimetype' => $mimetype, 'fileName' => $fileName, 'caption' => $caption,
        ]);
    }

    public function sendContact(string $to, string $fullName, string $phoneNumber, string $organization = ''): ?string
    {
        return $this->http->post('/message/contact', [
            'to' => $to,
            'contact' => ['fullName' => $fullName, 'organization' => $organization, 'phoneNumber' => $phoneNumber],
        ]);
    }

    public function sendContacts(string $to, string $displayName, array $contacts): ?string
    {
        return $this->http->post('/message/contacts', ['to' => $to, 'displayName' => $displayName, 'contacts' => $contacts]);
    }

    public function sendLocation(string $to, float $latitude, float $longitude, string $address): ?string
    {
        return $this->http->post('/message/location', [
            'to' => $to,
            'location' => ['latitude' => $latitude, 'longitude' => $longitude, 'address' => $address],
        ]);
    }

    public function sendLiveLocation(string $to, float $latitude, float $longitude, string $caption = ''): ?string
    {
        return $this->http->post('/message/live-location', [
            'to' => $to, 'latitude' => $latitude, 'longitude' => $longitude, 'caption' => $caption,
        ]);
    }

    public function sendLink(string $to, string $title, string $text, string $thumbnailUrl, string $sourceUrl, string $description = ''): ?string
    {
        return $this->http->post('/message/link', [
            'to' => $to, 'title' => $title, 'text' => $text,
            'description' => $description, 'thumbnailUrl' => $thumbnailUrl, 'sourceUrl' => $sourceUrl,
        ]);
    }

    public function sendReaction(string $text, string $msgId): ?string
    {
        return $this->http->post('/message/reaction', ['text' => $text, 'msgId' => $msgId]);
    }

    public function sendSticker(string $to, string $url): ?string
    {
        return $this->http->post('/message/sticker', ['to' => $to, 'url' => $url]);
    }

    public function sendVideoNote(string $to, string $url): ?string
    {
        return $this->http->post('/message/video-note', ['to' => $to, 'url' => $url]);
    }

    public function sendPoll(string $to, string $name, array $values, int $selectableCount = 1): ?string
    {
        return $this->http->post('/message/poll', [
            'to' => $to, 'name' => $name, 'values' => $values, 'selectableCount' => $selectableCount,
        ]);
    }

    public function sendEvent(string $to, string $name, string $description, string $startTime, string $locationName = '', string $locationAddress = ''): ?string
    {
        return $this->http->post('/message/event', [
            'to' => $to, 'name' => $name, 'description' => $description,
            'startTime' => $startTime, 'locationName' => $locationName, 'locationAddress' => $locationAddress,
        ]);
    }

    public function pin(string $id, int $duration = 604800): ?string
    {
        return $this->http->post('/message/pin', ['id' => $id, 'duration' => $duration]);
    }

    public function unpin(string $id): ?string
    {
        return $this->http->post('/message/unpin', ['id' => $id]);
    }

    public function sendCallLink(string $to, string $type = 'audio', string $caption = ''): ?string
    {
        return $this->http->post('/message/call-link', ['to' => $to, 'type' => $type, 'caption' => $caption]);
    }

    public function createCallLink(string $type = 'audio'): ?string
    {
        return $this->http->post('/message/create-call-link', ['type' => $type]);
    }

    public function getDetails(string $messageId): ?string
    {
        return $this->http->get('/message/' . $messageId);
    }

    public function downloadMedia(string $messageId, string $format = 'json'): ?string
    {
        return $this->http->get('/message/' . $messageId . '/media', ['format' => $format]);
    }

    public function sendBase64Image(string $to, string $base64, string $caption = ''): ?string
    {
        return $this->http->post('/message/base64/image', ['to' => $to, 'base64' => $base64, 'caption' => $caption]);
    }

    public function sendBase64Audio(string $to, string $base64): ?string
    {
        return $this->http->post('/message/base64/audio', ['to' => $to, 'base64' => $base64]);
    }

    public function sendBase64Document(string $to, string $base64, string $mimetype, string $fileName = '', string $caption = ''): ?string
    {
        return $this->http->post('/message/base64/document', [
            'to' => $to, 'base64' => $base64, 'mimetype' => $mimetype, 'fileName' => $fileName, 'caption' => $caption,
        ]);
    }

    public function sendProduct(array $body): ?string
    {
        return $this->http->post('/message/product', $body);
    }

    public function sendGroupInvite(array $body): ?string
    {
        return $this->http->post('/message/group-invite', $body);
    }

    public function requestPhone(string $to): ?string
    {
        return $this->http->post('/message/request-phone', ['to' => $to]);
    }

    // --- Reply methods ---

    public function replyText(string $id, string $to, string $text): ?string
    {
        return $this->http->post('/message/' . $id . '/text', ['to' => $to, 'text' => $text]);
    }

    public function replyButtonReply(string $id, array $body): ?string
    {
        return $this->http->post('/message/' . $id . '/button_reply', $body);
    }

    public function replyButtonAction(string $id, array $body): ?string
    {
        return $this->http->post('/message/' . $id . '/button_action', $body);
    }

    public function replyPix(string $id, array $body): ?string
    {
        return $this->http->post('/message/' . $id . '/pix', $body);
    }

    public function replyTitle(string $id, string $to, string $title, string $text, string $footer): ?string
    {
        return $this->http->post('/message/' . $id . '/title', ['to' => $to, 'title' => $title, 'text' => $text, 'footer' => $footer]);
    }

    public function replyAudio(string $id, string $to, string $url): ?string
    {
        return $this->http->post('/message/' . $id . '/audio', ['to' => $to, 'url' => $url]);
    }

    public function replyImage(string $id, string $to, string $url, string $caption = ''): ?string
    {
        return $this->http->post('/message/' . $id . '/image', ['to' => $to, 'url' => $url, 'caption' => $caption]);
    }

    public function replyVideo(string $id, string $to, string $url, string $caption = ''): ?string
    {
        return $this->http->post('/message/' . $id . '/video', ['to' => $to, 'url' => $url, 'caption' => $caption]);
    }

    public function replyDocument(string $id, string $to, string $url, string $mimetype, string $fileName = '', string $caption = ''): ?string
    {
        return $this->http->post('/message/' . $id . '/document', [
            'to' => $to, 'url' => $url, 'mimetype' => $mimetype, 'fileName' => $fileName, 'caption' => $caption,
        ]);
    }

    public function replyContact(string $id, string $to, string $fullName, string $phoneNumber, string $organization = ''): ?string
    {
        return $this->http->post('/message/' . $id . '/contact', [
            'to' => $to,
            'contact' => ['fullName' => $fullName, 'organization' => $organization, 'phoneNumber' => $phoneNumber],
        ]);
    }

    public function replyLocation(string $id, string $to, float $latitude, float $longitude, string $address): ?string
    {
        return $this->http->post('/message/' . $id . '/location', [
            'to' => $to,
            'location' => ['latitude' => $latitude, 'longitude' => $longitude, 'address' => $address],
        ]);
    }
}
