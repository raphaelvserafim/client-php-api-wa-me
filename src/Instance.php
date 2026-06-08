<?php

namespace Api\Wame;

class Instance
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    public function getInfo(): ?string
    {
        return $this->http->get('/instance');
    }

    public function connect(): ?string
    {
        return $this->http->post('/instance');
    }

    public function connectPairingCode(string $phoneNumber): ?string
    {
        return $this->http->post('/instance/pairing-code', ['phoneNumber' => $phoneNumber]);
    }

    public function updateSettings(bool $markMessageRead, bool $saveMedia, bool $receiveStatusMessage = false, bool $receivePresence = false): ?string
    {
        return $this->http->patch('/instance', [
            'markMessageRead' => var_export($markMessageRead, true),
            'saveMedia' => var_export($saveMedia, true),
            'receiveStatusMessage' => var_export($receiveStatusMessage, true),
            'receivePresence' => var_export($receivePresence, true),
        ]);
    }

    public function updateWebhook(array $body): ?string
    {
        return $this->http->put('/instance', $body);
    }

    public function logout(): ?string
    {
        return $this->http->delete('/instance');
    }

    public function addMongoDB(string $uri, string $dbName): ?string
    {
        return $this->http->post('/instance/mongodb', ['uri' => $uri, 'dbName' => $dbName]);
    }

    public function setProxy(string $proxy): ?string
    {
        return $this->http->post('/instance/proxy', ['proxy' => $proxy]);
    }

    public function resync(): ?string
    {
        return $this->http->post('/instance/resync');
    }

    public function restart(): ?string
    {
        return $this->http->post('/instance/restart');
    }

    public function updateProfileStatus(string $text): ?string
    {
        return $this->http->put('/instance/status', ['text' => $text]);
    }

    public function updateProfilePicture(string $url): ?string
    {
        return $this->http->put('/instance/profile/picture', ['url' => $url]);
    }

    public function removeProfilePicture(): ?string
    {
        return $this->http->delete('/instance/profile/picture');
    }

    public function updateProfileName(string $name): ?string
    {
        return $this->http->put('/instance/profile/name', ['name' => $name]);
    }

    public function prepareMobileRegistration(string $countryCode, string $nationalNumber, string $networkCode): ?string
    {
        return $this->http->post('/instance/mobile/prepare', [
            'phoneNumberCountryCode' => $countryCode,
            'phoneNumberNationalNumber' => $nationalNumber,
            'phoneNumberMobileNetworkCode' => $networkCode,
        ]);
    }

    public function requestMobileCode(string $method = 'sms'): ?string
    {
        return $this->http->post('/instance/mobile/request-code', ['method' => $method]);
    }

    public function verifyMobileCode(string $code): ?string
    {
        return $this->http->post('/instance/mobile/verify', ['code' => $code]);
    }

    public function getWebhookStatistics(): ?string
    {
        return $this->http->get('/instance/webhook/statistics');
    }
}
