<?php

include_once "Subject.php";
class Standard {
  private $name;
  private $id;
  private $subjects;  

  function __construct($name,$id){
     $this->name = $name;
     $this->id = $id;
     $this->subjects = array(Constants::TYPE_MAINSUBJECT=>array()
      ,Constants::TYPE_OPTIONALSUBJECT=>array(),Constants::TYPE_LABSUBJECT=>array());
  }

  function getName(){
    return $this->name;
  }

  function get_Id(){
    return $this->id;
  } 

  function set_subjects(Subject $subject){
    $this->subjects[$subject->get_sub_type()][$subject->get_subject_id()]=$subject;
  }

  function get_subjects($subject_type){
    return $this->subjects[$subject_type];
  }

  function get_subject($subject_id){
    $subject = NULL;
    if(isset($this->subjects[Constants::TYPE_MAINSUBJECT][$subject_id])){
     $subject= $this->subjects[Constants::TYPE_MAINSUBJECT][$subject_id];
    }else if(isset($this->subjects[Constants::TYPE_OPTIONALSUBJECT][$subject_id])){
     $subject= $this->subjects[Constants::TYPE_OPTIONALSUBJECT][$subject_id];
    }else if(isset($this->subjects[Constants::TYPE_LABSUBJECT][$subject_id])){
     $subject= $this->subjects[Constants::TYPE_LABSUBJECT][$subject_id];
    }
    return $subject;
  }

  function get_allsubjects(){
    $all_subjects =$this->subjects[Constants::TYPE_MAINSUBJECT];
    $result = array_merge($this->subjects[Constants::TYPE_OPTIONALSUBJECT], $this->subjects[Constants::TYPE_LABSUBJECT]);
    return array_merge($all_subjects, $result);
  }

  

}

?>