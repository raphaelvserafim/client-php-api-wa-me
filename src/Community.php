<?php

namespace Api\Wame;

class Community
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    public function list(): ?string
    {
        return $this->http->get('/community');
    }

    public function create(string $name, string $subject): ?string
    {
        return $this->http->post('/community', ['name' => $name, 'subject' => $subject]);
    }

    public function get(string $id): ?string
    {
        return $this->http->get('/community/' . $id);
    }

    public function update(string $id, string $subject, string $description): ?string
    {
        return $this->http->put('/community/' . $id, ['subject' => $subject, 'description' => $description]);
    }

    public function leave(string $id): ?string
    {
        return $this->http->delete('/community/' . $id);
    }

    public function updatePicture(string $id, string $url): ?string
    {
        return $this->http->put('/community/' . $id . '/picture', ['url' => $url]);
    }

    public function getInviteCode(string $id): ?string
    {
        return $this->http->post('/community/' . $id . '/invite');
    }

    public function removeParticipants(string $id, array $participants): ?string
    {
        return $this->http->delete('/community/' . $id . '/participants', ['participants' => $participants]);
    }

    public function getRequestParticipants(string $id): ?string
    {
        return $this->http->get('/community/' . $id . '/request_participants_list');
    }

    public function handleRequestParticipants(string $id, array $participants, string $action): ?string
    {
        return $this->http->put('/community/' . $id . '/request_participants_list', [
            'participants' => $participants, 'action' => $action,
        ]);
    }

    public function acceptInvite(string $code): ?string
    {
        return $this->http->post('/community/invite/accept', ['code' => $code]);
    }

    public function getInviteInfo(string $code): ?string
    {
        return $this->http->get('/community/invite/info', ['code' => $code]);
    }

    public function createGroup(string $id, string $subject, array $participants = []): ?string
    {
        return $this->http->post('/community/' . $id . '/group', ['subject' => $subject, 'participants' => $participants]);
    }

    public function setEphemeral(string $id, int $expiration): ?string
    {
        return $this->http->post('/community/' . $id . '/ephemeral', ['expiration' => $expiration]);
    }

    public function updateSettings(string $id, string $setting): ?string
    {
        return $this->http->patch('/community/' . $id . '/settings', [], ['setting' => $setting]);
    }

    public function setMemberAddMode(string $id, string $mode): ?string
    {
        return $this->http->patch('/community/' . $id . '/member-add-mode', [], ['mode' => $mode]);
    }

    public function setJoinApproval(string $id, string $mode): ?string
    {
        return $this->http->patch('/community/' . $id . '/join-approval', [], ['mode' => $mode]);
    }
}
