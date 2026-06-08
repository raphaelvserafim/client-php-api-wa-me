<?php

use Api\Wame\WhatsApp;

include_once 'vendor/autoload.php';

$wa = new WhatsApp([
    'server' => 'https://server.api-wa.me',
    'key' => 'YOUR_KEY',
]);

// Instance
$wa->instance->connect();
$wa->instance->getInfo();
$wa->instance->updateProfileName('My Bot');
$wa->instance->updateProfilePicture('https://example.com/photo.jpg');

// Messages
$wa->message->sendText('5511999999999', 'Hello!');
$wa->message->sendImage('5511999999999', 'https://example.com/image.jpg', 'Caption');
$wa->message->sendVideo('5511999999999', 'https://example.com/video.mp4', 'Video caption');
$wa->message->sendAudio('5511999999999', 'https://example.com/audio.mp3');
$wa->message->sendDocument('5511999999999', 'https://example.com/doc.pdf', 'application/pdf', 'doc.pdf');
$wa->message->sendLocation('5511999999999', -23.5505, -46.6333, 'Sao Paulo, Brazil');
$wa->message->sendContact('5511999999999', 'John Doe', '5511888888888');
$wa->message->sendReaction('👍', 'MESSAGE_ID');
$wa->message->sendSticker('5511999999999', 'https://example.com/sticker.webp');
$wa->message->sendPoll('5511999999999', 'Favorite color?', ['Red', 'Blue', 'Green']);

// Reply to a message
$wa->message->replyText('MESSAGE_ID', '5511999999999', 'This is a reply!');
$wa->message->replyImage('MESSAGE_ID', '5511999999999', 'https://example.com/image.jpg', 'Reply caption');

// Buttons
$wa->message->sendButtonAction([
    'to' => '5511999999999',
    'header' => ['title' => 'Header'],
    'text' => 'Choose an option',
    'footer' => 'Footer',
    'buttons' => [
        ['type' => 'quick_reply', 'id' => 'btn1', 'text' => 'Option 1'],
        ['type' => 'url', 'text' => 'Visit', 'url' => 'https://api-wa.me'],
    ],
]);

// List message
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

// Groups
$wa->group->create('Dev Team', ['5511999999999']);
$wa->group->list();
$wa->group->addParticipants('GROUP_ID', ['5511888888888']);
$wa->group->promote('GROUP_ID', ['5511888888888']);

// Contacts
$wa->contact->list();
$wa->contact->getProfile('5511999999999');
$wa->contact->block('5511999999999');
$wa->contact->isRegistered('5511999999999');

// Labels
$wa->label->list();
$wa->label->create('VIP');
$wa->label->addToChat('LABEL_ID', '5511999999999');

// Newsletter
$wa->newsletter->create('My Newsletter', 'Newsletter description');
$wa->newsletter->follow('NEWSLETTER_ID');

// Community
$wa->community->create('Dev Community', 'Community subject');
$wa->community->list();

// Status/Stories
$wa->status->sendText('Hello World!');
$wa->status->sendImage('https://example.com/image.jpg', 'My status');

// Chat
$wa->chat->getAll();
$wa->chat->getMessages('5511999999999', 1, 20);
$wa->chat->getPrivacy();

// Calls
$wa->call->make('5511999999999');

// Business
$wa->business->listCatalog();

// Webhook (use in your webhook endpoint)
$parsed = $wa->webhook->parse();
if ($parsed) {
    echo $parsed->messageType; // text, image, audio, etc.
    echo $parsed->text ?? '';
    echo $parsed->remoteJid;
}
