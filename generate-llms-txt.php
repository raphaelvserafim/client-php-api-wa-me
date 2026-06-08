<?php

/**
 * Script to generate llms.txt from the PHP client library source code.
 *
 * Usage: php generate-llms-txt.php > llms.txt
 *
 * Reads all src/*.php files via reflection and produces a structured
 * plain-text document describing installation, architecture, and
 * every public method with its signature and parameters.
 */

require __DIR__ . '/vendor/autoload.php';

$classes = [
    'Instance'   => Api\Wame\Instance::class,
    'Message'    => Api\Wame\Message::class,
    'Chat'       => Api\Wame\Chat::class,
    'Contact'    => Api\Wame\Contact::class,
    'Group'      => Api\Wame\Group::class,
    'Community'  => Api\Wame\Community::class,
    'Label'      => Api\Wame\Label::class,
    'Newsletter' => Api\Wame\Newsletter::class,
    'Business'   => Api\Wame\Business::class,
    'Status'     => Api\Wame\Status::class,
    'Call'       => Api\Wame\Call::class,
    'Webhook'    => Api\Wame\Webhook::class,
];

$composerJson = json_decode(file_get_contents(__DIR__ . '/composer.json'), true);
$version = $composerJson['version'] ?? 'latest';
$packageName = $composerJson['name'] ?? 'raphaelvserafim/client-php-api-wa-me';

// --- Helpers ---

function formatParam(ReflectionParameter $param): string
{
    $parts = [];

    $type = $param->getType();
    if ($type instanceof ReflectionNamedType) {
        $typeName = $type->getName();
        if ($type->allowsNull() && !$param->isDefaultValueAvailable()) {
            $typeName = '?' . $typeName;
        }
        $parts[] = $typeName;
    } elseif ($type instanceof ReflectionUnionType) {
        $parts[] = implode('|', array_map(fn($t) => $t->getName(), $type->getTypes()));
    }

    $parts[] = '$' . $param->getName();

    if ($param->isDefaultValueAvailable()) {
        $default = $param->getDefaultValue();
        $parts[] = '= ' . formatDefault($default);
    }

    return implode(' ', $parts);
}

function formatDefault(mixed $value): string
{
    if (is_null($value)) return 'null';
    if (is_bool($value)) return $value ? 'true' : 'false';
    if (is_string($value)) return "'" . addslashes($value) . "'";
    if (is_array($value)) return '[]';
    if (is_int($value) || is_float($value)) return (string) $value;
    return var_export($value, true);
}

function formatReturnType(ReflectionMethod $method): string
{
    $type = $method->getReturnType();
    if (!$type) return 'mixed';
    if ($type instanceof ReflectionNamedType) {
        $name = $type->getName();
        return $type->allowsNull() ? '?' . $name : $name;
    }
    if ($type instanceof ReflectionUnionType) {
        return implode('|', array_map(fn($t) => $t->getName(), $type->getTypes()));
    }
    return 'mixed';
}

function extractDeprecated(ReflectionMethod $method): ?string
{
    $doc = $method->getDocComment();
    if ($doc && preg_match('/@deprecated\s+(.+)/i', $doc, $m)) {
        return trim($m[1]);
    }
    return null;
}

function paramDescription(string $name, string $type): string
{
    $map = [
        'to'           => 'Recipient phone number (e.g. "5511999999999") or group ID ("groupId@g.us")',
        'text'         => 'Text content of the message',
        'url'          => 'URL of the media file',
        'caption'      => 'Caption for the media',
        'mimetype'     => 'MIME type (e.g. "application/pdf")',
        'fileName'     => 'Filename for the document',
        'base64'       => 'Base64-encoded media content',
        'id'           => 'Resource identifier',
        'messageId'    => 'Message ID',
        'msgId'        => 'Message ID',
        'name'         => 'Name',
        'description'  => 'Description text',
        'subject'      => 'Subject text',
        'body'         => 'Request body as associative array',
        'participants' => 'Array of phone numbers',
        'action'       => 'Action to perform',
        'setting'      => 'Setting name',
        'mode'         => 'Mode value',
        'code'         => 'Invite or verification code',
        'jid'          => 'WhatsApp JID (e.g. "5511999999999@s.whatsapp.net")',
        'number'       => 'Phone number',
        'latitude'     => 'Latitude coordinate',
        'longitude'    => 'Longitude coordinate',
        'address'      => 'Address string',
        'status'       => 'Presence status: unavailable, available, composing, recording, paused',
        'fullName'     => 'Full name of the contact',
        'phoneNumber'  => 'Phone number of the contact',
        'organization' => 'Organization name',
        'displayName'  => 'Display name for the contact list',
        'contacts'     => 'Array of contact objects with fullName, phoneNumber, organization',
        'options'      => 'Array of option strings',
        'values'       => 'Array of poll option values',
        'selectableCount' => 'Number of selectable options in a poll',
        'sections'     => 'Array of list sections',
        'buttons'      => 'Array of button objects',
        'header'       => 'Header object with title and optional media',
        'footer'       => 'Footer text',
        'title'        => 'Title text',
        'type'         => 'Type identifier',
        'format'       => 'Response format: "json" (base64) or "binary"',
        'duration'     => 'Duration in seconds',
        'expiration'   => 'Expiration time in seconds',
        'page'         => 'Page number for pagination',
        'limit'        => 'Number of items per page',
        'count'        => 'Number of items to fetch',
        'since'        => 'Unix timestamp filter',
        'after'        => 'Cursor for pagination',
        'cursor'       => 'Cursor for pagination',
        'value'        => 'Boolean value',
        'chatId'       => 'Chat identifier',
        'lids'         => 'Array of LID JIDs to resolve',
        'proxy'        => 'Proxy URL (e.g. "http://user:pass@ip:port")',
        'uri'          => 'MongoDB connection URI',
        'dbName'       => 'Database name',
        'countryCode'  => 'Country code (e.g. "55")',
        'nationalNumber' => 'National phone number (e.g. "11999999999")',
        'networkCode'  => 'Mobile network code (e.g. "11")',
        'method'       => 'Verification method: "sms" or "voice"',
        'rawInput'     => 'Raw JSON string from webhook (defaults to php://input)',
        'reaction'     => 'Reaction emoji',
        'serverId'     => 'Newsletter server message ID',
        'newOwnerJid'  => 'JID of the new owner',
        'userJid'      => 'JID of the user',
        'callId'       => 'Call identifier',
        'callFrom'     => 'Caller JID',
        'peerJid'      => 'Peer JID in the call',
        'from'         => 'Caller identifier',
        'productId'    => 'Product identifier',
        'statusJidList'   => 'Array of JIDs who can see the status (empty = all contacts)',
        'statusMsgId'     => 'Status message ID to mention',
        'markMessageRead' => 'Auto-mark messages as read',
        'saveMedia'       => 'Auto-save media files',
        'receiveStatusMessage' => 'Receive status/story messages',
        'receivePresence'      => 'Receive presence updates',
        'labelId'              => 'Label identifier',
        'thumbnailUrl'         => 'URL of the thumbnail image',
        'sourceUrl'            => 'Source URL for link preview',
        'startTime'            => 'ISO 8601 datetime (e.g. "2026-06-10T14:00:00Z")',
        'locationName'         => 'Location name',
        'locationAddress'      => 'Location address',
    ];

    return $map[$name] ?? '';
}

// --- Output ---

$out = '';

$out .= "# PHP Client for api-wa.me - WhatsApp API\n\n";
$out .= "PHP client library for integrating with the api-wa.me WhatsApp API.\n";
$out .= "Send and receive messages, manage contacts, groups, communities, newsletters, labels, business catalog, status/stories, and calls.\n\n";

$out .= "## Requirements\n\n";
$out .= "- PHP >= 8.1\n";
$out .= "- ext-curl\n\n";

$out .= "## Installation\n\n";
$out .= "```bash\n";
$out .= "composer require {$packageName}\n";
$out .= "```\n\n";

$out .= "## Quick Start\n\n";
$out .= "```php\n";
$out .= "use Api\\Wame\\WhatsApp;\n\n";
$out .= "require 'vendor/autoload.php';\n\n";
$out .= "\$wa = new WhatsApp([\n";
$out .= "    'server' => 'https://server.api-wa.me',\n";
$out .= "    'key'    => 'YOUR_INSTANCE_KEY',\n";
$out .= "]);\n\n";
$out .= "\$wa->message->sendText('5511999999999', 'Hello!');\n";
$out .= "```\n\n";

$out .= "## Architecture\n\n";
$out .= "The WhatsApp class is a facade that exposes domain-specific modules as public properties:\n\n";
$out .= "| Property | Description |\n";
$out .= "|----------|-------------|\n";
$out .= "| \$wa->instance | Connection, settings, profile, proxy, mobile registration |\n";
$out .= "| \$wa->message | Send and reply to all message types |\n";
$out .= "| \$wa->chat | Chat management, message history, privacy settings |\n";
$out .= "| \$wa->contact | Contact CRUD, block/unblock, registration check |\n";
$out .= "| \$wa->group | Group CRUD, members, roles, invites, settings |\n";
$out .= "| \$wa->community | Community CRUD, settings, invites, sub-groups |\n";
$out .= "| \$wa->label | Label CRUD, assign to chats |\n";
$out .= "| \$wa->newsletter | Newsletter CRUD, follow, messages, reactions |\n";
$out .= "| \$wa->business | Product catalog management |\n";
$out .= "| \$wa->status | Status/Stories publishing |\n";
$out .= "| \$wa->call | Make, accept, reject, end calls |\n";
$out .= "| \$wa->webhook | Parse incoming webhook payloads |\n";
$out .= "\n";

// Generate each module
foreach ($classes as $label => $className) {
    $ref = new ReflectionClass($className);
    $methods = $ref->getMethods(ReflectionMethod::IS_PUBLIC);

    // Filter out constructor
    $methods = array_filter($methods, fn($m) => $m->getName() !== '__construct');

    if (empty($methods)) continue;

    $accessor = '$wa->' . lcfirst($label);

    $out .= "## {$label}\n\n";

    foreach ($methods as $method) {
        $methodName = $method->getName();
        $params = $method->getParameters();
        $returnType = formatReturnType($method);

        // Build signature
        $paramStrings = array_map('formatParam', $params);
        $signature = "{$accessor}->{$methodName}(" . implode(', ', $paramStrings) . "): {$returnType}";

        $out .= "### `{$methodName}`\n\n";
        $out .= "```php\n{$signature}\n```\n\n";

        // Parameters table
        if (!empty($params)) {
            $out .= "**Parameters:**\n\n";
            $out .= "| Parameter | Type | Required | Description |\n";
            $out .= "|-----------|------|----------|-------------|\n";

            foreach ($params as $param) {
                $pName = '$' . $param->getName();
                $pType = '';
                $type = $param->getType();
                if ($type instanceof ReflectionNamedType) {
                    $pType = $type->getName();
                } elseif ($type instanceof ReflectionUnionType) {
                    $pType = implode('\\|', array_map(fn($t) => $t->getName(), $type->getTypes()));
                }

                $required = !$param->isDefaultValueAvailable() && !$param->allowsNull() ? 'Yes' : 'No';
                $desc = paramDescription($param->getName(), $pType);

                if ($param->isDefaultValueAvailable()) {
                    $default = formatDefault($param->getDefaultValue());
                    if ($desc) {
                        $desc .= " (default: {$default})";
                    } else {
                        $desc = "Default: {$default}";
                    }
                }

                $out .= "| {$pName} | {$pType} | {$required} | {$desc} |\n";
            }
            $out .= "\n";
        }

        $out .= "**Returns:** `{$returnType}` - JSON response from the API\n\n";
    }
}

// Webhook special section
$out .= "## Webhook Response Properties\n\n";
$out .= "After calling `\$wa->webhook->parse()`, the returned object has these properties:\n\n";
$out .= "| Property | Type | Description |\n";
$out .= "|----------|------|-------------|\n";
$out .= "| remoteJid | string | Sender phone number (digits only) |\n";
$out .= "| msgId | string | Message ID |\n";
$out .= "| pushName | string | Sender display name |\n";
$out .= "| messageType | string | Message type: text, image, audio, video, document, sticker, location, liveLocation, contact, button, list, reaction |\n";
$out .= "| text | string | Text content (text, reaction, and list messages) |\n";
$out .= "| selectedId | string | Selected button or list row ID |\n";
$out .= "| title | string | List response title |\n";
$out .= "| latitude | float | Location latitude |\n";
$out .= "| longitude | float | Location longitude |\n";
$out .= "| thumbnail | string | Base64 data URI for thumbnails |\n";
$out .= "| mimetype | string | Media MIME type |\n";
$out .= "| mediaURL | string | Direct media download URL |\n";
$out .= "| mediaBase64 | string | Base64-encoded media content |\n";
$out .= "| fileName | string | Document filename |\n";
$out .= "| caption | string | Media caption |\n";
$out .= "| messageKeys | array | Array with mediaKey, directPath, url, messageType |\n";
$out .= "| contact | array | Array of objects with name and number properties |\n";
$out .= "\n";

$out .= "## Webhook Usage Example\n\n";
$out .= "```php\n";
$out .= "\$wa = new WhatsApp(['server' => 'https://server.api-wa.me', 'key' => 'YOUR_KEY']);\n";
$out .= "\$parsed = \$wa->webhook->parse();\n\n";
$out .= "if (\$parsed && \$parsed->messageType === 'text') {\n";
$out .= "    \$wa->message->sendText(\$parsed->remoteJid, 'You said: ' . \$parsed->text);\n";
$out .= "}\n\n";
$out .= "if (\$parsed && \$parsed->messageType === 'image') {\n";
$out .= "    // Access media\n";
$out .= "    \$url = \$parsed->mediaURL;\n";
$out .= "    \$base64 = \$parsed->mediaBase64;\n";
$out .= "    \$caption = \$parsed->caption ?? '';\n";
$out .= "}\n";
$out .= "```\n";

echo $out;
