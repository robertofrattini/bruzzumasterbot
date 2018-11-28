<?php

/**
 *
 */
class API
{
  private $url; #salva l'indirizzo per inviare le richieste all'api
  public $webhookStatus = false; #logico, vero se è presente un collegamento webhook
  public $pendingUpdateCount; #in longpolling, rappresenta il numero di update non ancora processati rimasti sul server

  function __construct($token)
  {
    $this->url = 'https://api.telegram.org/bot'.BOT_TOKEN.'/'; #crea l'url, che può essere utilizzata solo all'interno di questa classe
    $this->getWebhookInfo(); #assegna un valore allo stato del webhook (alla prima istanza dovrebbe fornire true)
  }

  # Endpoint methods
  public function sendRequest(string $method, array $params = array()) {
    $params['method'] = $method;
    $handle = curl_init();
      curl_setopt($handle, CURLOPT_URL, $this->url);
      curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($params));
      curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
    $result = json_decode(curl_exec($handle),true);
    curl_close($handle);
    return $result;
  }

  public function uploadFile($method, array $params = array()) {
    $params['method'] = $method;
    $handle = curl_init();
      curl_setopt($handle, CURLOPT_URL, $this->url);
      curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($params));
      curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-Type: multipart/form-data"));
    $result = json_decode(curl_exec($handle),true);
    curl_close($handle);
    return $result;
  }

  # First level methods
  public function getWebhookInfo() {
    $webhookInfo = $this->sendRequest('getWebhookInfo');
    if ($webhookInfo['url']) {
      $this->webhookStatus = true;
      return true;
    }
    else {
      $this->webhookStatus = false;
      $this->pendingUpdateCount = $webhookInfo['pending_update_count'];
      return false;
    }
  }

  public function getUpdates($offset = 0, $timeout = 30, $limit = 50, array $allowed_updates = array()) {
    if ($this->webhookStatus === true) {
      $update = json_decode(file_get_contents('php://input'),true);#non è il risultato della funzione
      $this->chatId = $update['message']['chat']['id'];#ok ma non va in questa classe
      $this->userId = $update['message']['from']['id'];#ok ma non va in questa classe
      return $update;#aggiunta, altrimenti non spara fuori niente questa funzione
    } else {
      $params = array(
        'offset' => $offset,
        'timeout' => $timeout,
        'limit' => $limit,
        #'allowed_updates' => $allowed_updates,#è un array vuoto
        );
      $result = $this->sendRequest('getUpdates',$params);
      if($result['ok']) {
        return $result['result']; #non è un oggetto
      }
    }
  }
}
