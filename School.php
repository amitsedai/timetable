<?php

include_once "Standard.php";
include_once "Section.php";
include_once "Teacher.php";
include_once "Section.php";
include_once "Preferences.php";
include_once "Constants.php";
class School {
  private $name;
  private $id;
  private $subjects;
  private $teachers;
  private $sections;
  private $slots_max;
  private $slots_min;
  private $subj_max_count;
  private $preferences;
  private $section_class_count;

  function __construct($id,$name){
    $this->id = $id;
    $this->name = $name;
    $this->subjects = array();
    $this->teachers = array();
    $this->sections = array();
    $section_class_count = array();
  }

  function set_section_class_count($section_name,$tot_count){
    $this->section_class_count[$section_name] = $tot_count;
  }

  function get_section_class_count(){
    return $this->section_class_count;
  }

  function get_Id(){
    return $this->id;
  }

  function set_teachers(Teacher $teacher){
    if(empty($this->teachers[$teacher->get_teacher_id()])){
      $teacher->set_init_slot($this->get_slotmax());      
    }    
    $this->teachers[$teacher->get_teacher_id()]  = $teacher;
  }

  function update_teacher(Teacher $teacher){
    $this->teachers[$teacher->get_teacher_id()]  = $teacher;  
  }

  function get_teacher($id){
    if(isset($this->teachers[$id])){
      return $this->teachers[$id];
    }
    return NULL;
  }  

  function get_teachers(){
   return $this->teachers; 
  }

  function set_subjects(Subject $subject){
   $this->subjects[$subject->get_subject_id()]  = $subject; 
  }

  function get_subjects(){
   return $this->subjects; 
  }

  function get_subject($subject_id){
    if(isset($this->subjects[$subject_id])){
      return $this->subjects[$subject_id]; 
    }
    return NULL;
  }

  function set_sections(Section $section){
   $this->sections[$section->get_Id()]  = $section; 
  }

  function get_sections(){
   return $this->sections; 
  }

  function total_class_count(){
    return (Constants::NUM_FIVE*$this->get_slotmax() + $this->preferences->get_sat_totclass() );
  }

  function get_section($id){
    if(isset($this->sections[$id])){
      return $this->sections[$id];
    }
    return NULL;
  }

  function set_slotmax($max){
    $this->slots_max = $max;
  }

  function get_slotmax(){
    return $this->slots_max;
  }

  function set_slotmin($min){
    $this->slots_mins = $min;
  }

  function get_slotmin(){
    return $this->slots_min;
  }

  function set_subj_max_count($subj_max_count){
   $this->subj_max_count  = $subj_max_count; 
  }

  function get_subj_max_count(){
   return $this->subj_max_count; 
  }

  function set_preferences(Preferences $preferences){
    $this->preferences  = $preferences; 
  }

  function get_preferences(){
   return $this->preferences; 
  }

}