<?php

use Api\Wame\Webhook;

echo "webhook_meta_test\n";

$wh = new Webhook();
$fx = fn(string $f) => json_decode(file_get_contents(__DIR__ . '/fixtures/' . $f), true);

// --- WhatsApp text: provider=whatsapp, official=false, profile.name only ---
$events = $wh->parseMeta($fx('message-text.json'));
eq(1, count($events), 'whatsapp: one event');
$e = $events[0];
eq('text', $e['type'], 'whatsapp: type text');
eq('whatsapp', $e['provider'], 'whatsapp: provider');
eq(false, $e['official'], 'whatsapp: official false');
eq('5511999998888', $e['from'], 'whatsapp: from');
eq('Olá!', $e['text']['body'], 'whatsapp: text body');
eq('Fulano', $e['profile']['name'], 'whatsapp: profile name');
ok(!isset($e['fromUserId']), 'whatsapp: no fromUserId');
ok(!isset($e['profile']['username']), 'whatsapp: no username');

// --- Instagram text: provider=instagram, official=true, fromUserId + username + picture ---
$events = $wh->parseMeta($fx('message-text-instagram.json'));
$e = $events[0];
eq('instagram', $e['provider'], 'instagram: provider');
eq(true, $e['official'], 'instagram: official true');
eq('1700000000000000', $e['fromUserId'], 'instagram: fromUserId');
eq('Fulano', $e['profile']['name'], 'instagram: profile name');
eq('fulano', $e['profile']['username'], 'instagram: profile username');
eq('https://cdn.instagram.com/pic.jpg', $e['profile']['picture'], 'instagram: profile picture');
eq('Olá!', $e['text']['body'], 'instagram: text body');

// --- Messenger text: provider=messenger, official=true, fromUserId + picture (no username) ---
$events = $wh->parseMeta($fx('message-text-messenger.json'));
$e = $events[0];
eq('messenger', $e['provider'], 'messenger: provider');
eq(true, $e['official'], 'messenger: official true');
eq('2600000000000000', $e['fromUserId'], 'messenger: fromUserId');
eq('Fulano', $e['profile']['name'], 'messenger: profile name');
eq('https://cdn.facebook.com/pic.jpg', $e['profile']['picture'], 'messenger: profile picture');
ok(!isset($e['profile']['username']), 'messenger: no username');

// --- Status event ---
$events = $wh->parseMeta($fx('status-read.json'));
$e = $events[0];
eq('status', $e['type'], 'status: type');
eq('read', $e['status'], 'status: read');
eq('WAMID_TEXT', $e['messageId'], 'status: messageId');
eq('5511999998888', $e['recipientId'], 'status: recipientId');
eq('whatsapp', $e['provider'], 'status: provider');

// --- Robustness: invalid bodies never throw, return [] ---
eq([], $wh->parseMeta([]), 'invalid: empty array');
eq([], $wh->parseMeta(['object' => 'wame']), 'invalid: no entry');
eq([], $wh->parseMeta(['entry' => 'nope']), 'invalid: entry not array');

// --- Base fields present ---
$e = $wh->parseMeta($fx('message-text.json'))[0];
eq('messages', $e['field'], 'base: field messages');
eq('your-instance-id', $e['metadata']['phoneNumberId'], 'base: phoneNumberId');
eq('WAMID_TEXT', $e['messageId'], 'base: messageId');
ok(isset($e['raw']['object']), 'base: raw envelope kept');
