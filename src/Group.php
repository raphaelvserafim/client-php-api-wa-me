<?php

namespace Api\Wame;

class Group
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    public function list(): ?string
    {
        return $this->http->get('/groups');
    }

    public function create(string $name, array $participants): ?string
    {
        return $this->http->post('/groups', ['name' => $name, 'participants' => $participants]);
    }

    public function get(string $id): ?string
    {
        return $this->http->get('/groups/' . $id);
    }

    public function update(string $id, string $name, string $description): ?string
    {
        return $this->http->put('/groups/' . $id, ['name' => $name, 'description' => $description]);
    }

    public function changeSettings(string $id, string $setting): ?string
    {
        return $this->http->patch('/groups/' . $id, ['setting' => $setting]);
    }

    public function leave(string $id): ?string
    {
        return $this->http->delete('/groups/' . $id);
    }

    public function getMembers(string $id): ?string
    {
        return $this->http->get('/groups/' . $id . '/members');
    }

    public function getInviteCode(string $id): ?string
    {
        return $this->http->get('/groups/' . $id . '/invite');
    }

    public function getInviteInfo(string $code): ?string
    {
        return $this->http->get('/groups/invite/info', ['code' => $code]);
    }

    public function updatePicture(string $id, string $url): ?string
    {
        return $this->http->put('/groups/' . $id . '/picture', ['url' => $url]);
    }

    public function removePicture(string $id): ?string
    {
        return $this->http->delete('/groups/' . $id . '/picture');
    }

    public function addParticipants(string $id, array $participants): ?string
    {
        return $this->http->post('/groups/' . $id . '/participants', ['participants' => $participants]);
    }

    public function removeParticipants(string $id, array $participants): ?string
    {
        return $this->http->delete('/groups/' . $id . '/participants', ['participants' => $participants]);
    }

    public function changeRole(string $id, array $participants, string $action): ?string
    {
        return $this->http->patch('/groups/' . $id . '/role', ['action' => $action], ['participants' => $participants]);
    }

    public function promote(string $id, array $participants): ?string
    {
        return $this->changeRole($id, $participants, 'promote');
    }

    public function demote(string $id, array $participants): ?string
    {
        return $this->changeRole($id, $participants, 'demote');
    }

    public function getRequestParticipants(string $id): ?string
    {
        return $this->http->get('/groups/' . $id . '/request_participants_list');
    }

    public function handleRequestParticipants(string $id, array $participants, string $action): ?string
    {
        return $this->http->put('/groups/' . $id . '/request_participants_list', [
            'participants' => $participants, 'action' => $action,
        ]);
    }
}
