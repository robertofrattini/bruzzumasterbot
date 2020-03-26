<?php

/**
 *
 */
class Poll
{
  $active = false;
  function __construct()
  {
    $this->load();
  }
  function load($structure){
    $this->open_poll($structure[[]]);
    
  }
}



?>
