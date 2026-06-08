# WhatsApp API - PHP Client

PHP client library for [api-wa.me](https://api-wa.me) - Unofficial WhatsApp API integration.

Send and receive messages, manage contacts, groups, communities, newsletters, labels, business catalog and more.

## Requirements

- PHP >= 8.1
- ext-curl

## Installation

```bash
composer require raphaelvserafim/client-php-api-wa-me
```

## Quick Start

```php
use Api\Wame\WhatsApp;

require 'vendor/autoload.php';

$wa = new WhatsApp([
    'server' => 'https://server.api-wa.me',
    'key'    => 'YOUR_KEY',
]);

// Send a text message
$wa->message->sendText('5511999999999', 'Hello!');
```

## Architecture

The client is organized into domain-specific modules accessible via the main `WhatsApp` class:

| Property | Class | Description |
|----------|-------|-------------|
| `$wa->instance` | `Instance` | Connection, settings, profile, proxy, mobile registration |
| `$wa->message` | `Message` | Send/reply all message types |
| `$wa->chat` | `Chat` | Chat management, messages history, privacy |
| `$wa->contact` | `Contact` | Contacts CRUD, block/unblock |
| `$wa->group` | `Group` | Groups CRUD, members, roles, invites |
| `$wa->community` | `Community` | Communities CRUD, settings, invites |
| `$wa->label` | `Label` | Labels CRUD, assign to chats |
| `$wa->newsletter` | `Newsletter` | Newsletters CRUD, follow, messages |
| `$wa->business` | `Business` | Product catalog management |
| `$wa->status` | `Status` | Status/Stories (text, image, video, audio) |
| `$wa->call` | `Call` | Make, accept, reject, end calls |
| `$wa->webhook` | `Webhook` | Parse incoming webhook payloads |

---

## Instance

```php
// Connect via QR Code
$wa->instance->connect();

// Connect via Pairing Code
$wa->instance->connectPairingCode('5511999999999');

// Get instance info
$wa->instance->getInfo();

// Update settings
$wa->instance->updateSettings(
    markMessageRead: true,
    saveMedia: true,
    receiveStatusMessage: false,
    receivePresence: false
);

// Update webhook
$wa->instance->updateWebhook([
    'allowWebhook'         => true,
    'allowNumber'          => 'all',
    'webhookMessage'       => 'https://yourserver.com/webhook',
    'webhookGroup'         => '',
    'webhookConnection'    => '',
    'webhookQrCode'        => '',
    'webhookMessageFromMe' => '',
    'webhookHistory'       => '',
]);

// Logout
$wa->instance->logout();

// Profile
$wa->instance->updateProfileName('My Bot');
$wa->instance->updateProfilePicture('https://example.com/photo.jpg');
$wa->instance->removeProfilePicture();
$wa->instance->updateProfileStatus('Available');

// Proxy
$wa->instance->setProxy('http://user:pass@ip:port');

// Database
$wa->instance->addMongoDB('mongodb://localhost:27017', 'mydb');

// Resync & Restart
$wa->instance->resync();
$wa->instance->restart();

// Mobile Registration (3 steps)
$wa->instance->prepareMobileRegistration('55', '11999999999', '11');
$wa->instance->requestMobileCode('sms');
$wa->instance->verifyMobileCode('123456');

// Webhook Statistics
$wa->instance->getWebhookStatistics();
```

## Messages

### Text

```php
$wa->message->sendText('5511999999999', 'Hello!');

// With title and footer
$wa->message->sendTitle('5511999999999', 'Title', 'Message body', 'Footer text');
```

### Media

```php
// Image
$wa->message->sendImage('5511999999999', 'https://example.com/image.jpg', 'Caption');

// Video
$wa->message->sendVideo('5511999999999', 'https://example.com/video.mp4', 'Caption');

// Audio
$wa->message->sendAudio('5511999999999', 'https://example.com/audio.mp3');

// Document
$wa->message->sendDocument('5511999999999', 'https://example.com/doc.pdf', 'application/pdf', 'doc.pdf', 'Caption');

// Sticker
$wa->message->sendSticker('5511999999999', 'https://example.com/sticker.webp');

// Video Note (circular/PTV)
$wa->message->sendVideoNote('5511999999999', 'https://example.com/video.mp4');
```

### Base64 Media

```php
$wa->message->sendBase64Image('5511999999999', $base64String, 'Caption');
$wa->message->sendBase64Audio('5511999999999', $base64String);
$wa->message->sendBase64Document('5511999999999', $base64String, 'application/pdf', 'doc.pdf', 'Caption');
```

### Location

```php
// Static location
$wa->message->sendLocation('5511999999999', -23.5505, -46.6333, 'Sao Paulo, Brazil');

// Live location
$wa->message->sendLiveLocation('5511999999999', -23.5505, -46.6333, 'I am here!');
```

### Contact

```php
// Single contact
$wa->message->sendContact('5511999999999', 'John Doe', '5511888888888', 'Company Inc');

// Multiple contacts
$wa->message->sendContacts('5511999999999', 'My Contacts', [
    ['fullName' => 'John Doe', 'phoneNumber' => '5511888888888', 'organization' => 'Company'],
    ['fullName' => 'Jane Doe', 'phoneNumber' => '5511777777777'],
]);
```

### Link Preview

```php
$wa->message->sendLink(
    '5511999999999',
    'Raphael Serafim',
    'Check out this profile',
    'https://avatars.githubusercontent.com/u/68257896',
    'https://github.com/raphaelvserafim',
    'Software Developer'
);
```

### Reaction

```php
$wa->message->sendReaction('👍', 'MESSAGE_ID');
```

### Presence

```php
// composing, recording, paused, available, unavailable
$wa->message->sendPresence('5511999999999', 'composing');
```

### Buttons

```php
// Button with call to action
$wa->message->sendButtonAction([
    'to' => '5511999999999',
    'header' => [
        'title' => 'Header Title',
        'hasMediaAttachment' => true,
        'imageMessage' => ['url' => 'https://example.com/image.png'],
    ],
    'text' => 'Choose an option',
    'footer' => 'Footer text',
    'buttons' => [
        ['type' => 'cta_url', 'url' => 'https://api-wa.me', 'text' => 'Visit website'],
        ['type' => 'cta_call', 'phone_number' => '+5511999999999', 'text' => 'Call us'],
        ['type' => 'cta_copy', 'copy_code' => '1234567890', 'text' => 'Copy code'],
    ],
]);

// Button with quick reply
$wa->message->sendButtonReply([
    'to' => '5511999999999',
    'header' => ['title' => 'Header'],
    'text' => 'Choose an option',
    'footer' => 'Footer',
    'buttons' => [
        ['type' => 'quick_reply', 'id' => 'yes', 'text' => 'Yes'],
        ['type' => 'quick_reply', 'id' => 'no', 'text' => 'No'],
    ],
]);
```

### List

```php
$wa->message->sendList([
    'to' => '5511999999999',
    'buttonText' => 'View options',
    'text' => 'Select an option:',
    'title' => 'Main Menu',
    'footer' => 'Bot',
    'sections' => [
        [
            'title' => 'Services',
            'rows' => [
                ['title' => 'Support', 'description' => 'Talk to an agent', 'rowId' => 'support'],
                ['title' => 'Sales', 'description' => 'Buy products', 'rowId' => 'sales'],
            ],
        ],
    ],
]);
```

### Poll

```php
$wa->message->sendPoll('5511999999999', 'Favorite color?', ['Red', 'Blue', 'Green'], 1);
```

### Survey

```php
$wa->message->sendSurvey('5511999999999', 'Do you like PHP?', ['Yes', 'No']);
```

### Event

```php
$wa->message->sendEvent(
    '5511999999999',
    'Team Meeting',
    'Weekly sync',
    '2026-06-10T14:00:00Z',
    'Office HQ',
    '123 Main St'
);
```

### Pix Payment (Brazil)

```php
$wa->message->sendPix([
    'to' => '5511999999999',
    'title' => 'Pizza',
    'text' => 'Pizza order',
    'referenceId' => 'order123',
    'key' => '23711695000115',
    'merchantName' => 'My Store',
    'keyType' => 'CNPJ',
    'items' => [
        ['id' => '1', 'name' => 'Pizza', 'price' => 50, 'quantity' => 2],
    ],
    'subtotal' => 100,
    'totalAmount' => 100,
]);
```

### Pin / Unpin

```php
$wa->message->pin('MESSAGE_ID', 604800); // duration in seconds (7 days)
$wa->message->unpin('MESSAGE_ID');
```

### Call Link

```php
$wa->message->sendCallLink('5511999999999', 'audio', 'Let\'s talk!');
$wa->message->createCallLink('video');
```

### Product & Group Invite

```php
$wa->message->sendProduct([
    'to' => '5511999999999',
    'businessOwnerJid' => 'owner@s.whatsapp.net',
    'productId' => 'PRODUCT_ID',
    'catalogId' => 'CATALOG_ID',
    'body' => 'Check this product',
]);

$wa->message->sendGroupInvite([
    'to' => '5511999999999',
    'groupJid' => 'GROUP_ID@g.us',
    'groupName' => 'Dev Team',
    'inviteCode' => 'INVITE_CODE',
    'caption' => 'Join us!',
]);
```

### Get Message Details & Download Media

```php
$wa->message->getDetails('MESSAGE_ID');
$wa->message->downloadMedia('MESSAGE_ID', 'json');   // json (base64) or binary
```

### Reply to Messages

All reply methods require the original message ID as the first parameter:

```php
$wa->message->replyText('MSG_ID', '5511999999999', 'This is a reply!');
$wa->message->replyImage('MSG_ID', '5511999999999', 'https://example.com/image.jpg', 'Caption');
$wa->message->replyVideo('MSG_ID', '5511999999999', 'https://example.com/video.mp4', 'Caption');
$wa->message->replyAudio('MSG_ID', '5511999999999', 'https://example.com/audio.mp3');
$wa->message->replyDocument('MSG_ID', '5511999999999', 'https://example.com/doc.pdf', 'application/pdf', 'doc.pdf');
$wa->message->replyContact('MSG_ID', '5511999999999', 'John Doe', '5511888888888');
$wa->message->replyLocation('MSG_ID', '5511999999999', -23.5505, -46.6333, 'Sao Paulo');
$wa->message->replyTitle('MSG_ID', '5511999999999', 'Title', 'Text', 'Footer');
$wa->message->replyButtonReply('MSG_ID', $buttonBody);
$wa->message->replyButtonAction('MSG_ID', $buttonBody);
$wa->message->replyPix('MSG_ID', $pixBody);
```

## Chat

```php
$wa->chat->getAll();
$wa->chat->getMessages('5511999999999', 1, 20);     // chatId, page, limit
$wa->chat->modify('5511999999999', 'markRead', true); // markRead, pin, etc.
$wa->chat->delete('5511999999999');
$wa->chat->subscribePresence('5511999999999@s.whatsapp.net');
$wa->chat->setDisappearing('5511999999999@s.whatsapp.net', 86400);
$wa->chat->getPrivacy();
```

## Contacts

```php
$wa->contact->list();
$wa->contact->add('5511999999999', 'John Doe');
$wa->contact->getProfile('5511999999999');
$wa->contact->remove('5511999999999');
$wa->contact->getStatus('5511999999999');
$wa->contact->block('5511999999999');
$wa->contact->unblock('5511999999999');
$wa->contact->listBlocked();
$wa->contact->clearSession('5511999999999');
$wa->contact->isRegistered('5511999999999');
$wa->contact->resolveLids(['lid1@lid', 'lid2@lid']);
```

## Groups

```php
$wa->group->list();
$wa->group->create('Dev Team', ['5511999999999', '5511888888888']);
$wa->group->get('GROUP_ID');
$wa->group->update('GROUP_ID', 'New Name', 'New description');
$wa->group->leave('GROUP_ID');

// Members
$wa->group->getMembers('GROUP_ID');
$wa->group->addParticipants('GROUP_ID', ['5511999999999']);
$wa->group->removeParticipants('GROUP_ID', ['5511999999999']);
$wa->group->promote('GROUP_ID', ['5511999999999']);
$wa->group->demote('GROUP_ID', ['5511999999999']);

// Invite
$wa->group->getInviteCode('GROUP_ID');
$wa->group->getInviteInfo('INVITE_CODE');

// Picture
$wa->group->updatePicture('GROUP_ID', 'https://example.com/photo.jpg');
$wa->group->removePicture('GROUP_ID');

// Settings
$wa->group->changeSettings('GROUP_ID', 'announcement'); // announcement, not_announcement, locked, unlocked

// Join Requests
$wa->group->getRequestParticipants('GROUP_ID');
$wa->group->handleRequestParticipants('GROUP_ID', ['5511999999999'], 'approve'); // approve or reject
```

## Communities

```php
$wa->community->list();
$wa->community->create('Dev Community', 'Community subject');
$wa->community->get('COMMUNITY_ID');
$wa->community->update('COMMUNITY_ID', 'Updated subject', 'Updated description');
$wa->community->leave('COMMUNITY_ID');

// Picture
$wa->community->updatePicture('COMMUNITY_ID', 'https://example.com/photo.jpg');

// Invite
$wa->community->getInviteCode('COMMUNITY_ID');
$wa->community->getInviteInfo('INVITE_CODE');
$wa->community->acceptInvite('INVITE_CODE');

// Participants
$wa->community->removeParticipants('COMMUNITY_ID', ['5511999999999']);
$wa->community->getRequestParticipants('COMMUNITY_ID');
$wa->community->handleRequestParticipants('COMMUNITY_ID', ['5511999999999'], 'approve');

// Sub-groups
$wa->community->createGroup('COMMUNITY_ID', 'Sub Group', ['5511999999999']);

// Settings
$wa->community->setEphemeral('COMMUNITY_ID', 86400);
$wa->community->updateSettings('COMMUNITY_ID', 'announcement');
$wa->community->setMemberAddMode('COMMUNITY_ID', 'admin_add');
$wa->community->setJoinApproval('COMMUNITY_ID', 'on');
```

## Labels

```php
$wa->label->list();
$wa->label->create('VIP', 'label_id_optional');
$wa->label->getChats('LABEL_ID');
$wa->label->addToChat('LABEL_ID', '5511999999999');
$wa->label->removeFromChat('LABEL_ID', '5511999999999');
$wa->label->delete('LABEL_ID');
```

## Newsletter

```php
$wa->newsletter->create('My Newsletter', 'Description');
$wa->newsletter->getMetadata('jid', 'NEWSLETTER_JID');
$wa->newsletter->getSubscribers('NEWSLETTER_ID');
$wa->newsletter->getAdmins('NEWSLETTER_ID');

// Follow / Unfollow
$wa->newsletter->follow('NEWSLETTER_ID');
$wa->newsletter->unfollow('NEWSLETTER_ID');

// Update
$wa->newsletter->updateName('NEWSLETTER_ID', 'New Name');
$wa->newsletter->updateDescription('NEWSLETTER_ID', 'New description');
$wa->newsletter->updatePicture('NEWSLETTER_ID', 'https://example.com/photo.jpg');
$wa->newsletter->removePicture('NEWSLETTER_ID');

// Messages
$wa->newsletter->getMessages('NEWSLETTER_ID', 10);
$wa->newsletter->react('NEWSLETTER_ID', 'SERVER_ID', '👍');

// Mute / Unmute
$wa->newsletter->mute('NEWSLETTER_ID');
$wa->newsletter->unmute('NEWSLETTER_ID');

// Admin
$wa->newsletter->transferOwnership('NEWSLETTER_ID', 'new_owner@s.whatsapp.net');
$wa->newsletter->demoteAdmin('NEWSLETTER_ID', 'user@s.whatsapp.net');

// Delete
$wa->newsletter->delete('NEWSLETTER_ID');
```

## Business Catalog

```php
$wa->business->listCatalog(10, '');
$wa->business->createProduct([
    'name' => 'Product',
    'description' => 'Description',
    'originCountryCode' => 'BR',
    'currency' => 'BRL',
    'price' => 29.90,
    'images' => [['url' => 'https://example.com/product.jpg']],
]);
$wa->business->updateProduct('PRODUCT_ID', ['name' => 'Updated Product']);
$wa->business->deleteProduct('PRODUCT_ID');
```

## Status / Stories

```php
$wa->status->sendText('Hello World!');
$wa->status->sendImage('https://example.com/image.jpg', 'Caption');
$wa->status->sendVideo('https://example.com/video.mp4', 'Caption');
$wa->status->sendAudio('https://example.com/audio.mp3');

// Limit visibility to specific contacts
$wa->status->sendText('VIP only', ['5511999999999@s.whatsapp.net']);

// Mention
$wa->status->sendMention('5511999999999@s.whatsapp.net', 'STATUS_MSG_ID');
```

## Calls

```php
$wa->call->make('5511999999999');
$wa->call->accept('CALL_ID', 'CALLER_JID');
$wa->call->reject('CALL_ID', 'CALLER_JID');
$wa->call->end('CALL_ID', 'PEER_JID');
```

## Webhook

Use in your webhook endpoint to parse incoming messages:

```php
$parsed = $wa->webhook->parse();

if ($parsed) {
    $parsed->remoteJid;    // sender number
    $parsed->msgId;        // message ID
    $parsed->pushName;     // sender name
    $parsed->messageType;  // text, image, audio, video, document, sticker, location, contact, button, list, reaction, liveLocation
    $parsed->text;         // text content (for text messages)
}
```

### Example: Auto-reply bot

```php
$parsed = $wa->webhook->parse();

if ($parsed && $parsed->messageType === 'text') {
    if ($parsed->text === 'Hi') {
        $wa->message->sendText($parsed->remoteJid, 'Hello! How can I help you?');
    }
}
```

### Media webhook properties

```php
if ($parsed && in_array($parsed->messageType, ['image', 'video', 'audio', 'document', 'sticker'])) {
    $parsed->mimetype;     // e.g. image/jpeg
    $parsed->mediaURL;     // direct download URL
    $parsed->mediaBase64;  // base64 encoded content
    $parsed->thumbnail;    // base64 thumbnail (images/videos)
    $parsed->caption;      // media caption
    $parsed->fileName;     // document filename
    $parsed->messageKeys;  // array with mediaKey, directPath, url
}
```

### Button / List webhook properties

```php
if ($parsed && $parsed->messageType === 'button') {
    $parsed->selectedId; // selected button ID
}

if ($parsed && $parsed->messageType === 'list') {
    $parsed->selectedId; // selected row ID
    $parsed->title;      // selected row title
}
```

### Contact webhook properties

```php
if ($parsed && $parsed->messageType === 'contact') {
    foreach ($parsed->contact as $contact) {
        $contact->name;   // contact name
        $contact->number; // phone number
    }
}
```

## Backward Compatibility

All methods from the previous version still work but are marked as `@deprecated`. They delegate to the new module methods internally:

```php
// Old way (still works, but deprecated)
$wa->sendText('5511999999999', 'Hello');
$wa->listContacts();
$wa->createGroup('Name', ['5511999999999']);

// New way (recommended)
$wa->message->sendText('5511999999999', 'Hello');
$wa->contact->list();
$wa->group->create('Name', ['5511999999999']);
```

## Support

- **Email:** [support@api-wa.me](mailto:support@api-wa.me)
- **Website:** [https://api-wa.me](https://api-wa.me)
- **Sign up:** [https://api-wa.me/sign-up](https://api-wa.me/sign-up)

## License

MIT
