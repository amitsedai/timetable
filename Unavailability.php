<?php

include_once "Standard.php";
include_once "Constants.php";
class Unavailability {
  private $week;
  private $periods = array();

  function __construct($week,$periods){
    $this->week = $week;
    $this->periods = $periods;
  }

  function get_week(){
    return $this->week;
  }

  function get_periods(){
    return $this->periods;
  }
}


?>
