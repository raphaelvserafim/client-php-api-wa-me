<?php

namespace Api\Wame;

use stdClass;

class Webhook
{
    public ?stdClass $from = null;

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
}
