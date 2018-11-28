<?=

define('BOT_TOKEN','364944422:AAGd1iM_wwBqDEg119yUgtN-83y9zrVxJJU');
define('APP_URL','https://bruzzumasterbot.herokuapp.com/bruzzumasterbot.php');

include('Classes.php');

$api = new API(BOT_TOKEN);
$upd = $api->getUpdates();

$msg = new Message($upd);
//$usr = new User($msg);//l'userId deve rimanere sempre lo stesso all'interno del longpolling, a meno che non sia necessario l'input anche da parte di altri utenti. Considera l'opzione di impostare i comandi anche in modalità webhook se per caso qualche utente vuole usufruire di un pannello di controllo già aperto da qualche altro utente
$cht = new Chat($msg);//chatId deve rimanere sempre lo stesso nel runtime dell'applicazione (anche nel loop), può cambiare solo quando il webhook è attivo

$lastUpdate = $upd['update_id'];
$api->request('sendMessage',['text'=>"Ok let's start! send me 'poll'"]);

if($msg['text']==="poll"){
  $res = $api->deleteWebhook();
  if ($res) {
    $api->request('sendMessage',['text'=>"webhook deleted"]);
  }
	$api->request('sendMessage',['text'=>"Longpolling initialising"]);
  while(true) {
    $offset = $lastUpdate+1;
    $newUpdates = $api->getUpdates($offset);
    foreach ($newUpdates as $update) {
      $msgText = $update['message']['text'];
      if($msgText==="stop") {break 2;}
      else {
        if ($lastUpdate<$update['update_id']){
          $lastUpdate = $update['update_id'];
          $api->request('sendMessage',['text'=>"$msgText"]);
        }
      }
    }
    sleep(2);
	}
}

$api->setWebhook();
































?>
