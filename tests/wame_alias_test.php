<?php

use Api\Wame\Wame;
use Api\Wame\WhatsApp;
use Api\Wame\Provider;

echo "wame_alias_test\n";

$init = ['server' => 'http://test.local', 'key' => 'k'];

$wame = new Wame($init);
$wa = new WhatsApp($init);

// --- WhatsApp is a Wame; behaves identically ---
ok($wa instanceof Wame, 'WhatsApp extends Wame');
ok($wame->message instanceof \Api\Wame\Message, 'Wame wires message service');
ok($wa->message instanceof \Api\Wame\Message, 'WhatsApp wires message service');
ok($wame->webhook instanceof \Api\Wame\Webhook, 'Wame wires webhook');

// --- provider from Init reaches the Message service (both classes) ---
$readDefault = function (Wame $c): ?string {
    $rp = new ReflectionProperty(\Api\Wame\Message::class, 'defaultProvider');
    $rp->setAccessible(true);
    return $rp->getValue($c->message);
};

eq(null, $readDefault(new Wame($init)), 'no provider: default null');
eq(Provider::INSTAGRAM, $readDefault(new Wame($init + ['provider' => Provider::INSTAGRAM])), 'Wame passes provider to Message');
eq(Provider::MESSENGER, $readDefault(new WhatsApp($init + ['provider' => Provider::MESSENGER])), 'WhatsApp passes provider to Message');

// --- Provider helper ---
ok(Provider::isValid('whatsapp'), 'Provider::isValid whatsapp');
ok(!Provider::isValid('telegram'), 'Provider::isValid rejects unknown');
