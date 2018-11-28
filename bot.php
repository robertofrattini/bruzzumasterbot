<?php

define('API_URL', 'https://api.telegram.org/bot364944422:AAGd1iM_wwBqDEg119yUgtN-83y9zrVxJJU/');
$update = json_decode(file_get_contents('php://input'), true);
$chatId = $update['message']['chat']['id'];
$params = [
  'method' => "sendMessage",
  'chat_id' => $chatId,
  'text' => "Ciao",
]
$ch = curl_init(API_URL);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
  curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
  $res = json_decode(curl_exec($handle),true);
curl_close($handle);

?>
