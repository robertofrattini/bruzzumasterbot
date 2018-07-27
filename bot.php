<?php

$redis = predis/Client(getenv(REDIS_URL));
$redis->set("key","value");
echo $redis->get("key");

?>
