<?php

class API {

  public $url;

  public $update;

  public $webhookStatus = false;
  public $pendingUpdateCount;

  public $chatId;
  public $userId;

  # Constructor
  public function __construct($token) {
    $this->url = 'https://api.telegram.org/bot'.BOT_TOKEN.'/';
    $this->getWebhookInfo();
  }

  # Endpoint methods
  public function request($method, array $params = array()) {
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

  public function upload($method, array $params = array()) {
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
    $webhookInfo = $this->request('getWebhookInfo');
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
    }
    else {
      $params = array(
        'offset' => $offset,
        'timeout' => $timeout,
        'limit' => $limit,
        #'allowed_updates' => $allowed_updates,#è un array vuoto
        );
      $result = $this->request('getUpdates',$params);
      if($result['ok']) {
        return $result['result']; #non è un oggetto
      }
    }
  }

  public function setWebhook() {
    $this->webhookStatus = true;
    $params = array(
      'url' => APP_URL,
      );
    return $this->request('setWebhook', $params);
  }

  public function deleteWebhook() {
    $this->webhookStatus = false;
    return $this->request('deleteWebhook');
  }

  # Second level methods
  public function sendMessage($text, $chatId = false) {
    if (!$chatId) {
      $chatId = $this->chatId;
    }
    $params = array(
      'chat_id' => $chatId,
      'text' => $text,
      'parse_mode' => "HTML",
      );
    return $this->request('sendMessage',$params);
  }

  public function forwardMessage($msgId, $chatId, $fromChatId = false) {
    if (!$fromChatId) {
      $fromChatId = $this->chatId;
    }
    $params = array(
      'chat_id' => $chatId,
      'from_chat_id' => $fromChatId,
      'message_id' => $msgId,
    );
    return $this->request('forwardMessage', $params);
  }

}

class Message {

  public $tag;
  public $botCommand;
  public $hashtag;

  public $userId;
  public $chatId;
  public $text;
  public $time;

  public function __construct($update) {
    $this->text = $update['message']['text'];

    $entity = $update['message']['entities']['0']; //elabora solo la prima Entity
    if ($entity) {
      $start = $entity['offset'];
      $lenght = $entity['lenght'];
      switch ($entity['type']) {
        case 'mention':
          $this->tag = substr($text,$start,$lenght);
          break;
        case 'hashtag':
          $this->hashtag = substr($text,$start,$lenght);
          break;
        case 'bot_command':
          $this->botCommand = substr($text,$start,$lenght);
          break;
        case 'text_mention':
          $this->tag = $entity['user']['id'];
          break;
        default:
          break;
      }
    }
  }

  //public function checkId($message) {}
  //public function isReply($message) {}
  //public function isForward($message) {}
  //public function checkBan($message) {}

}

class Chat {

  public $id;
  public $type;
  public $title;
  public $username;
  public $first_name;
  public $last_name;
  public $all_members_are_administrators;

  public function __construct($update) {
    $this->id = $update['message']['chat']['id'];
    $this->type = $update['message']['chat']['type'];
    $this->title = $update['message']['chat']['title'];
    $this->username = $update['message']['chat']['username'];
    $this->first_name = $update['message']['chat']['first_name'];
    $this->last_name = $update['message']['chat']['last_name'];
    $this->all_members_are_administrators = $update['message']['chat']['all_members_are_administrators'];
  }

}

class User {

  public $id;
  public $first_name;
  public $last_name;
  public $username;

  public function __construct($update) {
    $this->id = $update['message']['from']['id'];
    $this->username = $update['message']['from']['username'];
    $this->first_name = $update['message']['from']['first_name'];
    $this->last_name = $update['message']['from']['last_name'];
  }
}

/*class Postgres{

  public function connect() {
    $this->connection = pg_connect($this->credentials);
    return $this->connection;
  }

  public function disconnect() {
    $result = pg_close($this->connection);
    return $result;
  }

  function read($userId) {}
  function write($userArray) {}
  function filter($col,$value) {}

  public function addUser($userArray) {}
  public function removeUser($userId) {}
  public function updateUser() {}

  function is_garante($userId) {}
  function is_capo($userId) {}
  function is_vicecapo($userId) {}
  function is_cortedicastrazione($userId) {}
  function is_consiglioristretto($userId) {}
  function is_fuoridalbruzzu($userId) {}
  function is_fuoridalnonno($userId) {}

  function warn_count($userId) {}
  function warn_check($userId;$hourLimit="24") {}
  function warn_add($userId) {}
  function warn_remove($userId) {}
  function warn_reset($userId) []

}
class Redis{

  function __construct() {

  }
}


class SimplePoll {
  function __construct() {
    while(true) {
      $offset = $lastUpdate+1;
      $newUpdates = $api->getUpdates($offset);
      foreach ($newUpdates as $update) {
        $msgText = $update['message']['text'];
        if($msgText==="stop") {break 2;}
        else {
          if ($lastUpdate<$update['update_id']){
            $lastUpdate = $update['update_id'];
            $api->request('sendMessage',['chat_id' => $api->chatId,'text'=>"$msgText"]);
          }
        }
      }
      sleep(2);
	  }
  }
}


class Poll {

}

class Agenda {

}

class Vote {

}

class Warn {

}

class Ban {

}
?>
