<?php

include_once "Constants.php";
include_once "Unavailability.php";
class Teacher{
  private $name;
  private $teacher_id;
  private $short_name;
  private $slots;
  private $class_assignment;
  private $unavailability;

  function __construct($teacher_id,$name){
    $this->teacher_id = $teacher_id;
    $this->name = $name;
    $unavailability = array();
  }

  function get_teacher_id(){
    return $this->teacher_id;
  }

  function getName(){
    return $this->name;
  }

  function get_slot(){
    return $this->slots;
  }

  function set_slot($slots){
    $this->slots = $slots;
  }

  function get_slot_data($day,$index){
    return $this->slots[$day][$index];
  }

  function get_unavailability(){
    return $this->unavailability;
  }

  function set_Unavailability(Unavailability $unavailability, $maxslots, Subject $dummy_subject){
    $this->unavailability = $unavailability;
    $week = $unavailability->get_week();
    $periods = $unavailability->get_periods();
    foreach ($periods as $period) {
      if($period <= $maxslots){
        //drupal_set_message("Unavailability -- $this is $week and $period");
        $this->slots[$week][$period]=$dummy_subject;
      }else{
        drupal_set_message("The Unavailability Slot: $period for ".$this->getName()." is greater than the max periods allocated",'notice');
      }
    }
  }

  function get_num_empty_slots($maxslot,$minslot,$max_consec,$breakb4periods){
    $empty_slots = $this->get_empty_slots($maxslot,$minslot,$max_consec,$breakb4periods);
    return count($empty_slots);
  }

  function get_empty_slots($maxslot,$minslot,$max_consec,$breakb4periods,$caller_func = NULL){
    $slots = array();
    $days =  Constants::get_days_week();
    if($maxslot!=$minslot){
      $days =  Constants::get_days_till_friday();
    }
    foreach ($days as $key=> $day) {
      for($i=1;$i<=$maxslot;$i++){
        if($this->is_slot_available($day,$i,$maxslot,$max_consec,$breakb4periods,$caller_func )) {
          $slots[] =  array('DAY'=>$day,'INDEX'=>$i);
        }
      }
    }
    if($maxslot!=$minslot){
      for($i=1;$i<=$minslot;$i++){
        if($this->is_slot_available(Constants::DAY_SATURDAY,$i,$minslot,$max_consec,$breakb4periods,$caller_func)){
          $slots[] =  array('DAY'=>Constants::DAY_SATURDAY,'INDEX'=>$i);
        }
      }
    }
    return $slots;
  }

  function get_availability($maxslot,$minslot){
    $days = Constants::get_days_week();
    if($maxslot!=$minslot){
      $days =  Constants::get_days_till_friday();
    }
    $empty_slot=0;
    $total_slot = 0;
    foreach ($days as $key=> $day) {
      for($i=1;$i<=$maxslot;$i++){
        $total_slot++;
        if(empty($this->slots[$day][$i])) {
          $empty_slot++;
        }
      }
    }
    if($maxslot!=$minslot){
      for($i=1;$i<=$minslot;$i++){
        $total_slot++;
        if(empty($this->slots[Constants::DAY_SATURDAY][$i])){
          $empty_slot++;
        }
      }
    }

    $filled_slot_count =  $total_slot-$empty_slot;
    $final_perc = round(($filled_slot_count/$total_slot)*100,0) ;
    return $final_perc;
  }

  function is_slot_available($day,$index,$maxslot,$max_consec=NULL,$b4break_periods=NULL){
    if($b4break_periods==NULL){
      $b4break_periods=array();
    }
    if($max_consec==NULL){
      $max_consec=Constants::NUM_TWO;
    }

    if(empty($this->slots[$day][$index])) {
      $right_pos = 0;
      $left_pos = 0;
      $pos = $index+1;
      $lessflag = FALSE;
      $moreflag = FALSE;
      //Move Right to Check if empty within limit
      if(in_array($index-1, $b4break_periods)){
        $lessflag = TRUE;
      }
      if(in_array($index, $b4break_periods)){
        $moreflag = TRUE;
      }

      if($moreflag == FALSE){
        while($pos <= $maxslot){
          if(empty($this->slots[$day][$pos])){
            break;
          }else if(in_array($pos-1, $b4break_periods)){
            break;
          }
          $pos++;
          $right_pos++;
        }
      }

      //Move Left to Check if empty within limit
      if($lessflag == FALSE){
        $pos = $index-1;
        while($pos > 0){
          if(empty($this->slots[$day][$pos])){
            break;
          }else if(in_array($pos, $b4break_periods) ){
            break;
          }
          $pos--;
          $left_pos++;
        }
      }
      if($max_consec > ($left_pos + $right_pos)){
        return TRUE;
      }
    }
    return FALSE;
  }

  function update_slot($day,$index,$data){
    $this->slots[$day][$index] = $data;
  }

  function clear_slot($day,$index){
    $this->slots[$day][$index] = NULL;
  }

  function print_empty_slots($maxslot,$minslot,$max_consec,$breakb4periods,$caller_func = NULL){
    $empty_slots= $this->get_empty_slots($maxslot,$minslot,$max_consec,$breakb4periods,$caller_func);
    $data ="";
    if(empty($empty_slots)){
      $data = "The teacher $this->name has no empty slots left. Either allocate a separate teacher for the class or modify allocation accordingly";
    }else{
      foreach ($empty_slots as $slots) {
        $data.= $slots['DAY']." And Period: ".$slots['INDEX'].", " ;
      }
    }
    return $data;
  }

  function set_init_slot($maxslots){
    for($i=1;$i<=$maxslots;$i++){
      $this->slots[Constants::DAY_MONDAY][$i]=NULL;
      $this->slots[Constants::DAY_TUESDAY][$i]=NULL;
      $this->slots[Constants::DAY_WEDNESDAY][$i]=NULL;
      $this->slots[Constants::DAY_THURSDAY][$i]=NULL;
      $this->slots[Constants::DAY_FRIDAY][$i]=NULL;
      $this->slots[Constants::DAY_SATURDAY][$i]=NULL;
    }
  }
  function __toString(){
    return $this->name();
  }
}


