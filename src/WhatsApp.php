<?php

namespace Api\Wame;

use stdClass;

class WhatsApp
{
  public $key;
  public $server;
  public $from;
  private $header = array();
  private $parth;
  private $method;
  private $body;

  public function __construct(array $data = [])
  {
    if (isset($data["server"])) {
      $this->server = $data["server"];
    }
    if (isset($data["key"])) {
      $this->key = $data["key"];
    }
  }

  private function request()
  {
    $this->header[] = 'Content-Type: application/json';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->server . $this->parth);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);
    if ($this->method === 'POST' || $this->method === 'PUT') {
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $this->body);
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
    $result = curl_exec($ch);

    if (curl_errno($ch)) {
      $errorMessage = curl_error($ch);
      curl_close($ch);
      throw new \RuntimeException("cURL Error: $errorMessage");
    }
    curl_close($ch);
    return $result;
  }

  /**
   * Obtém o código QR em HTML para a instância atual.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function connect()
  {
    // Define o caminho, método e corpo da requisição para obter o código QR em HTML.
    $this->parth = "/{$this->key}/instance";
    $this->method = "POST";
    // Executa a requisição e retorna o resultado.
    return $this->request();
  }

  /**
   * Obtém informações sobre a instância atual.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function inforInstance()
  {
    // Define o caminho, método e corpo da requisição para obter informações sobre a instância.
    $this->parth = "/{$this->key}/instance";
    $this->method = "GET";
    // Executa a requisição e retorna o resultado.
    return $this->request();
  }


  /**
   * Atualiza o webhook para a instância atual.
   *
   * @param mixed $body Os dados a serem enviados no corpo da requisição.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function updateWebhook($body)
  {
    // Define o caminho, método e corpo da requisição para atualizar o webhook.
    $this->parth = "/{$this->key}/instance";
    $this->method = "PUT";
    $this->body = json_encode($body);
    // Executa a requisição e retorna o resultado.
    return $this->request();
  }

  /**
   * Faz logout da instância atual.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function logout()
  {
    // Define o caminho, método e corpo da requisição para efetuar o logout.
    $this->parth = "/{$this->key}/instance";
    $this->method = "DELETE";

    // Executa a requisição e retorna o resultado.
    return $this->request();
  }


  /**
   * Lista todos os contatos associados à instância atual.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function listContacts()
  {
    // Define o caminho, método e corpo da requisição para listar os contatos.
    $this->parth = "/{$this->key}/contacts";
    $this->method = "GET";
    // Executa a requisição e retorna o resultado.
    return $this->request();
  }

  /**
   * Obtém a imagem do perfil de um contato específico.
   *
   * @param string $to O identificador do contato para o qual se deseja obter a imagem do perfil.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function profilePic(string $to)
  {
    // Define o caminho, método e corpo da requisição para obter a imagem do perfil.
    $this->parth = "/{$this->key}/contacts/{$to}";
    $this->method = "GET";
    // Executa a requisição e retorna o resultado.
    return $this->request();
  }

  /**
   * Atualiza o nome do perfil.
   *
   * @param string $name O novo nome para o perfil.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function updateProfileName(string $name)
  {
    // Define o caminho, método e corpo da requisição para atualizar o nome do perfil.
    // $this->parth = "/actions/updateProfileName?key={$this->key}";
    // $this->method = "POST";
    // $this->body = json_encode(["name" => $name]);
    // // Executa a requisição e retorna o resultado.
    // return $this->request();
  }


  /**
   * Atualiza a imagem do perfil.
   *
   * @param mixed $to O destino da atualização da imagem do perfil.
   * @param string $url A URL da nova imagem do perfil.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function updateProfilePicture(string $url)
  {
    // Define o caminho, método e corpo da requisição para atualizar a imagem do perfil.
    $this->parth = "/{$this->key}/actions/picture";
    $this->method = "PUT";
    $this->body = json_encode(["url" => $url]);
    // Executa a requisição e retorna o resultado.
    return $this->request();
  }

  /**
   * Envia um recibo de leitura para uma mensagem específica.
   *
   * @param string $to O identificador do destinatário.
   * @param string $MsgId O identificador da mensagem.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function readReceipt(string $to, string $MsgId)
  {
    // Define o corpo da requisição para enviar um recibo de leitura.
    // $body = ["to" => $to, "idMsg" => $MsgId];
    // $this->parth = "/actions/readReceipt?key={$this->key}";
    // $this->method = "POST";
    // $this->body = json_encode($body);

    // // Executa a requisição e retorna o resultado.
    // return $this->request();
  }


  /**
   * Baixa um arquivo de mídia associado a uma mensagem.
   *
   * @param mixed $body Os dados a serem enviados no corpo da requisição.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function downloadMediaMessage($type, $body)
  {
    // Define o caminho, método e corpo da requisição para baixar um arquivo de mídia.
    $this->parth = "/{$this->key}/actions/download/media?type=" . $type;
    $this->method = "POST";
    $this->body = json_encode($body);
    // Executa a requisição e retorna o resultado.
    return $this->request();
  }


  /**
   * Envia um status de presença para um destinatário.
   *
   * @param string $to O identificador do destinatário.
   * @param string $status O status de presença a ser enviado.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function sendPresence(string $to, string $status)
  {
    // Define o corpo da requisição para enviar um status de presença.
    $this->parth = "/{$this->key}/message/presence";
    $this->method = "POST";
    $this->body = json_encode([
      "to" => $to,
      "status" => $status
    ]);
    // Executa a requisição e retorna o resultado.
    return $this->request();
  }

  /**
   * Envia uma mensagem de texto para um destinatário.
   *
   * @param string $to O identificador do destinatário.
   * @param string $text O texto da mensagem a ser enviado.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function sendText(string $to, string $text)
  {
    // Define o corpo da requisição para enviar uma mensagem de texto.
    $this->parth = "/{$this->key}/message/text";
    $this->method = "POST";
    $this->body = json_encode([
      "to" => $to,
      "text" => $text
    ]);
    // Executa a requisição e retorna o resultado.
    return $this->request();
  }

  /**
   * Envia uma mensagem de áudio para um destinatário.
   *
   * @param string $to O identificador do destinatário.
   * @param string $url A URL do arquivo de áudio.
   * @param bool $ptt Indica se o áudio deve ser reproduzido como push-to-talk (PTT).
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function sendAudio(string $to, string $url, bool $ptt = true)
  {
    // Define o corpo da requisição para enviar uma mensagem de áudio.
    $this->parth = "/{$this->key}/message/audio";
    $this->method = "POST";
    $this->body = json_encode([
      "to" => $to,
      "url" => $url
    ]);
    // Executa a requisição e retorna o resultado.
    return $this->request();
  }


  /**
   * Envia uma mensagem de mídia (base64) para um destinatário.
   *
   * @param string $to O identificador do destinatário.
   * @param string $base64String A string base64 que representa a mídia.
   * @param string $type O tipo de mídia (por exemplo, imagem, vídeo, áudio).
   * @param string $caption Uma legenda para a mídia.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function sendMediaBase64(string $to, string $base64String, string $type, string $caption)
  {
    // Define o caminho, método e corpo da requisição para enviar uma mensagem de mídia (base64).
    $this->parth = "/message/sendMediaBase64?key={$this->key}";
    $this->method = "POST";
    $this->body = json_encode([
      "data" => [
        "to" => $to,
        "stringBase64" => $base64String,
        "type" => $type,
        "caption" => $caption
      ]
    ]);

    // Executa a requisição e retorna o resultado.
    return $this->request();
  }

  /**
   * Envia uma mensagem de pesquisa para um destinatário.
   *
   * @param string $to O identificador do destinatário.
   * @param string $name O nome da pesquisa.
   * @param array $options Um array contendo as opções da pesquisa.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function sendSurvey(string $to, string $name, array $options)
  {
    // Define o caminho, método e corpo da requisição para enviar uma mensagem de pesquisa.
    $this->parth = "/{$this->key}/message/survey";
    $this->method = "POST";
    $this->body = json_encode([
      "to" => $to,
      "name" => $name,
      "options" => $options
    ]);
    // Executa a requisição e retorna o resultado.
    return $this->request();
  }



  /**
   * Responde a uma mensagem específica.
   *
   * @param string $to O identificador do destinatário.
   * @param string $text O texto da resposta.
   * @param string $messageId O identificador da mensagem à qual está respondendo.
   * @param array $msgContent Um array contendo o conteúdo da mensagem (se necessário).
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function replyToMessage(string $to, string $text, string $messageId, array $msgContent = [])
  {
    // // Define o caminho, método e corpo da requisição para responder a uma mensagem.
    // $this->parth = "/message/replymessage?key={$this->key}";
    // $this->method = "POST";
    // $this->body = json_encode([
    //   "messageData" => [
    //     "to" => $to,
    //     "text" => $text,
    //     "messageId" => $messageId,
    //     "msgContent" => $msgContent
    //   ]
    // ]);
    // // Executa a requisição e retorna o resultado.
    // return $this->request();
  }


  /**
   * Envia uma mensagem de texto com menções para um grupo específico.
   *
   * @param string $groupId O identificador do grupo.
   * @param string $text O texto da mensagem com menções.
   * @param array $mentions Um array contendo os identificadores das menções.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function sendTextWithMentions(string $groupId, string $text, array $mentions)
  {
    // Define o caminho, método e corpo da requisição para enviar uma mensagem de texto com menções.
    // $this->parth = "/message/textMentions?key={$this->key}";
    // $this->method = "POST";
    // $this->body = json_encode([
    //   "data" => [
    //     "group_id" => $groupId,
    //     "text" => $text,
    //     "mentions" => $mentions
    //   ]
    // ]);

    // // Executa a requisição e retorna o resultado.
    // return $this->request();
  }


  /**
   * Envia uma mensagem de mídia para um destinatário.
   *
   * @param string $to O identificador do destinatário.
   * @param string $url A URL do arquivo de mídia.
   * @param string $type O tipo de mídia (por exemplo, "image", "video").
   * @param string $caption A legenda da mídia.
   * @param string $mimeType O tipo MIME do arquivo de mídia.
   * @param bool $ptt Indica se o áudio deve ser reproduzido como push-to-talk (PTT).
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function sendMedia(string $to, string $url, string $type, string $caption, string $mimeType = '', bool $ptt = false)
  {
    // Define o corpo da requisição para enviar uma mensagem de mídia.
    // $this->parth = "/message/media?key={$this->key}";
    // $this->method = "POST";
    // $this->body = json_encode([
    //   "data" => [
    //     "to" => $to,
    //     "url" => $url,
    //     "type" => $type,
    //     "caption" => $caption,
    //     "mimeType" => $mimeType,
    //     "ptt" => $ptt
    //   ]
    // ]);
    // Executa a requisição e retorna o resultado.
    //return $this->request();
  }


  public function sendImage(string $to, string $url, string $caption)
  {
    // Define o corpo da requisição para enviar uma mensagem de mídia.
    $this->parth = "/{$this->key}/message/image";
    $this->method = "POST";
    $this->body = json_encode([
      "to" => $to,
      "url" => $url,
      "caption" => $caption,
    ]);
    return $this->request();
  }

  public function sendVideo(string $to, string $url, string $caption)
  {
    // Define o corpo da requisição para enviar uma mensagem de mídia.
    $this->parth = "/{$this->key}/message/video";
    $this->method = "POST";
    $this->body = json_encode([
      "to" => $to,
      "url" => $url,
      "caption" => $caption,
    ]);
    return $this->request();
  }

  public function sendDocument(string $to, string $url, string $caption, string $mimetype,  $fileName)
  {
    // Define o corpo da requisição para enviar uma mensagem de mídia.
    $this->parth = "/{$this->key}/message/document";
    $this->method = "POST";
    $this->body = json_encode([
      "to" => $to,
      "url" => $url,
      "caption" => $caption,
      "mimetype" => $mimetype,
      "fileName" => $fileName,
    ]);
    return $this->request();
  }


  /**
   * Envia uma mensagem com botões para um destinatário.
   *
   * @param mixed $body Os dados a serem enviados no corpo da requisição.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function sendButton($body)
  {
    // Define o corpo da requisição para enviar uma mensagem com botões.
    $this->parth = "/{$this->key}/message/button";
    $this->method = "POST";
    $this->body = json_encode($body);
    // Executa a requisição e retorna o resultado.
    return $this->request();
  }

  /**
   * Envia uma mensagem com botões de modelo para um destinatário.
   *
   * @param mixed $body Os dados a serem enviados no corpo da requisição.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function sendTemplateButtons($body)
  {
    // Define o corpo da requisição para enviar uma mensagem com botões de modelo.
    // $this->parth = "/message/templateButtons?key={$this->key}";
    // $this->method = "POST";
    // $this->body = json_encode($body);

    // // Executa a requisição e retorna o resultado.
    // return $this->request();
  }

  /**
   * Envia uma lista para um destinatário.
   *
   * @param mixed $body Os dados a serem enviados no corpo da requisição.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function sendList($body)
  {
    // Define o corpo da requisição para enviar uma lista.
    $this->parth = "/{$this->key}/message/list";
    $this->method = "POST";
    $this->body = json_encode($body);
    // Executa a requisição e retorna o resultado.
    return $this->request();
  }

  /**
   * Envia um cartão de contato para um destinatário.
   *
   * @param string $to O identificador do destinatário.
   * @param string $name O nome do contato.
   * @param string $number O número do contato.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function sendContact(string $to, string $name, string $number)
  {
    // Define o corpo da requisição para enviar um cartão de contato.
    $this->parth = "/{$this->key}/message/contact";
    $this->method = "POST";
    $this->body = json_encode([
      "to" => $to,
      "contact" => [
        "fullName" => $name,
        "organization" => "",
        "phoneNumber" => $number
      ]
    ]);
    // Executa a requisição e retorna o resultado.
    return $this->request();
  }

  /**
   * Envia uma localização para um destinatário.
   *
   * @param string $to O identificador do destinatário.
   * @param float $lat A latitude da localização.
   * @param float $lon A longitude da localização.
   * @param string $address O endereço da localização.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function sendLocation(string $to, float $lat, float $lon, string $address)
  {
    // Define o corpo da requisição para enviar uma localização.
    $this->parth = "/{$this->key}/message/location";
    $this->method = "POST";
    $this->body = json_encode([
      "to" => $to,
      "location" => [
        "latitude" => $lat,
        "longitude" => $lon,
        "address" => $address
      ]
    ]);
    // Executa a requisição e retorna o resultado.
    return $this->request();
  }


  /**
   * Envia uma reação a uma mensagem específica.
   *
   * @param string $to O identificador do destinatário.
   * @param string $text O texto da reação.
   * @param string $MsgId O identificador da mensagem à qual a reação está associada.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function sendReaction(string $to, string $text, string $msgId)
  {
    // Define o corpo da requisição para enviar uma reação.
    $this->parth = "/{$this->key}/message/reaction";
    $this->method = "POST";
    $this->body = json_encode([
      "to" => $to,
      "text" => $text,
      "msgId" => $msgId
    ]);
    // Executa a requisição e retorna o resultado.
    return $this->request();
  }


  /**
   * Lista todos os grupos associados à instância atual.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function listGroup()
  {
    // Define o caminho, método e corpo da requisição para listar todos os grupos.
    $this->parth = "/{$this->key}/groups";
    $this->method = "GET";
    // Executa a requisição e retorna o resultado.
    return $this->request();
  }

  /**
   * Obtém informações sobre um grupo específico.
   *
   * @param string $group_id O identificador do grupo.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function inforGroup(string $group_id)
  {
    // Define o caminho, método e corpo da requisição para obter informações sobre um grupo.
    $this->parth = "/{$this->key}/groups/{$group_id}";
    $this->method = "GET";
    // Executa a requisição e retorna o resultado.
    return $this->request();
  }

  /**
   * Obtém o código de convite de um grupo específico.
   *
   * @param string $group_id O identificador do grupo.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function groupInviteCode(string $group_id)
  {
    // Define o caminho, método e corpo da requisição para obter o código de convite de um grupo.
    $this->parth = "/{$this->key}/groups/{$group_id}/invite";
    $this->method = "GET";
    // Executa a requisição e retorna o resultado.
    return $this->request();
  }

  /**
   * Cria um novo grupo.
   *
   * @param string $name O nome do grupo.
   * @param array $participants Um array contendo os identificadores dos participantes do grupo.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function createGroup(string $name, array $participants)
  {
    // Define o corpo da requisição para criar um novo grupo.
    $this->parth = "/{$this->key}/groups";
    $this->method = "POST";
    $this->body = json_encode([
      "name" => $name,
      "participants" => $participants
    ]);
    // Executa a requisição e retorna o resultado.
    return $this->request();
  }

  /**
   * Adiciona participantes a um grupo existente.
   *
   * @param string $group_id O identificador do grupo.
   * @param array $participants Um array contendo os identificadores dos participantes a serem adicionados.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function addParticipantsGroup(string $group_id, array $participants)
  {
    // Define o corpo da requisição para adicionar participantes a um grupo existente.
    $this->parth = "/{$this->key}/groups/{$group_id}/participants";
    $this->method = "POST";
    $this->body = json_encode([
      "participants" => $participants
    ]);
    // Executa a requisição e retorna o resultado.
    return $this->request();
  }

  /**
   * Remove participantes de um grupo existente.
   *
   * @param string $group_id O identificador do grupo.
   * @param array $participants Um array contendo os identificadores dos participantes a serem removidos.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function removeParticipantsGroup(string $group_id, array $participants)
  {
    // Define o corpo da requisição para remover participantes de um grupo existente.
    $this->parth = "/{$this->key}/groups/{$group_id}/participants";
    $this->method = "DELETE";
    $this->body = json_encode([
      "participants" => $participants
    ]);
    // Executa a requisição e retorna o resultado.
    return $this->request();
  }

  /**
   * Deixa um grupo existente.
   *
   * @param string $group_id O identificador do grupo.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function leaveGroup(string $group_id)
  {
    // Define o caminho, método e corpo da requisição para deixar um grupo existente.
    $this->parth = "/{$this->key}/groups/{$group_id}";
    $this->method = "DELETE";
    // Executa a requisição e retorna o resultado.
    return $this->request();
  }

  /**
   * Promove participantes a administradores em um grupo existente.
   *
   * @param string $group_id O identificador do grupo.
   * @param array $participants Um array contendo os identificadores dos participantes a serem promovidos.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function promoteParticipantsGroup(string $group_id, array $participants, string $action)
  {
    // Define o corpo da requisição para promover participantes a administradores em um grupo existente.
    $this->parth = "/{$this->key}/groups/{$group_id}/role?action={$action}";
    $this->method = "POST";
    $this->body = json_encode(["participants" => $participants]);
    // Executa a requisição e retorna o resultado.
    return $this->request();
  }

  /**
   * Define quem pode enviar mensagens em um grupo específico.
   *
   * @param string $group_id O identificador do grupo.
   * @param bool $allow Indica se apenas administradores podem enviar mensagens no grupo.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function setWhoCanSendMessageGroup(string $group_id, bool $allow)
  {
    // Define o corpo da requisição para definir quem pode enviar mensagens em um grupo específico.
    // $this->parth = "/group/setWhoCanSendMessage?key={$this->key}&group_id={$group_id}&allowOnlyAdmins=" . $allow;
    // $this->method = "POST";
    // // Executa a requisição e retorna o resultado.
    // return $this->request();
  }

  /**
   * Define quem pode alterar configurações em um grupo específico.
   *
   * @param string $group_id O identificador do grupo.
   * @param bool $allow Indica se apenas administradores podem alterar configurações no grupo.
   *
   * @return mixed A resposta da requisição ou uma mensagem de erro em caso de falha.
   * @throws \RuntimeException Se ocorrer um erro durante a requisição cURL.
   */
  public function setWhoCanChangeSettingsGroup(string $group_id, bool $allow)
  {
    // Define o corpo da requisição para definir quem pode alterar configurações em um grupo específico.
    // $this->parth = "/group/setWhoCanChangeSettings?key={$this->key}&group_id={$group_id}&allowOnlyAdmins=" . $allow;
    // $this->method = "POST";
    // // Executa a requisição e retorna o resultado.
    // return $this->request();
  }




  public function constructWebhook()
  {

    $isMidia = false;

    $data = json_decode(file_get_contents('php://input'), true);

    if (!empty($data)) {

      $this->from = new stdClass();

      if (isset($data["data"]["key"]["remoteJid"])) {
        $this->from->remoteJid = preg_replace("/[^0-9]/", "", $data["data"]["key"]["remoteJid"]);
      }

      if (isset($data["data"]["key"]["id"])) {
        $this->from->msgId = $data["data"]["key"]["id"] ?? null;
      }

      if (isset($data["data"]["push_name"])) {
        $this->from->pushName = $data["data"]["push_name"] ?? null;
      }

      if (isset($data["data"]["messageType"])) {
        $this->from->messageType = $data["data"]["messageType"] ?? "unknown";
      }

      if (isset($data["data"]["msgContent"]["conversation"])) {
        $this->from->messageType = "text";
        $this->from->text = $data["data"]["msgContent"]["conversation"];
      }

      if (isset($data["data"]["msgContent"]["extendedTextMessage"]["text"])) {
        $this->from->messageType = "text";
        $this->from->text = $data["data"]["msgContent"]["extendedTextMessage"]["text"];
      }

      if (isset($data["data"]["msgContent"]["buttonsResponseMessage"])) {
        $this->from->messageType = "button";
        $this->from->selectedId = $data["data"]["msgContent"]["buttonsResponseMessage"]["selectedButtonId"];
      }


      if (isset($data["data"]["msgContent"]["listResponseMessage"])) {
        $this->from->messageType = "list";
        $this->from->selectedId = $data["data"]["msgContent"]["listResponseMessage"]["singleSelectReply"]["selectedRowId"];
        $this->from->title = $data["data"]["msgContent"]["listResponseMessage"]["title"];
      }

      if ($this->from->messageType == "messageContextInfo") {
        $this->from->messageType = "list";
        if (isset($data["data"]["msgContent"]["listResponseMessage"]["singleSelectReply"]["selectedRowId"])) {
          $this->from->selectedId = $data["data"]["msgContent"]["listResponseMessage"]["singleSelectReply"]["selectedRowId"];
        }
        if (isset($data["data"]["msgContent"]["listResponseMessage"]["title"])) {
          $this->from->text = $data["data"]["msgContent"]["listResponseMessage"]["title"];
        }
      }

      if (isset($data["data"]["msgContent"]["reactionMessage"])) {
        $this->from->messageType = "reaction";
        $this->from->text = $data["data"]["msgContent"]["reactionMessage"]["text"];
      }

      if ($this->from->latitude === "locationMessage") {
        $this->from->messageType = "location";
        $this->from->latitude = $data["data"]["msgContent"]["locationMessage"]["degreesLatitude"];
        $this->from->longitude = $data["data"]["msgContent"]["locationMessage"]["degreesLongitude"];
        $this->from->thumbnail = "data:image/jpeg;base64," . $data["data"]["msgContent"]["locationMessage"]["jpegThumbnail"];
      }

      if ($this->from->messageType === "liveLocationMessage") {
        $this->from->messageType = "liveLocation";
        $this->from->latitude = $data["data"]["msgContent"]["liveLocationMessage"]["degreesLatitude"];
        $this->from->longitude = $data["data"]["msgContent"]["liveLocationMessage"]["degreesLongitude"];
        $this->from->thumbnail = "data:image/jpeg;base64," . $data["data"]["msgContent"]["liveLocationMessage"]["jpegThumbnail"];
      }

      if ($this->from->messageType === "templateButtonReplyMessage") {
        $this->from->messageType = "button";
        $this->from->selectedId = $data["data"]["msgContent"]["templateButtonReplyMessage"]["selectedIndex"];
      }

      if ($this->from->messageType === "audioMessage") {
        $this->from->messageType = "audio";
        $isMidia = true;
      }
      if ($this->from->messageType === "imageMessage") {
        $this->from->messageType = "image";
        $isMidia = true;
      }

      if ($this->from->messageType === "stickerMessage") {
        $this->from->messageType = "sticker";
        $isMidia = true;
      }
      if ($this->from->messageType === "videoMessage") {
        $this->from->messageType = "video";
        $isMidia = true;
      }

      if ($this->from->messageType === "documentMessage") {
        $this->from->messageType = "document";
        $isMidia = true;
      }

      if ($this->from->messageType === "contactMessage") {
        $this->from->messageType = "contact";
        $displayName = $data["data"]["msgContent"]["contactMessage"]["displayName"] ?? "";
        $vcard = explode("\n", $data["data"]["msgContent"]["contactMessage"]["vcard"]);
        $c_b = explode(":", $vcard[4]);
        $contact = new stdClass();
        $contact->name = $displayName;
        $contact->number = $c_b[1] ?? "";
        $this->from->contact[] = $contact;
      }

      if ($this->from->messageType === "contactsArrayMessage") {
        $this->from->messageType = "contact";
        for ($i = 0; sizeof($data["data"]["msgContent"]["contactsArrayMessage"]["contacts"]) > $i; $i++) {
          $displayName = $data["data"]["msgContent"]["contactsArrayMessage"]["contacts"][$i]["displayName"];
          $vcard = explode("\n", $data["data"]["msgContent"]["contactsArrayMessage"]["contacts"][$i]["vcard"]);
          $c_b = explode(":", $vcard[4]);
          $contact = new stdClass();
          $contact->name = $displayName;
          $contact->number = $c_b[1] ?? "";
          $this->from->contact[] = $contact;
        }
      }

      if ($isMidia) {

        if (isset($data["data"]["msgContent"][$data["data"]["messageType"]]["mimetype"])) {
          $this->from->mimetype = $data["data"]["msgContent"][$data["data"]["messageType"]]["mimetype"];
        }
        if (!empty($data["data"]["msgContent"][$data["data"]["messageType"]]["jpegThumbnail"])) {
          $this->from->thumbnail = "data:image/jpeg;base64," . $data["data"]["msgContent"][$data["data"]["messageType"]]["jpegThumbnail"];
        }

        $this->from->messageKeys = [
          "messageKeys" => [
            "mediaKey" => $data["data"]["msgContent"][$data["data"]["messageType"]]["mediaKey"],
            "directPath" => $data["data"]["msgContent"][$data["data"]["messageType"]]["directPath"],
            "url" => $data["data"]["msgContent"][$data["data"]["messageType"]]["url"],
            "messageType" => $this->from->messageType
          ]
        ];

        $this->from->mediaBase64 = $data["data"]["fileBase64"];
        $this->from->mediaURL = $data["data"]["urlMedia"];

        if (isset($data["data"]["msgContent"][$data["data"]["messageType"]]["title"])) {
          $this->from->title = $data["data"]["msgContent"][$data["data"]["messageType"]]["title"];
        }

        if (isset($data["data"]["msgContent"][$data["data"]["messageType"]]["fileName"])) {
          $this->from->caption = $data["data"]["msgContent"][$data["data"]["messageType"]]["fileName"];
        }

        if (isset($data["data"]["msgContent"][$data["data"]["messageType"]]["caption"])) {
          $this->from->caption = $data["data"]["msgContent"][$data["data"]["messageType"]]["caption"];
        }
      }
    }
  }
}
