<?php

namespace Api\Wame;

use stdClass;

class Webhook
{
    public ?stdClass $from = null;

    /**
     * Parse a legacy (baileys-style `data.msgContent`) webhook payload.
     *
     * @deprecated The API now delivers the Meta/envelope format. Use
     *   {@see Webhook::parseMeta()} instead.
     */
    public function parse(?string $rawInput = null): ?stdClass
    {
        $raw = $rawInput ?? file_get_contents('php://input');
        $data = json_decode($raw, true);

        if (empty($data) || !isset($data['data'])) {
            return null;
        }

        $this->from = new stdClass();
        $msg = $data['data'];
        $msgContent = $msg['msgContent'] ?? [];
        $messageType = $msg['messageType'] ?? 'unknown';

        $this->from->remoteJid = isset($msg['key']['remoteJid'])
            ? preg_replace('/[^0-9]/', '', $msg['key']['remoteJid'])
            : null;
        $this->from->msgId = $msg['key']['id'] ?? null;
        $this->from->pushName = $msg['push_name'] ?? null;
        $this->from->messageType = $messageType;

        // Text messages
        if (isset($msgContent['conversation'])) {
            $this->from->messageType = 'text';
            $this->from->text = $msgContent['conversation'];
        } elseif (isset($msgContent['extendedTextMessage']['text'])) {
            $this->from->messageType = 'text';
            $this->from->text = $msgContent['extendedTextMessage']['text'];
        }

        // Button response
        if (isset($msgContent['buttonsResponseMessage'])) {
            $this->from->messageType = 'button';
            $this->from->selectedId = $msgContent['buttonsResponseMessage']['selectedButtonId'];
        }

        // List response
        if (isset($msgContent['listResponseMessage'])) {
            $this->from->messageType = 'list';
            $this->from->selectedId = $msgContent['listResponseMessage']['singleSelectReply']['selectedRowId'] ?? null;
            $this->from->title = $msgContent['listResponseMessage']['title'] ?? null;
        }

        // messageContextInfo fallback for list
        if ($messageType === 'messageContextInfo' && isset($msgContent['listResponseMessage'])) {
            $this->from->messageType = 'list';
            $this->from->selectedId = $msgContent['listResponseMessage']['singleSelectReply']['selectedRowId'] ?? null;
            $this->from->text = $msgContent['listResponseMessage']['title'] ?? null;
        }

        // Reaction
        if (isset($msgContent['reactionMessage'])) {
            $this->from->messageType = 'reaction';
            $this->from->text = $msgContent['reactionMessage']['text'];
        }

        // Location (BUG FIX: old code checked $this->from->latitude instead of $messageType)
        if ($messageType === 'locationMessage') {
            $this->from->messageType = 'location';
            $this->from->latitude = $msgContent['locationMessage']['degreesLatitude'] ?? null;
            $this->from->longitude = $msgContent['locationMessage']['degreesLongitude'] ?? null;
            if (isset($msgContent['locationMessage']['jpegThumbnail'])) {
                $this->from->thumbnail = 'data:image/jpeg;base64,' . $msgContent['locationMessage']['jpegThumbnail'];
            }
        }

        // Live location
        if ($messageType === 'liveLocationMessage') {
            $this->from->messageType = 'liveLocation';
            $this->from->latitude = $msgContent['liveLocationMessage']['degreesLatitude'] ?? null;
            $this->from->longitude = $msgContent['liveLocationMessage']['degreesLongitude'] ?? null;
            if (isset($msgContent['liveLocationMessage']['jpegThumbnail'])) {
                $this->from->thumbnail = 'data:image/jpeg;base64,' . $msgContent['liveLocationMessage']['jpegThumbnail'];
            }
        }

        // Template button reply
        if ($messageType === 'templateButtonReplyMessage') {
            $this->from->messageType = 'button';
            $this->from->selectedId = $msgContent['templateButtonReplyMessage']['selectedIndex'] ?? null;
        }

        // Media types
        $mediaTypes = [
            'audioMessage' => 'audio',
            'imageMessage' => 'image',
            'stickerMessage' => 'sticker',
            'videoMessage' => 'video',
            'documentMessage' => 'document',
        ];

        if (isset($mediaTypes[$messageType])) {
            $this->from->messageType = $mediaTypes[$messageType];
            $mediaContent = $msgContent[$messageType] ?? [];

            if (isset($mediaContent['mimetype'])) {
                $this->from->mimetype = $mediaContent['mimetype'];
            }
            if (!empty($mediaContent['jpegThumbnail'])) {
                $this->from->thumbnail = 'data:image/jpeg;base64,' . $mediaContent['jpegThumbnail'];
            }

            $this->from->messageKeys = [
                'mediaKey' => $mediaContent['mediaKey'] ?? null,
                'directPath' => $mediaContent['directPath'] ?? null,
                'url' => $mediaContent['url'] ?? null,
                'messageType' => $this->from->messageType,
            ];

            $this->from->mediaBase64 = $msg['fileBase64'] ?? null;
            $this->from->mediaURL = $msg['urlMedia'] ?? null;

            if (isset($mediaContent['title'])) {
                $this->from->title = $mediaContent['title'];
            }
            if (isset($mediaContent['fileName'])) {
                $this->from->fileName = $mediaContent['fileName'];
            }
            if (isset($mediaContent['caption'])) {
                $this->from->caption = $mediaContent['caption'];
            }
        }

        // Contact message
        if ($messageType === 'contactMessage') {
            $this->from->messageType = 'contact';
            $this->from->contact = [];
            $displayName = $msgContent['contactMessage']['displayName'] ?? '';
            $vcard = $msgContent['contactMessage']['vcard'] ?? '';
            $contact = new stdClass();
            $contact->name = $displayName;
            $contact->number = $this->extractPhoneFromVcard($vcard);
            $this->from->contact[] = $contact;
        }

        // Multiple contacts
        if ($messageType === 'contactsArrayMessage') {
            $this->from->messageType = 'contact';
            $this->from->contact = [];
            $contacts = $msgContent['contactsArrayMessage']['contacts'] ?? [];
            foreach ($contacts as $c) {
                $contact = new stdClass();
                $contact->name = $c['displayName'] ?? '';
                $contact->number = $this->extractPhoneFromVcard($c['vcard'] ?? '');
                $this->from->contact[] = $contact;
            }
        }

        return $this->from;
    }

    private function extractPhoneFromVcard(string $vcard): string
    {
        if (preg_match('/TEL[^:]*:(.+)/i', $vcard, $matches)) {
            return trim($matches[1]);
        }
        return '';
    }

    // ==================== Meta / "wame" envelope parser ====================

    /**
     * Parse an incoming webhook payload in the Meta / "wame" envelope format
     * into a list of normalized events (associative arrays).
     *
     * A single POST can batch multiple entries / changes / messages, so this
     * always returns an array. It never throws on shape: an unrecognized field
     * becomes an `unknown` event, and a body that is not a valid envelope
     * returns an empty array — so your endpoint can always respond 200.
     *
     * @param array|null $body Decoded payload. When null, reads php://input.
     * @return array<int, array<string, mixed>> Normalized events.
     */
    public function parseMeta(?array $body = null): array
    {
        if ($body === null) {
            $raw = file_get_contents('php://input');
            $body = json_decode($raw ?: '', true);
        }

        if (!is_array($body) || !isset($body['entry']) || !is_array($body['entry'])) {
            return [];
        }

        $events = [];

        foreach ($body['entry'] as $entry) {
            if (!is_array($entry)) {
                continue;
            }
            $changes = (isset($entry['changes']) && is_array($entry['changes'])) ? $entry['changes'] : [];

            foreach ($changes as $change) {
                if (!is_array($change)) {
                    continue;
                }

                $base = [
                    'instanceId' => $entry['id'] ?? null,
                    'metadata' => $this->toMetadata($change['value']['metadata'] ?? null),
                    'field' => $change['field'] ?? null,
                    'raw' => $body,
                ];
                if (isset($body['provider'])) {
                    $base['provider'] = $body['provider'];
                }
                if (array_key_exists('official', $body)) {
                    $base['official'] = $body['official'];
                }

                foreach ($this->mapChange($change, $base) as $event) {
                    $events[] = $event;
                }
            }
        }

        return $events;
    }

    private function toMetadata($raw): array
    {
        $meta = ['phoneNumberId' => $raw['phone_number_id'] ?? ''];
        if (isset($raw['display_phone_number'])) {
            $meta['displayPhoneNumber'] = $raw['display_phone_number'];
        }
        return $meta;
    }

    private function toMedia($raw): array
    {
        $raw = is_array($raw) ? $raw : [];
        $media = [];
        if (isset($raw['id'])) {
            $media['id'] = $raw['id'];
        }
        if (isset($raw['url'])) {
            $media['url'] = $raw['url'];
        }
        if (isset($raw['mime_type'])) {
            $media['mimeType'] = $raw['mime_type'];
        }
        if (isset($raw['sha256'])) {
            $media['sha256'] = $raw['sha256'];
        }
        if (isset($raw['caption'])) {
            $media['caption'] = $raw['caption'];
        }
        return $media;
    }

    /** Find the contact entry matching this message and map its profile. */
    private function resolveProfile($msg, $contacts): ?array
    {
        if (!is_array($contacts) || count($contacts) === 0) {
            return null;
        }

        $match = null;
        foreach ($contacts as $c) {
            if (!is_array($c)) {
                continue;
            }
            $byUserId = isset($msg['from_user_id']) && ($c['user_id'] ?? null) === $msg['from_user_id'];
            $byWaId = isset($msg['from']) && ($c['wa_id'] ?? null) === $msg['from'];
            if ($byUserId || $byWaId) {
                $match = $c;
                break;
            }
        }
        if ($match === null) {
            $match = is_array($contacts[0]) ? $contacts[0] : null;
        }

        $profile = is_array($match) ? ($match['profile'] ?? null) : null;
        if (!is_array($profile)) {
            return null;
        }

        $mapped = [];
        foreach (['name', 'username', 'picture'] as $k) {
            if (isset($profile[$k])) {
                $mapped[$k] = $profile[$k];
            }
        }
        return count($mapped) > 0 ? $mapped : null;
    }

    private function toReferral($raw): array
    {
        $raw = is_array($raw) ? $raw : [];
        $ref = [];
        $map = [
            'sourceUrl' => 'source_url',
            'sourceId' => 'source_id',
            'sourceType' => 'source_type',
            'headline' => 'headline',
            'body' => 'body',
            'mediaType' => 'media_type',
        ];
        foreach ($map as $out => $in) {
            if (isset($raw[$in])) {
                $ref[$out] = $raw[$in];
            }
        }
        return $ref;
    }

    /**
     * Parse a single message object (Meta format) into a normalized event.
     * Falls back to an `unsupported` event when the message type is unknown.
     */
    private function mapMessage($msg, array $base, $contacts): array
    {
        $msg = is_array($msg) ? $msg : [];

        $messageBase = array_merge($base, [
            'field' => 'messages',
            'from' => $msg['from'] ?? null,
            'messageId' => $msg['id'] ?? null,
            'timestamp' => $msg['timestamp'] ?? null,
        ]);

        if (isset($msg['from_user_id'])) {
            $messageBase['fromUserId'] = $msg['from_user_id'];
        }
        $profile = $this->resolveProfile($msg, $contacts);
        if ($profile !== null) {
            $messageBase['profile'] = $profile;
        }
        if (($msg['from_me'] ?? null) === true) {
            $messageBase['fromMe'] = true;
        }
        if (isset($msg['group_id'])) {
            $messageBase['groupId'] = $msg['group_id'];
        }
        if (is_array($msg['context'] ?? null)) {
            $messageBase['context'] = [
                'from' => $msg['context']['from'] ?? null,
                'id' => $msg['context']['id'] ?? null,
            ];
        }
        if (is_array($msg['referral'] ?? null)) {
            $messageBase['referral'] = $this->toReferral($msg['referral']);
        }

        // Edited messages carry an `edit` block regardless of the original type.
        if (is_array($msg['edit'] ?? null)) {
            return array_merge($messageBase, [
                'type' => 'edit',
                'edit' => [
                    'originalMessageId' => $msg['edit']['original_message_id'] ?? ($msg['edit']['originalMessageId'] ?? null),
                    'text' => $msg['edit']['text']['body'] ?? ($msg['edit']['text'] ?? null),
                ],
            ]);
        }

        switch ($msg['type'] ?? null) {
            case 'text':
                return array_merge($messageBase, ['type' => 'text', 'text' => ['body' => $msg['text']['body'] ?? '']]);

            case 'image':
                return array_merge($messageBase, ['type' => 'image', 'image' => $this->toMedia($msg['image'] ?? null)]);

            case 'audio':
                $audio = $this->toMedia($msg['audio'] ?? null);
                if (isset($msg['audio']['voice'])) {
                    $audio['voice'] = $msg['audio']['voice'];
                }
                return array_merge($messageBase, ['type' => 'audio', 'audio' => $audio]);

            case 'video':
                return array_merge($messageBase, ['type' => 'video', 'video' => $this->toMedia($msg['video'] ?? null)]);

            case 'document':
                $document = $this->toMedia($msg['document'] ?? null);
                if (isset($msg['document']['filename'])) {
                    $document['filename'] = $msg['document']['filename'];
                }
                return array_merge($messageBase, ['type' => 'document', 'document' => $document]);

            case 'sticker':
                $sticker = $this->toMedia($msg['sticker'] ?? null);
                if (isset($msg['sticker']['animated'])) {
                    $sticker['animated'] = $msg['sticker']['animated'];
                }
                return array_merge($messageBase, ['type' => 'sticker', 'sticker' => $sticker]);

            case 'location':
                return array_merge($messageBase, [
                    'type' => 'location',
                    'location' => [
                        'latitude' => $msg['location']['latitude'] ?? null,
                        'longitude' => $msg['location']['longitude'] ?? null,
                        'name' => $msg['location']['name'] ?? null,
                        'address' => $msg['location']['address'] ?? null,
                    ],
                ]);

            case 'contacts':
                return array_merge($messageBase, [
                    'type' => 'contacts',
                    'contacts' => is_array($msg['contacts'] ?? null) ? $msg['contacts'] : [],
                ]);

            case 'reaction':
                $messageId = $msg['reaction']['message_id'] ?? ($msg['reaction']['messageId'] ?? null);
                $emoji = $msg['reaction']['emoji'] ?? null;
                if ($emoji) {
                    return array_merge($messageBase, ['type' => 'reaction', 'reaction' => ['messageId' => $messageId, 'emoji' => $emoji]]);
                }
                return array_merge($messageBase, ['type' => 'reaction-removed', 'reaction' => ['messageId' => $messageId]]);

            case 'button':
                return array_merge($messageBase, [
                    'type' => 'button',
                    'button' => ['text' => $msg['button']['text'] ?? null, 'payload' => $msg['button']['payload'] ?? null],
                ]);

            case 'interactive':
                $interactive = is_array($msg['interactive'] ?? null) ? $msg['interactive'] : [];
                if (is_array($interactive['list_reply'] ?? null)) {
                    return array_merge($messageBase, [
                        'type' => 'list-reply',
                        'listReply' => [
                            'id' => $interactive['list_reply']['id'] ?? null,
                            'title' => $interactive['list_reply']['title'] ?? null,
                            'description' => $interactive['list_reply']['description'] ?? null,
                        ],
                    ]);
                }
                if (is_array($interactive['button_reply'] ?? null)) {
                    return array_merge($messageBase, [
                        'type' => 'button-reply',
                        'buttonReply' => [
                            'id' => $interactive['button_reply']['id'] ?? null,
                            'title' => $interactive['button_reply']['title'] ?? null,
                        ],
                    ]);
                }
                return array_merge($messageBase, ['type' => 'unsupported', 'errors' => $msg['errors'] ?? null]);

            default:
                if (is_array($msg['referral'] ?? null)) {
                    return array_merge($messageBase, ['type' => 'referral', 'referral' => $this->toReferral($msg['referral'])]);
                }
                return array_merge($messageBase, ['type' => 'unsupported', 'errors' => $msg['errors'] ?? null]);
        }
    }

    /**
     * Map a single change object into a list of normalized events.
     *
     * @return array<int, array<string, mixed>>
     */
    private function mapChange(array $change, array $base): array
    {
        $value = is_array($change['value'] ?? null) ? $change['value'] : [];
        $events = [];

        switch ($change['field'] ?? null) {
            case 'messages':
                if (is_array($value['messages'] ?? null)) {
                    foreach ($value['messages'] as $msg) {
                        $events[] = $this->mapMessage($msg, $base, $value['contacts'] ?? null);
                    }
                }
                if (is_array($value['statuses'] ?? null)) {
                    foreach ($value['statuses'] as $st) {
                        $st = is_array($st) ? $st : [];
                        $events[] = array_merge($base, [
                            'field' => 'messages',
                            'type' => 'status',
                            'status' => $st['status'] ?? null,
                            'messageId' => $st['id'] ?? null,
                            'recipientId' => $st['recipient_id'] ?? null,
                            'timestamp' => $st['timestamp'] ?? null,
                            'recipientType' => $st['recipient_type'] ?? null,
                            'participantId' => $st['participant_id'] ?? null,
                            'errors' => $st['errors'] ?? null,
                            'conversation' => $st['conversation'] ?? null,
                            'pricing' => $st['pricing'] ?? null,
                        ]);
                    }
                }
                break;

            case 'presence':
                $presence = is_array($value['presence'] ?? null) ? $value['presence'] : [];
                $events[] = array_merge($base, [
                    'field' => 'presence',
                    'type' => 'presence',
                    'waId' => $presence['wa_id'] ?? null,
                    'status' => $presence['status'] ?? null,
                ]);
                break;

            case 'connection':
                $connection = is_array($value['connection'] ?? null) ? $value['connection'] : [];
                if (($connection['status'] ?? null) === 'close') {
                    $events[] = array_merge($base, [
                        'field' => 'connection',
                        'type' => 'connection.close',
                        'code' => $connection['code'] ?? null,
                        'reason' => $connection['reason'] ?? null,
                    ]);
                } else {
                    $events[] = array_merge($base, [
                        'field' => 'connection',
                        'type' => 'connection.open',
                        'code' => $connection['code'] ?? null,
                    ]);
                }
                break;

            case 'qrcode':
                $events[] = array_merge($base, [
                    'field' => 'qrcode',
                    'type' => 'qrcode',
                    'code' => $value['qrcode']['code'] ?? null,
                ]);
                break;

            case 'call':
                $calls = is_array($value['calls'] ?? null) ? $value['calls'] : [];
                foreach ($calls as $c) {
                    $c = is_array($c) ? $c : [];
                    $events[] = array_merge($base, [
                        'field' => 'call',
                        'type' => 'call',
                        'callId' => $c['id'] ?? null,
                        'from' => $c['from'] ?? null,
                        'status' => $c['status'] ?? null,
                        'isVideo' => $c['is_video'] ?? null,
                        'isGroup' => $c['is_group'] ?? null,
                        'timestamp' => $c['timestamp'] ?? null,
                    ]);
                }
                break;

            case 'groups':
                $groups = is_array($value['groups'] ?? null) ? $value['groups'] : [];
                if (($groups['event'] ?? null) === 'update') {
                    $events[] = array_merge($base, [
                        'field' => 'groups',
                        'type' => 'group.update',
                        'data' => is_array($groups['data'] ?? null) ? $groups['data'] : [],
                    ]);
                } else {
                    $events[] = array_merge($base, [
                        'field' => 'groups',
                        'type' => 'group.participants',
                        'groupId' => $groups['id'] ?? ($groups['group_id'] ?? null),
                        'action' => $groups['action'] ?? null,
                        'participants' => is_array($groups['participants'] ?? null) ? $groups['participants'] : [],
                    ]);
                }
                break;

            case 'health':
                $health = is_array($value['health'] ?? null) ? $value['health'] : [];
                $events[] = array_merge($base, [
                    'field' => 'health',
                    'type' => 'health',
                    'status' => $health['status'] ?? null,
                    'previous' => $health['previous'] ?? null,
                    'reason' => $health['reason'] ?? null,
                    'shouldPause' => $health['should_pause'] ?? null,
                    'shouldRotate' => $health['should_rotate'] ?? null,
                ]);
                break;

            default:
                $events[] = array_merge($base, ['type' => 'unknown']);
                break;
        }

        return $events;
    }
}
