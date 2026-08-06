<?php

use Api\Wame\Message;
use Api\Wame\Provider;

echo "provider_send_test\n";

// --- No default provider: field omitted (API assumes whatsapp) ---
$http = new FakeHttpClient();
$msg = new Message($http);
$msg->sendText('5511999999999', 'hi');
ok(!isset($http->last['body']['provider']), 'no default: provider omitted');
eq('/message/text', $http->last['path'], 'no default: path');

// --- Client default injected ---
$http = new FakeHttpClient();
$msg = new Message($http, Provider::INSTAGRAM);
$msg->sendText('5511999999999', 'hi');
eq('instagram', $http->last['body']['provider'], 'default: injected on sendText');

$msg->sendImage('to', 'url', 'cap');
eq('instagram', $http->last['body']['provider'], 'default: injected on sendImage');

$msg->sendAudio('to', 'url');
eq('instagram', $http->last['body']['provider'], 'default: injected on sendAudio');

$msg->sendVideo('to', 'url', 'cap');
eq('instagram', $http->last['body']['provider'], 'default: injected on sendVideo');

$msg->sendDocument('to', 'url', 'application/pdf', 'f.pdf', 'cap');
eq('instagram', $http->last['body']['provider'], 'default: injected on sendDocument');

// --- Per-call override wins over client default ---
$msg->sendText('5511999999999', 'hi', Provider::WHATSAPP);
eq('whatsapp', $http->last['body']['provider'], 'override: per-call wins');

// --- Array-body sends respect explicit provider, else receive default ---
$msg->sendButtonAction(['to' => 'x', 'provider' => Provider::MESSENGER]);
eq('messenger', $http->last['body']['provider'], 'button: explicit provider respected');

$msg->sendButtonAction(['to' => 'x']);
eq('instagram', $http->last['body']['provider'], 'button: default injected');

$msg->sendTemplate(['to' => 'x', 'name' => 'welcome']);
eq('instagram', $http->last['body']['provider'], 'template: default injected');
eq('/message/template', $http->last['path'], 'template: path');
