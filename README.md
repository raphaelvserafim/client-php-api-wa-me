# WhatsApp API

## About Our WhatsApp API

🔓 **Unlock the Potential of WhatsApp:** Powerful Integration with Our Unofficial API!

### Efficient Group Management on WhatsApp

📋 Our stable, though unofficial, API allows for efficient management of WhatsApp groups. Simplify administration, and easily add or remove members.

### Versatile Message Sending

💬 With our API, you can send text, audio, video, and image messages quickly and easily. Adapt to your business communication needs comprehensively.

### Receive Instant Events

🔔 Stay updated with our API, receiving real-time events when new messages are received. Stay connected and agile in responding to interactions on WhatsApp.

🔗 **Create Your Account:** Visit [https://api-wa.me/sign-up](https://api-wa.me/sign-up) to create your account and start using our API today!

🎁 **Special Discount:** Use the coupon **GIT20** and get 20% off on your first instance!

## Contact Support

- **Name:** Raphael Serafim
- **Email:** [support@api-wa.me](mailto:support@api-wa.me)
- **Website:** [https://api-wa.me](https://api-wa.me)



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
"allowWebhook" => 1, // 1=true or 0=false
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

###  send Image 
 ```php
 $to     = '556696852025'; // if it's a group, use full id ex: 123456789@g.us  
 $url    = '';  
 $caption = '';
echo $whatsapp->sendImage($to, $url);
```
 
 ###  send Video 
 ```php
 $to     = '556696852025'; // if it's a group, use full id ex: 123456789@g.us  
 $url    = '';  
 $caption = '';
echo $whatsapp->sendVideo($to, $url);
```


 
 ###  send Document 
 ```php
$to     = '556696852025'; // if it's a group, use full id ex: 123456789@g.us  
$url    = '';  
$caption = '';
$mimetype = 'application/pdf'; // https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types/Common_types 
$fileName='';
echo $whatsapp->sendDocument($to, $url, $mimetype, $fileName);
```

 
 ### Send Button
 ```php
   $body = [
    "to" => "556696852025",
    "title" => "Are you enjoying ?",
    "footer" => "choose an option",
    "buttons" => [
        [
            "type"=> "quick_reply",
            "id" => "click_1",
            "text" => "Yes"
        ],
        [
            "type"=> "cta_copy",
            "copy_code" => "000000000000",
            "text" => "Copy barcode"
        ],
        [
            "type"=> "cta_url",
            "url" => "https://api-wa.me",
            "text" => "Access the website"
        ],
        [
            "type"=> "send_location"
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

 
