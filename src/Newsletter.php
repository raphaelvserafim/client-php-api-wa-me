<?php

namespace Api\Wame;

class Newsletter
{
    private HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    public function create(string $name, string $description = ''): ?string
    {
        return $this->http->post('/newsletter', ['name' => $name, 'description' => $description]);
    }

    public function getMetadata(string $type, string $id): ?string
    {
        return $this->http->get('/newsletter/metadata', ['type' => $type, 'id' => $id]);
    }

    public function getSubscribers(string $id): ?string
    {
        return $this->http->get('/newsletter/' . $id . '/subscribers');
    }

    public function getAdmins(string $id): ?string
    {
        return $this->http->get('/newsletter/' . $id . '/admins');
    }

    public function follow(string $id): ?string
    {
        return $this->http->post('/newsletter/' . $id . '/follow');
    }

    public function unfollow(string $id): ?string
    {
        return $this->http->post('/newsletter/' . $id . '/unfollow');
    }

    public function updateName(string $id, string $name): ?string
    {
        return $this->http->put('/newsletter/' . $id . '/name', ['name' => $name]);
    }

    public function updateDescription(string $id, string $description): ?string
    {
        return $this->http->put('/newsletter/' . $id . '/description', ['description' => $description]);
    }

    public function updatePicture(string $id, string $url): ?string
    {
        return $this->http->put('/newsletter/' . $id . '/picture', ['url' => $url]);
    }

    public function removePicture(string $id): ?string
    {
        return $this->http->delete('/newsletter/' . $id . '/picture');
    }

    public function transferOwnership(string $id, string $newOwnerJid): ?string
    {
        return $this->http->put('/newsletter/' . $id . '/owner', ['newOwnerJid' => $newOwnerJid]);
    }

    public function demoteAdmin(string $id, string $userJid): ?string
    {
        return $this->http->put('/newsletter/' . $id . '/demote', ['userJid' => $userJid]);
    }

    public function getMessages(string $id, int $count = 10, ?int $since = null, ?int $after = null): ?string
    {
        $query = ['count' => $count];
        if ($since !== null) {
            $query['since'] = $since;
        }
        if ($after !== null) {
            $query['after'] = $after;
        }
        return $this->http->get('/newsletter/' . $id . '/messages', $query);
    }

    public function react(string $id, string $serverId, string $reaction): ?string
    {
        return $this->http->post('/newsletter/' . $id . '/react', ['serverId' => $serverId, 'reaction' => $reaction]);
    }

    public function mute(string $id): ?string
    {
        return $this->http->post('/newsletter/' . $id . '/mute');
    }

    public function unmute(string $id): ?string
    {
        return $this->http->post('/newsletter/' . $id . '/unmute');
    }

    public function delete(string $id): ?string
    {
        return $this->http->delete('/newsletter/' . $id);
    }
}
