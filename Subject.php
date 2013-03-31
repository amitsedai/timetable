<?php

include_once "Teacher.php";
class Subject{
  private $name;
  private $sub_type;
  private $subject_id;
  private $subject_teacher;
  private $subj_total_count;
  private $class_count; //count classes per week for the Section
  private $is_hard_allocation;
  private $is_subj_shared; //shared across 2 or more sections
  private $shared_sections;
  private $room_booking_reqd; //Is the room booking for the subject -> Case for Lab subjects
  private $allocated_room; //Shared room for Labs, Library etc
  private $num_consec_class;

  function __construct($subject_id,$name,$sub_type,Teacher $subject_teacher=NULL,$subj_total_count=NULL){
    $this->subject_id = $subject_id;
    $this->name = $name;
    $this->sub_type = $sub_type;
    $this->subject_teacher = $subject_teacher;
    $this->subj_total_count = $subj_total_count;
    $this->class_count = 0;
    $this->is_hard_allocation=FALSE;
  }

  function get_num_consec_class(){
    return $this->num_consec_class;
  }

  function set_num_consec_class($num_consec_class){
    $this->num_consec_class=$num_consec_class;
  }

  function get_is_subj_shared(){
    return $this->is_subj_shared;
  }

  function set_is_subj_shared($is_subj_shared){
    $this->is_subj_shared=$is_subj_shared;
  }

  function get_shared_sections(){
    return $this->shared_sections;
  }

  function set_shared_sections($shared_sections){
    $this->shared_sections= $shared_sections;
  }

  function get_room_booking_reqd(){
    return $this->room_booking_reqd;
  }

  function set_room_booking_reqd($room_booking_reqd){
    $this->room_booking_reqd = $room_booking_reqd;
  }

  function set_allocated_room($allocated_room){
    $this->allocated_room = $allocated_room;
  }

  function get_allocated_room(){
    return $this->allocated_room;
  }


  function get_subject_id(){
    return $this->subject_id;
  }

  function increment_count(){
    $this->class_count++;
  }

  function get_current_count(){
    return $this->class_count;
  }

  function get_total_count(){
    return $this->subj_total_count;
  }

  function get_allotment_left(){
    return ($this->subj_total_count-$this->class_count);
  }

  function get_subject_teacher(){
    return $this->subject_teacher;
  }

  function set_subject_teacher(Teacher $subject_teacher){
    $this->subject_teacher = $subject_teacher;
  }

  function get_sub_type(){
    return $this->sub_type;
  }

  function set_sub_type($sub_type){
    $this->sub_type=$sub_type;
  }

  function is_full(){
    return ($this->class_count >= $this->subj_total_count) ? TRUE:FALSE;
  }

 	function getName(){
    return $this->name;
  }

  function is_hard_allocation(){
    return $this->is_hard_allocation;
  }

  function set_hard_allocation($allocation_status){
    $this->is_hard_allocation = $allocation_status;
  }

  function __toString(){
    return $this->name;
  }
}

?>
