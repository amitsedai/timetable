<?php

include_once "Standard.php";
include_once "Constants.php";
include_once "TimeTable.php";
include_once "Teacher.php";
class Section extends Standard{
  private $timetable; 
  private $class_teacher;   

  function __construct($name,$id){
    parent::__construct($name,$id);
    //$this->name= $name;
    $this->timetable = new TimeTable();
    $this->timetable->set_init_slot(); 
  }

  function get_timetable(){
    return $this->timetable->get_slot();
  }

  function set_timetable($timetable){
    $this->timetable->set_slot($timetable);
  }

  function get_timetableobj(){
    return $this->timetable;
  }

  function get_class_teacher(){
    return $this->class_teacher;
  }  

  function set_class_teacher(Teacher $class_teacher){
    $this->class_teacher=$class_teacher;
  }

  function __toString(){
    return $this->getName();
  }

}

?>