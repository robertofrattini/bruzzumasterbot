<?php

class Webhook
{

    private $url = APP_URL;
    private $has_custom_certificate = false;
    private $pending_update_count = 0;
    private $last_error_date;
    private $last_error_message;
    private $max_connections = 40;
    private $allowed_updates = [];

  function __construct(argument)
  {
    $this->getInfo();
  }

  public function setWebhook()
  {

  }

  public function deleteWebhook()
  {

  }

  public function getWebhookInfo()
  {

  }

}


?>
