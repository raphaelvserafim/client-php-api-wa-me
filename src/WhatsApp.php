<?php

namespace Cachesistemas\ClassePhpApiWame;


class WhatsApp
{
    private $key;
    private $server;
    private $header = array();
    private $parth;
    private $method;
    private $body;

    public function __construct(array $dados)
    {

        $this->server =  $dados["server"];
    }


    private function request()
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->server . $this->parth);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,  $this->body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        return $result;
    }


    // ManagerInstance // 
    public function ListAllInstance($admin_key)
    {
        $this->parth  = "/instance/list?admin_key={$admin_key}";
        $this->method = "GET";
        return $this->request();
    }


    public function Create($admin_key,  $key,   $body)
    {

        /* EXEMPLO 
        $body = [
        "allowWebhook" => true, 
        "webhookMessage" => "", 
        "webhookGroup" => "", 
        "webhookConnection" => "", 
        "webhookQrCode" => "" 
        ]; 
        */

        $this->parth  = "/instance/create?key={$key}&admin_key={$admin_key}";
        $this->method = "POST";
        $this->body   = json_encode($body);

        return $this->request();
    }

    public function Delete($admin_key,   $key)
    {
        $this->parth  = "/instance/delete?key={$key}&admin_key={$admin_key}";
        $this->method = "DELETE";
        return $this->request();
    }




    //Instance

    public function getQrCodeHTML($key)
    {
        $this->parth  = "/instance/qrcode?key={$key}";
        $this->method = "GET";
        return $this->request();
    }

    public function getQrCodeBase64($key)
    {
        $this->parth  = "/instance/qrcode_base64={$key}";
        $this->method = "GET";
        return $this->request();
    }


    public function inforInstance($key)
    {
        $this->parth  = "/instance/info?key={$key}";
        $this->method = "GET";
        return $this->request();
    }


    public function updateWebhook($key, $body)
    {
        /* EXEMPLO 
        $body = [
        "allowWebhook" => true, 
        "webhookMessage" => "", 
        "webhookGroup" => "", 
        "webhookConnection" => "", 
        "webhookQrCode" => "" 
        ]; 
        */

        array_push($this->header, 'Content-Type: application/json');
        $this->parth    = "/instance/updateWebhook?key={$key}";
        $this->method   = "POST";
        $this->body     = json_encode($body);
        return $this->request();
    }


    public function logout($key)
    {

        $this->parth  = "/instance/logout?key={$key}";
        $this->method = "DELETE";
        return $this->request();
    }

    // Actions 

    public function listContacts($key)
    {
        $this->parth  = "/action/contacts?key={$key}";
        $this->method = "GET";
        return $this->request();
    }

    public function profilePic($key, $to)
    {

        $this->parth  = "/actions/getPicture?key={$key}&to={$to}";
        $this->method = "GET";
        return $this->request();
    }


    public function updateProfileName($key, $name)
    {
        array_push($this->header, 'Content-Type: application/json');
        $this->parth  = "/actions/updateProfileName?key={$key}";
        $this->method = "POST";
        $this->body = json_encode(["name" => $name]);
        return $this->request();
    }


    public function updateProfilePicture($key, $body) // AND GROUP
    {
        // ["to" => "string", "url" => "string"] EXEMPLO 
        array_push($this->header, 'Content-Type: application/json');
        $this->parth  = "/actions/updateProfilePicture?key={$key}";
        $this->method = "POST";
        $this->body   = json_encode($body);
        return $this->request();
    }

    public function readReceipt($key, $body)
    {
        // ["to" => "string", "idMsg" => "string"] EXEMPLO 
        array_push($this->header, 'Content-Type: application/json');
        $this->parth  = "/actions/readReceipt?key={$key}";
        $this->method = "POST";
        $this->body   = json_encode($body);
        return $this->request();
    }


    public function downloadMediaMessage($key, $body)
    {
        /* EXEMPLO BODY 
        [
            "messageKeys" => [
                "mediaKey" => "string", 
                "directPath" => "string", 
                "url" => "string", 
                "messageType" => "string" 
            ] 
         ] 
         */
        array_push($this->header, 'Content-Type: application/json');
        $this->parth  = "/actions/downloadMediaMessage?key={$key}";
        $this->method = "POST";
        $this->body   = json_encode($body);
        return $this->request();
    }



    //SendMessage 

    public function sendPresence($key, $body)
    {
        /* EXEMPLO BODY
                 unavailable | available | composing | recording | paused
                [
                    "data" => [
                        "to" => "556696852025", 
                        "status" => "composing" 
                    ] 
                ]
            */
        array_push($this->header, 'Content-Type: application/json');
        $this->parth  =  "/message/setstatus?key={$key}";
        $this->method =  "POST";
        $this->body   =  json_encode($body);
        return $this->request();
    }



    public function sendText($key, $body)
    {
        /*
         [
            "messageData" => [
                  "to" => "556696852025", 
                  "text" => "estou testando a API" 
               ] 
         ]
         */
        array_push($this->header, 'Content-Type: application/json');
        $this->parth  = "/message/text?key={$key}";
        $this->method = "POST";
        $this->body   =  json_encode($body);
        return $this->request();
    }


    public function sendMedia($key, $body)
    {
        /*
        EXEMPLO BODY
         [
            "data" => [
                "to" => "556696852025",
                "url" => "https://blogvidanoegito.files.wordpress.com/2019/11/esfinge.jpg",
                "type" => "image",
                "ptt" => true,
                "caption" => "EGITO",
                "mimeType" => "image/jpeg"
            ]
        ]
        */
        array_push($this->header, 'Content-Type: application/json');
        $this->parth  = "/message/media?key={$key}";
        $this->method = "POST";
        $this->body   = json_encode($body);
        return $this->request();
    }


    public function sendButton($key,  $body)
    {
        /* EXEMPLO BODY 
        $jayParsedAry = [
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
        */
        array_push($this->header, 'Content-Type: application/json');
        $this->parth  = "/message/button?key={$key}";
        $this->method = "POST";
        $this->body   = json_encode($body);
        return $this->request();
    }

    public function sendTemplateButtons($key, $body)
    {
        /* EXEMPLO BODY  replyButton | urlButton | callButton
        $jayParsedAry = [
            "to" => "556696852025",
            "data" => [
                "text" => "Finalizar a compra",
                "buttons" => [
                    [
                        "type" => "urlButton",
                        "title" => "Pagar",
                        "payload" => "https://api-wa.me"
                    ]
                ],
                "footerText" => "Visite o site para finalizar"
            ]
        ];
        */

        array_push($this->header, 'Content-Type: application/json');
        $this->parth  = "/message/templateButtons?key={$key}";
        $this->method = "POST";
        $this->body   = json_encode($body);
        return $this->request();
    }


    public function sendList($key, $body)
    {
        /* EXEMPLO BODY 
        $jayParsedAry = [
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
        */

        array_push($this->header, 'Content-Type: application/json');
        $this->parth  = "/message/list?key={$key}";
        $this->method = "POST";
        $this->body   = json_encode($body);
        return $this->request();
    }


    public function enviarLocalizacao($numero,    $latitude,   $longitude)
    {
        array_push($this->header, 'Content-Type: application/json');
        $this->parth  = '/rest/send/' . $this->key . '/localizacao';
        $this->method = 'POST';
        $this->body   = json_encode(array('messageData' => array('numero_whatsapp' => $numero, 'coordenadas' => array('latitude' => $latitude, 'longitude' => $longitude))));
        return $this->request();
    }

    public function enviarContato($numero,    $nome,   $phoneNumber)
    {
        array_push($this->header, 'Content-Type: application/json');
        $this->parth  = '/rest/send/' . $this->key . '/contato';
        $this->method = 'POST';
        $this->body   = json_encode(array('messageData' => array('to' => $numero, 'vcard' => array('fullName' => $nome, 'displayName' => $nome, 'organization' => '', 'phoneNumber' => $phoneNumber))));

        return $this->request();
    }




    public function grupos()
    {
        $this->parth  = '/rest/group/' . $this->key . '/list';
        $this->method = 'GET';
        return $this->request();
    }


    public function gruposADM()
    {
        $this->parth  = '/rest/group/' . $this->key . '/adminGroups';
        $this->method = 'GET';
        return $this->request();
    }

    public function criarGrupo($nome, $participantes)
    {
        array_push($this->header, 'Content-Type: application/json');
        $this->parth  = '/rest/group/' . $this->key . '/create';
        $this->method = 'POST';
        $this->body   = json_encode(array('group_data' => array('group_name' => $nome, 'participants' => $participantes)));

        return $this->request();
    }
}
