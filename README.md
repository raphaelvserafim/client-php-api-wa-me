# WhatsApp API - PHP

##### Gerar Instância Key:
 
Acessar: <a href="https://api-wa.me">api-wa.me</a>

<a href="https://doc.api-wa.me/">Swagger</a>

<a href="https://documenter.getpostman.com/view/27660901/2sA3Qs9s7K/">Postman</a>

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
echo $whatsapp->connect();
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
echo $whatsapp->updateProfilePicture($url);
```

### Download Media  
```php
    $body = [
    "mediaKey" => "", 
    "directPath" => "", 
    "url" => "",
    ] ;
$type = "image";// video | audio| image | sticker | document|
echo $whatsapp->downloadMediaMessage($type, $body);
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
echo $whatsapp->sendAudio($to, $url);
```

###  send Media 
 
 
 
 ### Send Button
 ```php
   $body = [
    "to" => "556696852025",
    "title" => "Are you enjoying ?",
    "footer" => "choose an option",
    "buttons" => [
        [
            "id" => "click_1",
            "text" => "Yes"
        ],
        [
            "id" => "click_2",
            "text" => "No"
        ]
    ]
];
echo $whatsapp->sendButton($body);
```


 ### Send List 
 ```php
   $body = [
    "to" => "556696852025",
    "buttonText" => "Menu",
    "text" => "string", 
    "title" => "Menu",
    "description" => "Description",
    "footer" => "footer", 
    "sections" => [
        [
            "title" => "Pizza",
            "rows" => [
                [
                    "title" => "Pizza 01",
                    "description" => "Example pizza 01",
                    "rowId" => "1"
                ]
            ]
        ]
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
 $lat    =  37.7749;   
 $lon    =  -122.4194;
 $address = '123 Main St, San Francisco, CA';
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
   $action = "promote"; // demote
   echo $whatsapp->promoteParticipantsGroup($group_id, $participants, $action);
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

 
