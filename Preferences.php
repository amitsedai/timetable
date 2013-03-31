<?php

include_once 'Constants.php';
class Preferences {
  private $class_teacher_first_class;
  private $labs_start;
  private $b4break_periods;
  private $sat_totclass;
  private $max_consec_class;

  function set_class_teacher_first_class($flag){
    $this->class_teacher_first_class=$flag;
  }

  function is_class_teacher_class_first(){
    if($this->class_teacher_first_class=='YES' || $this->class_teacher_first_class==1){
      return TRUE;
    }
    return FALSE;
  }

  function get_labs_start(){
    return $this->labs_start;
  }

  function set_labs_start($labs_start){
    $this->labs_start=$labs_start;
  }


  function set_b4break_periods($b4break_periods){
    $this->b4break_periods=$b4break_periods;
  }

  function get_b4break_periods(){
    return $this->b4break_periods;
  }

  function get_sat_totclass(){
    return $this->sat_totclass;
  }

  function set_sat_totclass($sat_totclass){
    $this->sat_totclass=$sat_totclass;
  }

  function get_max_consec_class(){
    return $this->max_consec_class;
  }

  function set_max_consec_class($max_consec_class){
    $this->max_consec_class=$max_consec_class;
  }

  function get_dummy_subject(Teacher $subject_teacher, $hard_allocation = NULL, $name=NULL,$sub_type= NULL){
   $subject_id = mt_rand ( 1000 , 9999);
   if(is_null($name)){
    $name =  'NA';
   }
   if(is_null($sub_type)){
    $sub_type = Constants::TYPE_DUMMY;
   }
   $subj_total_count=1;
   $subject = new Subject($subject_id,$name,$sub_type, $subject_teacher,$subj_total_count);
   if(!is_null($hard_allocation)){
    $subject->set_hard_allocation($hard_allocation);
   }
   return $subject;
  }

}
