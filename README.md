# WhatsApp API - PHP

##### Gerar Instância Key:
 
Acessar: <a href="https://api-wa.me">api-wa.me</a>

<a href="https://server.api-wa.me/docs/">Swagger</a>

## Installing via composer

```
composer require raphaelvserafim/client-php-api-wa-me
```

### EXAMPLES


#####  WHATSAPP
```php

use Api\Wame\WhatsApp;

include_once 'vendor/autoload.php';

$whatsapp     = new WhatsApp(
    ["server" => "API server", 
    "key" => "Your Key Instance"]
    );
```

#### Get webhook 
```php
    $whatsapp->constructWebhook();
    $whatsapp->from->remoteJid; //  number that sent message
    $whatsapp->from->messageType; // video | text | audio| image | sticker | document| reaction | liveLocation | 
    $whatsapp->from->msgId;
    $whatsapp->from->pushName;
    $whatsapp->from->text; 
```

#### Exemple
```php
if ($whatsapp->from->messageType === "text" && $whatsapp->from->text === "Hi") {
  $whatsapp->sendText($whatsapp->from->remoteJid, "Hello!");
}
```

#### Get QrCode HTML
```php
echo $whatsapp->getQrCodeHTML();
```

#### Get QrCode Base64
```php
echo $whatsapp->getQrCodeBase64();
```

#### Infor Instance
```php
echo $whatsapp->inforInstance();
```

#### Update Webhook
```php
$body = [
        "allowWebhook" => false,
        "webhookMessage" => "",
        "webhookGroup" => "",
        "webhookConnection" => "",
        "webhookQrCode" => "", 
        "webhookMessageFromMe"=>"", 
        "webhookHistory"=>""
    ]; 
echo $whatsapp->updateWebhook($body);
```

#### Logout
```php
echo $whatsapp->logout();
```
 
## Actions

### Get List Contacts
```php
echo $whatsapp->listContacts();
```

### Get Profile  Pic
```php
echo $whatsapp->profilePic('556696852025');
```

### Update Profile Name
```php
echo $whatsapp->updateProfileName('Raphael Serafim');
```
 
### Update Profile And Group  Picture
```php
$url =''; // url image 
$id  ='556696852025'; // if it's a group, use full id ex: 123456789@g.us 
echo $whatsapp->updateProfilePicture($id, $url);
```

### Read Receipt
```php
$MsgId ='';  
$to  ='556696852025'; // if it's a group, use full id ex: 123456789@g.us 
echo $whatsapp->readReceipt($to, $MsgId);
```

### Download Media  
```php
  $body = [
    "messageKeys" => [
        "mediaKey" => "", 
        "directPath" => "", 
        "url" => "", 
        "messageType" => "" 
    ] 
  ];
echo $whatsapp->downloadMediaMessage($body);
```

## Send Message

### send Presence
 ```php
 $to     = '556696852025'; // if it's a group, use full id ex: 123456789@g.us  
 $status = 'composing';   // unavailable | available | composing | recording | paused
echo $whatsapp->sendPresence($to, $status);
```

### send Text
 ```php
 $to     = '556696852025'; // if it's a group, use full id ex: 123456789@g.us  
 $text   = 'Hi';   
echo $whatsapp->sendText($to, $text);
```

### send Audio
 ```php
 $to     = '556696852025'; // if it's a group, use full id ex: 123456789@g.us  
 $url    = ''; // your MP3 or OGG audio URL    
 $ptt    = true;
echo $whatsapp->sendAudio($to, $url, $ptt  );
```

###  send Media 
 
```php 
$to         = '556696852025'; // if it's a group, use full id ex: 123456789@g.us  
$url        = '';
$type       = 'image'; //  image |  video | audio | document
$caption    = 'Hi';  
echo $whatsapp->sendMedia($to, $url, $type, $caption);
```
 
 ### Send Button
 ```php
    $body = [
        "to" => "556696852025",
        "data" => [
            "text" => "Recebeu ?",
            "buttons" => [
                [
                    "title" => "Sim",
                    "id" => "1"
                ],
                [
                    "title" => "Não",
                    "id" => "2"
                ]
            ],
            "footerText" => "Escolha uma opção"
        ]
    ];
echo $whatsapp->sendButton($body);
```

 ### Send Template Buttons
 ```php
    $body = [
        "to" => "556696852025",
        "data" => [
            "text" => "Finalizar a compra",
            "buttons" => [
                [
                    "type" => "urlButton", // replyButton | urlButton | callButton 
                    "title" => "Pagar",
                    "payload" => "https://api-wa.me"
                ]
            ],
            "footerText" => "Visite o site para finalizar"
        ]
    ];
echo $whatsapp->sendTemplateButtons($body);
```
 

 ### Send List 
 ```php
    $body = [
        "messageData" => [
            "to" => "556696852025",
            "buttonText" => "Menu",
            "text" => "Esse nosso menu",
            "title" => "Menu",
            "description" => "veja nosso menu",
            "sections" => [
                [
                    "title" => "Menu 01",
                    "rows" => [
                        [
                            "title" => "Opção 01",
                            "description" => "essa é uma opção",
                            "rowId" => "1"
                        ],
                        [
                            "title" => "Opção 02",
                            "description" => "essa é outra opção",
                            "rowId" => "2"
                        ]
                    ]
                ]
            ],
            "listType" => 0
        ]
    ];
echo $whatsapp->sendList($body);
```


### send Contact
 ```php
 $to     = '556696852025'; // if it's a group, use full id ex: 123456789@g.us  
 $name   = 'CACHE SISTEMAS';   
 $number = '+556696883327';
echo $whatsapp->sendContact($to, $name, $number);
```

### send Location
 ```php
 $to     = '556696852025'; // if it's a group, use full id ex: 123456789@g.us  
 $lat    = 35.000;   
 $lon    = 20.000;
 $address = 'Rua do fulando';
echo $whatsapp->sendLocation($to, $lat, $lon, $address);
```

### send Reaction
 ```php
 $to     = '556696852025'; // if it's a group, use full id ex: 123456789@g.us  
 $text   =  '😘';   
 $msgId  =  '';
echo $whatsapp->sendReaction($to, $text, $msgId);
```


## Group

### Get list Group
```php 
   echo $whatsapp->listGroup();
```

### Get infor Group
```php 
   $group_id = '123456789@g.us'; 
   echo $whatsapp->inforGroup($group_id);
```


### Get Invite Code Group
```php 
   $group_id = '123456789@g.us'; 
   echo $whatsapp->groupInviteCode($group_id);
```

 ### create Group
```php 
   $name = 'API PHP WhatsApp'; 
   $participants = ['556696852025'];
   echo $whatsapp->createGroup($name, $participants);
```


 ### add Participants Group
```php 
   $group_id     = '123456789@g.us'; 
   $participants = ['556696852025'];
   echo $whatsapp->addParticipantsGroup($group_id, $participants);
```


 ### Promote Participants Group Admin 
```php 
   $group_id     = '123456789@g.us'; 
   $participants = ['556696852025'];
   echo $whatsapp->promoteParticipantsGroup($group_id, $participants);
```

 ### Demote Participants Group   
```php 
   $group_id     = '123456789@g.us'; 
   $participants = ['556696852025'];
   echo $whatsapp->demoteParticipantsGroup($group_id, $participants);
```

 ### Set Who Can Send Message Group  
```php 
   $group_id     = '123456789@g.us'; 
   // true = Admin; false= All 
   echo $whatsapp->setWhoCanSendMessageGroup($group_id, true);
```

 ### Set Who Can Change Settings Group
```php 
   $group_id     = '123456789@g.us'; 
   // true = Admin; false= All 
   echo $whatsapp->setWhoCanChangeSettingsGroup($group_id, true);
```

 ### Remove Participants Group  
```php 
   $group_id     = '123456789@g.us'; 
   $participants = ['556696852025'];
   echo $whatsapp->removeParticipantsGroup($group_id, $participants);
```

 ### Leave Group
```php 
   $group_id     = '123456789@g.us'; 
   echo $whatsapp->leaveGroup($group_id);
```

 
