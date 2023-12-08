<?php
use Api\Wame\WhatsApp;

include_once 'vendor/autoload.php';

$whatsapp = new WhatsApp(
  [
    "server" => "https://server.api-wa.me",
    "key" => "your-key"
  ]
);


$whatsapp->constructWebhook();
$whatsapp->from->remoteJid; //number that sent message
$whatsapp->from->msgId;
$whatsapp->from->pushName;
$whatsapp->from->messageType; // | video | text | audio| image | sticker | document| reaction | liveLocation | 
$whatsapp->from->text;


$whatsapp->getQrCodeBase64();

$whatsapp->getQrCodeHTML();

$webhookData = [
  "allowWebhook" => false,
  "webhookMessage" => "",
  "webhookGroup" => "",
  "webhookConnection" => "",
  "webhookQrCode" => "",
  "webhookMessageFromMe" => "",
  "webhookHistory" => ""
];

$whatsapp->updateWebhook($webhookData);


$whatsapp->inforInstance();

$whatsapp->logout();

$whatsapp->profilePic($to);

$whatsapp->updateProfileName($name);

$whatsapp->updateProfilePicture($to, $url);

$whatsapp->sendPresence($to, $presence);

$whatsapp->sendText($to, $text);

$whatsapp->sendMediaBase64($to, $base64String, $type, $caption);

$whatsapp->sendMedia($to, $url, $type, $caption);

$whatsapp->sendReaction($to, $text, $MsgId);

$whatsapp->sendSurvey($to, $name, $options);

$whatsapp->sendLocation($to, $lat, $lon, $address);

$whatsapp->sendContact($to, $name, $number);

$whatsapp->sendTextWithMentions($groupId, $text, $mentions);

$whatsapp->sendAudio($to, $url, true);


$whatsapp->downloadMediaMessage($body);


$whatsapp->listGroup();

$whatsapp->createGroup($name, $participants);

$whatsapp->inforGroup($group_id);

$whatsapp->groupInviteCode($group_id);

$whatsapp->addParticipantsGroup($group_id, $participants);

$whatsapp->demoteParticipantsGroup($group_id, $participants);

$whatsapp->leaveGroup($group_id);

$whatsapp->promoteParticipantsGroup($group_id, $participants);

$whatsapp->setWhoCanSendMessageGroup($group_id, $allow);




