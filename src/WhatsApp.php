<?php

namespace Api\Wame;

class WhatsApp
{
    private HttpClient $http;

    public Instance $instance;
    public Message $message;
    public Chat $chat;
    public Contact $contact;
    public Group $group;
    public Community $community;
    public Label $label;
    public Newsletter $newsletter;
    public Business $business;
    public Status $status;
    public Call $call;
    public Webhook $webhook;

    public function __construct(array $data = [])
    {
        $server = $data['server'] ?? '';
        $key = $data['key'] ?? '';

        $this->http = new HttpClient($server, $key);

        $this->instance = new Instance($this->http);
        $this->message = new Message($this->http);
        $this->chat = new Chat($this->http);
        $this->contact = new Contact($this->http);
        $this->group = new Group($this->http);
        $this->community = new Community($this->http);
        $this->label = new Label($this->http);
        $this->newsletter = new Newsletter($this->http);
        $this->business = new Business($this->http);
        $this->status = new Status($this->http);
        $this->call = new Call($this->http);
        $this->webhook = new Webhook();
    }

    // --- Deprecated backward-compatible methods ---

    /** @deprecated Use $this->instance->connect() */
    public function connect(): ?string
    {
        return $this->instance->connect();
    }

    /** @deprecated Use $this->instance->getInfo() */
    public function inforInstance(): ?string
    {
        return $this->instance->getInfo();
    }

    /** @deprecated Use $this->instance->updateSettings() */
    public function updateSaveMediaMarkMessageRead(bool $markMessageRead, bool $saveMedia): ?string
    {
        return $this->instance->updateSettings($markMessageRead, $saveMedia);
    }

    /** @deprecated Use $this->instance->updateWebhook() */
    public function updateWebhook($body): ?string
    {
        return $this->instance->updateWebhook(is_array($body) ? $body : (array) $body);
    }

    /** @deprecated Use $this->instance->logout() */
    public function logout(): ?string
    {
        return $this->instance->logout();
    }

    /** @deprecated Use $this->contact->list() */
    public function listContacts(): ?string
    {
        return $this->contact->list();
    }

    /** @deprecated Use $this->contact->getProfile() */
    public function profilePic(string $to): ?string
    {
        return $this->contact->getProfile($to);
    }

    /** @deprecated Use $this->instance->updateProfileName() */
    public function updateProfileName(string $name): ?string
    {
        return $this->instance->updateProfileName($name);
    }

    /** @deprecated Use $this->instance->updateProfilePicture() */
    public function updateProfilePicture(string $url): ?string
    {
        return $this->instance->updateProfilePicture($url);
    }

    /** @deprecated Use $this->message->sendPresence() */
    public function sendPresence(string $to, string $status): ?string
    {
        return $this->message->sendPresence($to, $status);
    }

    /** @deprecated Use $this->message->sendText() */
    public function sendText(string $to, string $text): ?string
    {
        return $this->message->sendText($to, $text);
    }

    /** @deprecated Use $this->message->sendAudio() */
    public function sendAudio(string $to, string $url, bool $ptt = true): ?string
    {
        return $this->message->sendAudio($to, $url);
    }

    /** @deprecated Use $this->message->sendImage() */
    public function sendImage(string $to, string $url, string $caption): ?string
    {
        return $this->message->sendImage($to, $url, $caption);
    }

    /** @deprecated Use $this->message->sendVideo() */
    public function sendVideo(string $to, string $url, string $caption): ?string
    {
        return $this->message->sendVideo($to, $url, $caption);
    }

    /** @deprecated Use $this->message->sendDocument() */
    public function sendDocument(string $to, string $url, string $caption, string $mimetype, $fileName): ?string
    {
        return $this->message->sendDocument($to, $url, $mimetype, (string) $fileName, $caption);
    }

    /** @deprecated Use $this->message->sendBase64Image() / sendBase64Audio() / sendBase64Document() */
    public function sendMediaBase64(string $to, string $base64String, string $type, string $caption): ?string
    {
        return match ($type) {
            'image' => $this->message->sendBase64Image($to, $base64String, $caption),
            'audio' => $this->message->sendBase64Audio($to, $base64String),
            'document' => $this->message->sendBase64Document($to, $base64String, '', '', $caption),
            default => $this->message->sendBase64Image($to, $base64String, $caption),
        };
    }

    /** @deprecated Use $this->message->sendSurvey() */
    public function sendSurvey(string $to, string $name, array $options): ?string
    {
        return $this->message->sendSurvey($to, $name, $options);
    }

    /** @deprecated Use $this->message->sendButtonAction() */
    public function sendButton($body): ?string
    {
        return $this->message->sendButtonAction(is_array($body) ? $body : (array) $body);
    }

    /** @deprecated Use $this->message->sendButtonReply() */
    public function sendButtonReply($body): ?string
    {
        return $this->message->sendButtonReply(is_array($body) ? $body : (array) $body);
    }

    /** @deprecated Use $this->message->sendList() */
    public function sendList($body): ?string
    {
        return $this->message->sendList(is_array($body) ? $body : (array) $body);
    }

    /** @deprecated Use $this->message->sendContact() */
    public function sendContact(string $to, string $name, string $number): ?string
    {
        return $this->message->sendContact($to, $name, $number);
    }

    /** @deprecated Use $this->message->sendLocation() */
    public function sendLocation(string $to, float $lat, float $lon, string $address): ?string
    {
        return $this->message->sendLocation($to, $lat, $lon, $address);
    }

    /** @deprecated Use $this->message->sendReaction() */
    public function sendReaction(string $to, string $text, string $msgId): ?string
    {
        return $this->message->sendReaction($text, $msgId);
    }

    /** @deprecated Use $this->group->list() */
    public function listGroup(): ?string
    {
        return $this->group->list();
    }

    /** @deprecated Use $this->group->get() */
    public function inforGroup(string $group_id): ?string
    {
        return $this->group->get($group_id);
    }

    /** @deprecated Use $this->group->getInviteCode() */
    public function groupInviteCode(string $group_id): ?string
    {
        return $this->group->getInviteCode($group_id);
    }

    /** @deprecated Use $this->group->create() */
    public function createGroup(string $name, array $participants): ?string
    {
        return $this->group->create($name, $participants);
    }

    /** @deprecated Use $this->group->addParticipants() */
    public function addParticipantsGroup(string $group_id, array $participants): ?string
    {
        return $this->group->addParticipants($group_id, $participants);
    }

    /** @deprecated Use $this->group->removeParticipants() */
    public function removeParticipantsGroup(string $group_id, array $participants): ?string
    {
        return $this->group->removeParticipants($group_id, $participants);
    }

    /** @deprecated Use $this->group->leave() */
    public function leaveGroup(string $group_id): ?string
    {
        return $this->group->leave($group_id);
    }

    /** @deprecated Use $this->group->promote() or $this->group->demote() */
    public function promoteParticipantsGroup(string $group_id, array $participants, string $action): ?string
    {
        return $this->group->changeRole($group_id, $participants, $action);
    }

    /** @deprecated Use $this->message->downloadMedia() */
    public function downloadMediaMessage($type, $body): ?string
    {
        if (is_array($body) && isset($body['mediaKey'])) {
            return $this->http->request('POST', '/actions/download/media', $body, ['type' => $type]);
        }
        return null;
    }

    /** @deprecated Use $this->webhook->parse() */
    public function constructWebhook(): void
    {
        $this->webhook->parse();
    }
}
