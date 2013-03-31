<?php

include_once "Standard.php";
include_once "Constants.php";
class TimeTable {
  private $slots;
  private $maxslots;
  private $minslots;

  function __construct(){
    $this->slots= array();
  }

  function set_slot($slots){
    $this->slots=$slots;
  }

  function get_slot(){
    return $this->slots;
  }

  function set_timetableslots($maxslots,$minslots=NULL){
    $this->maxslots= $maxslots;
    $this->minslots= $minslots;
    if($minslots==NULL){
      $this->minslots= $maxslots;
    }
    $this->set_init_slot();
  }

  function get_slot_data($day,$index){
    return $this->slots[$day][$index];
  }

  function set_init_slot(){
    for($i=1;$i<=$this->maxslots;$i++){
      $this->slots[Constants::DAY_MONDAY][$i]=NULL;
      $this->slots[Constants::DAY_TUESDAY][$i]=NULL;
      $this->slots[Constants::DAY_WEDNESDAY][$i]=NULL;
      $this->slots[Constants::DAY_THURSDAY][$i]=NULL;
      $this->slots[Constants::DAY_FRIDAY][$i]=NULL;
      $this->slots[Constants::DAY_SATURDAY][$i]=NULL;
    }
  }

  function update_slot_data($day,$index,$data){
    $this->slots[$day][$index] = $data;
  }

  function clear_slot($day,$index){
    $this->slots[$day][$index] = NULL;
  }

  function is_slot_empty($subject,$day,$index,$maxslots,$tchr_maxconsec,$b4_break_periods){
    if(empty($this->slots[$day][$index])) {
      $teacher = $subject->get_subject_teacher();
      if($teacher->is_slot_available($day,$index,$maxslots,$tchr_maxconsec,$b4_break_periods)) {
        if(!$subject->is_full()){
          if($subject->get_sub_type()==Constants::TYPE_LABSUBJECT){
            if($index!==$maxslots && $teacher->is_slot_available($day,$index+1,$maxslots,$tchr_maxconsec,$b4_break_periods)){
              return TRUE;
            }
          }else{
            return TRUE;
          }
        }
      }
    }
    return FALSE;
  }


  function gen_replace_subject_position($school,$section, $pending_subject,$weekday,$index){
    $start=Constants::NUM_TWO;
    $days =  Constants::get_days_week();
    if($this->minslots!=$this->maxslots){
      $days =  Constants::get_days_till_friday();
    }
    $tchr_pending_subject = $pending_subject->get_subject_teacher();
    foreach ($days as $day) {
      for($i=$start;$i<=$this->maxslots;$i++){
        if($weekday!=$day && $index!=$i &&  isset($this->slots[$day][$i])){
          $subject = $this->slots[$day][$i];
          $subject_tchr = $subject->get_subject_teacher();
          if($subject_tchr->get_teacher_id() != $tchr_pending_subject->get_teacher_id()) {
            $flag = $this->replace_subject_position($school,$section, $subject,$weekday,$index,$pending_subject,$day,$i);
            //drupal_set_message("Replaceing $subject from $day and $i to $weekday and $index for pending_subject $pending_subject. Outcome: $flag");
            if($flag ==TRUE){
              return TRUE;
            }
          }
        }
      }
    }
    return FALSE;
  }

  function replace_subject_position(&$school,$section, $empty_slot_subj,$weekday,$index,$pending_subject,$day,$i){
    $tchr_max_consec = $school->get_preferences()->get_max_consec_class();
    $b4break_periods = $school->get_preferences()->get_b4break_periods();
    $empty_slot_subjteacher = $empty_slot_subj->get_subject_teacher();
    $pending_subjteacher = $pending_subject->get_subject_teacher();
    $maxslots = $weekday==Constants::DAY_SATURDAY?$this->maxslots:$this->minslots;
    //drupal_set_message("Empty slot subj: $empty_slot_subj pending_subject $pending_subject to check at: $weekday and $index");
    $teacher_available = $empty_slot_subjteacher->is_slot_available($weekday,$index,$maxslots,$tchr_max_consec,$b4break_periods);

    if($empty_slot_subj->is_hard_allocation() == FALSE
      &&  $teacher_available==TRUE
      && !$pending_subject->is_full()) {
      //drupal_set_message("Change happens here");
      $this->slots[$weekday][$index]=$empty_slot_subj;
      $empty_slot_subjteacher->update_slot($weekday,$index,$section);

      $empty_slot_subjteacher->clear_slot($day,$i);
      $this->slots[$day][$i]=$pending_subject;
      //$pending_subject->increment_count();
      $pending_subjteacher->update_slot($day,$i,$section);
      return TRUE;
    }
    return FALSE;
  }

  function update_subject_position(&$school,$pending_subject,$weekday,$index,$day,$i){
    $tchr_max_consec = $school->get_preferences()->get_max_consec_class();
    $pending_subjteacher = $pending_subject->get_subject_teacher();
    if($pending_subject->get_sub_type()!==Constants::TYPE_LABSUBJECT
      && $pending_subject->is_hard_allocation() !== TRUE
      &&  $empty_slot_subjteacher->is_slot_available($weekday,$index,$maxslots,$tchr_max_consec,$school->get_preferences()->get_b4break_periods())) {
      $this->slots[$weekday][$index]=$empty_slot_subj;
      $pending_subjteacher->clear_slot($weekday,$index);
      $this->slots[$day][$i]=$pending_subject;
      $pending_subjteacher->update_slot($day,$i,$section);
      //$school->update_teacher($pending_subjteacher);
      return;
    }
  }


  function set_empty_slots(&$school,$section, &$pending_subject,$empty_slot,$days,$maxslots,$init_start,$tchr_max_consec=NULL){
    //$init_start=Constants::NUM_ONE;  //Constant Assumed For Now
    $pending_subjteacher = $pending_subject->get_subject_teacher();
    if(empty($tchr_max_consec)){
      $tchr_max_consec = $school->get_preferences()->get_max_consec_class();
    }
    $break4period = $school->get_preferences()->get_b4break_periods();
    $index = $empty_slot['INDEX'];
    $weekday = $empty_slot['DAY'];

    if($pending_subjteacher->is_slot_available($weekday,$index,$maxslots,$tchr_max_consec,$break4period)
      &&  $pending_subject->is_full()==FALSE) {
      //drupal_set_message("Empty slot present for: $pending_subject of $section");
      $this->slots[$weekday][$index]=$pending_subject;
      $pending_subjteacher->update_slot($weekday,$index,$section);
      $pending_subject->increment_count();
      return ;
    }
    foreach ($days as $day) {
      for($i=$init_start;$i<=$maxslots;$i++){
        if(!empty($this->slots[$day][$i]) && $day!=$weekday && $index!=$i){
          $subject = $this->slots[$day][$i];
          $teacher = $subject->get_subject_teacher();
          if($subject->is_hard_allocation() != TRUE
            && $pending_subjteacher->is_slot_available($day,$i,$maxslots,$tchr_max_consec,$break4period)
            &&  $pending_subject->is_full()==FALSE
            &&  $teacher->is_slot_available($weekday,$index,$maxslots,$tchr_max_consec,$break4period)){
            $this->slots[$weekday][$index]=$subject;
            $teacher->clear_slot($day,$i);
            $this->slots[$day][$i]=$pending_subject;
            $pending_subject->increment_count();
            $pending_subjteacher->update_slot($day,$i,$section);
            $teacher->update_slot($weekday,$index,$section);
            //drupal_set_message("Pending Subj: $pending_subject placed at: $day and $i and $subject moved to $weekday and $index for $section");
            return ;
          }
        }
      }
    }
  }

  function update_empty_slot($school,$section, &$pending_subject,$tchr_max_consec=NULL){
    $empty_slots = $this->get_empty_slot('ALL');
    $days =  Constants::get_days_week();
    if($empty_slots!==NULL){
      foreach ($empty_slots as $empty_slot) {
        if($pending_subject->is_full()==FALSE){
          $this->set_empty_slots($school,$section,$pending_subject,$empty_slot,$days,$this->minslots,Constants::NUM_ONE,$tchr_max_consec=NULL);
          if($this->minslots!=$this->maxslots){
            $days =  Constants::get_days_till_friday();
            $this->set_empty_slots($school,$section,$pending_subject,$empty_slot,$days,$this->maxslots,$this->minslots+1,$tchr_max_consec=NULL);
          }
        }
      }
    }
  }

  function get_empty_slot($flag=NULL){
    $empty_slot = array();
    $days =  Constants::get_days_till_friday();
    for($i=$this->maxslots;$i>=1;$i--){
      foreach ($days as $key=> $day) {
        if(empty($this->slots[$day][$i])) {
          $empty_slot[]= array('DAY'=>$day,'INDEX'=>$i);
        }
      }
    }
    for($i=$this->minslots;$i>=1;$i--){
       $day =  Constants::DAY_SATURDAY;
      if(empty($this->slots[$day][$i])){
        $empty_slot[]= array('DAY'=>$day,'INDEX'=>$i);
      }
    }
    if($flag==NULL){
      return $empty_slot[0];
    }
    return $empty_slot;
  }

  function update_resource_slots(&$section,&$subject,&$teacher,$day,$index,$maxslots,$tchr_maxconsec,$b4break_periods,$shared_section =NULL){
    $subj_consec = $subject->get_num_consec_class();
    $is_allotable =TRUE;
    for($i=1; $i <$subj_consec ; $i++) {  // First check already done
      if($this->is_slot_empty($subject,$day,($index + $i),$maxslots,$tchr_maxconsec,$b4break_periods)){
        if(!empty($shared_section)) {
          foreach ($shared_section as $school_section) {
            if(($school_section instanceof Section)
              && $school_section->get_Id()!= $section->get_Id()) {
              $shared_timetable = $school_section->get_timetableobj();
              if(($shared_timetable instanceof TimeTable)
                && $shared_timetable->is_slot_empty($subject,$day,($index+$i),$maxslots,$tchr_maxconsec,$b4break_periods)) {
               // drupal_set_message("Hello TimeTable");
              }else{
                drupal_set_message("For $subject Is allotable false is : $is_allotable");
                $is_allotable =FALSE;
                break;
              }
            }
          }
        }else{
          $is_allotable =FALSE;
          break;
        }
      }
    }

    if($is_allotable == TRUE){
      for($i=0; $i <$subj_consec ; $i++) {
        if(!$subject->is_full()){
          $this->slots[$day][$index+$i] = $subject;
          $teacher->update_slot($day,$index+$i,$section);
          $subject->increment_count();
          if(!empty($shared_section)) {
            foreach ($shared_section as $school_section) {
              if($school_section instanceof Section){
                $shared_timetable = $school_section->get_timetableobj();
                $shared_subject = clone $subject;
                $shared_subject->set_shared_sections(NULL);
                $shared_subject->set_is_subj_shared(Constants::NUM_ZERO);
                $subject->set_hard_allocation(TRUE);
                $shared_subject->set_hard_allocation(TRUE);
                $shared_timetable->update_slot_data($day,$index+$i,$shared_subject);
              }
            }
          }
        }
      }
    }
  }

  function set_slotdata(&$school,&$section,$subjects,$days,$maxslots,$start,$tchr_maxconsec=NULL){
    if(empty($tchr_maxconsec)) {
      $tchr_maxconsec = $school->get_preferences()->get_max_consec_class();
    }
    $b4break_periods = $school->get_preferences()->get_b4break_periods();
    for($i=$start;$i<= $maxslots ; $i++){
      foreach ($days as $day) {
        foreach ($subjects as $key => $subject) {
          $teacher = $subject->get_subject_teacher();
          $shared_sections_data = $subject->get_shared_sections();
          $shared_section = array();
          $shared_subject = NULL;
          $shared_timetable = NULL;
          if(!empty($shared_sections_data)) {
            foreach ($shared_sections_data as $key => $value) {
              $school_section = $school->get_section($value);
              if(!is_null($school_section) && $school_section instanceof Section){
                array_push($shared_section, $school_section);
              }
            }
            $shared_subject =  clone $subject;
            $shared_subject->set_shared_sections(NULL);
            $shared_subject->set_is_subj_shared(Constants::NUM_ZERO);
          }
          if($this->is_slot_empty($subject,$day,$i,$maxslots,$tchr_maxconsec,$b4break_periods)) {
            if($subject->get_sub_type()==Constants::TYPE_LABSUBJECT ){
              $this->update_resource_slots($section,$subject, $teacher,$day,$i,$maxslots,$tchr_maxconsec,$b4break_periods,$shared_section);
            }else if(!empty($shared_section)) {
              $is_allotable = TRUE;
              foreach ($shared_section as $school_section) {
                $shared_timetable = $school_section->get_timetableobj();
                if(!empty($shared_timetable)
                 // && $school_section->get_Id()!= $section->get_Id()
                  && $shared_timetable->is_slot_empty($shared_subject,$day,$i,$maxslots,$tchr_maxconsec,$b4break_periods)) {
                }else{
                  $is_allotable =FALSE;
                  break;
                }
              }
              if($is_allotable == TRUE){
                $this->slots[$day][$i] = $subject;
                $teacher->update_slot($day,$i,$section);
                $subject->set_hard_allocation(TRUE);
                $subject->increment_count();
                //dpm($shared_section);
                foreach ($shared_section as $school_section) {
                  if(($school_section instanceof Section)
                    && $school_section->get_Id()!= $section->get_Id()) {
                    //drupal_set_message("Section is: $school_section and Shared Subject is: $shared_subject and subj is $subject");
                    $shared_timetable = $school_section->get_timetableobj();
                    $shared_subject->set_hard_allocation(TRUE);
                    $shared_timetable->update_slot_data($day,$i,$shared_subject);
                  }
                }
              }
            }else{
              $this->slots[$day][$i] = $subject;
              $teacher->update_slot($day,$i,$section);
              $subject->increment_count();
            }
          }
        }
      }
    }
    //$school->set_sections($section);
  }

  function update_preallocated_slots(&$school,&$section,$subject,$days,$periods){
    $teacher = $subject->get_subject_teacher();
    $tchr_maxconsec = $school->get_preferences()->get_max_consec_class();
    $b4break_periods = $school->get_preferences()->get_b4break_periods();
    foreach ($days as $day) {
      foreach ($periods as $i) {
        if($this->is_slot_empty($subject,$day,$i,$this->maxslots, $tchr_maxconsec,$b4break_periods)) {
          if($subject->get_sub_type()==Constants::TYPE_LABSUBJECT){
              if(($day != DAY_SATURDAY && ($i+1) <= $this->maxslots) || ($day == DAY_SATURDAY && ($i+1) <= $this->minslots)) {
                $this->slots[$day][$i] = $subject;
                $teacher->update_slot($day,$i,$section);
                $subject->increment_count();
                $this->slots[$day][$i+1] = $subject;
                $teacher->update_slot($day,$i+1,$section);
                $subject->increment_count();
              }
          }else{
            $this->slots[$day][$i] = $subject;
            $teacher->update_slot($day,$i,$section);
            $subject->increment_count();
          }
          //$subject->set_subject_teacher($teacher);
          //$school->update_teacher($teacher);
        }
      }
    }
   // $school->set_sections($section);
  }


  function update_slot(&$school,&$section,$subjects,$start=NULL){
    if($start==NULL){
      $start=Constants::NUM_ONE;
    }
    $days =  Constants::get_days_week();
    $this->set_slotdata($school,$section,$subjects,$days,$this->minslots,$start);
    if($this->minslots!=$this->maxslots){
      $days =  Constants::get_days_till_friday();
      $start = $this->minslots+1;
      $this->set_slotdata($school,$section,$subjects,$days,$this->maxslots,$start);
    }
  }
}

?>
