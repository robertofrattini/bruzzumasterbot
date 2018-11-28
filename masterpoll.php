<?php

define('BOT_TOKEN','364944422:AAGd1iM_wwBqDEg119yUgtN-83y9zrVxJJU');
define('APP_URL','https://bruzzumasterbot.herokuapp.com/masterpoll.php');

include('API.php');
include('class.php');

#$api = new API(BOT_TOKEN);
#$update = $api->getUpdates();
$update = json_decode(file_get_contents('php://input'), true);
#$api->sendRequest('sendMessage',['text'=>"ok",'chat_id'=>$update['message']['chat']['id']]);
apiRequestJson('sendMessage',['text'=>"ok",'chat_id'=>$update['message']['chat']['id']]);
