<?php

define('API_URL', 'https://api.telegram.org/bot364944422:AAGd1iM_wwBqDEg119yUgtN-83y9zrVxJJU/');
function exec_curl_request($handle) {
    $response = curl_exec($handle);
    if ($response === false) {
      $errno = curl_errno($handle);
      $error = curl_error($handle);
      error_log("Curl returned error $errno: $error\n");
      curl_close($handle);
      return false;
    }
    $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
    curl_close($handle);
    if ($http_code >= 500) {
      sleep(10);
      return false;
    } else if ($http_code != 200) {
      $response = json_decode($response, true);
      error_log("Request has failed with error {$response['error_code']}: {$response['description']}\n");
      if ($http_code == 401) {
        throw new Exception('Invalid access token provided');
      }
      return false;
    } else {
      $response = json_decode($response, true);
      if (isset($response['description'])) {
        error_log("Request was successfull: {$response['description']}\n");
      }
      $response = $response['result'];
    }
    return $response;
  }
function apiRequestJson($method, $parameters) {
    if (!is_string($method)) {
      error_log("Method name must be a string\n");
      return false;
    }
    if (!$parameters) {
      $parameters = array();
    }
    else if (!is_array($parameters)) {
      error_log("Parameters must be an array\n");
      return false;
    }
    $parameters["method"] = $method;
    $handle = curl_init(API_URL);
      curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($handle, CURLOPT_TIMEOUT, 60);
      curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($parameters));
      curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
    return exec_curl_request($handle);
  }

# getting updates from server
$update = json_decode(file_get_contents('php://input'), true);
$chatId = $update['message']['chat']['id'];

apiRequestJson('sendMessage',['chat_id' => $chatId,'text' => "chat_id = ".$chatId]);

# metodo base
$last_update = $update['update_id'];
set_time_limit(60);
// main loop
while (true) {
  $offset = $lastUpdate+1;
  $result = apiRequestJson('getUpdates',['offset' => $lastUpdate+1]);
  foreach ($result["result"] as $updateNo => $update) {
    if ($update['message']['chat']['id']===$chatId) {
      apiRequestJson('sendMessage',['chat_id' => $chatId,'text' => "update_id = ".$update['update_id']]);
      if ($update['message']['text']==="stop") {
        apiRequestJson('sendMessage',['chat_id' => $chatId,'text' => "ok!"]);
        break 2;
      } else {
        if ($lastUpdate<$update['update_id']){
          $lastUpdate = $update['update_id'];
          $api->request('sendMessage',['text'=>"$msgText"]);
          apiRequestJson('sendMessage',['chat_id' => $chatId,'text' => "Say stop"]);
        }
      }
    } else {
      apiRequestJson('sendMessage',['chat_id' => $chatId,'text' => "chat_id = ".$chatId]);
    }
  }
	sleep(3);
}
