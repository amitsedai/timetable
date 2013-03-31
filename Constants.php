<?php

class Constants{
  const DAY_MONDAY = 'Monday';
  const DAY_TUESDAY = 'Tuesday';
  const DAY_WEDNESDAY = 'Wednesday';
  const DAY_THURSDAY = 'Thursday';
  const DAY_FRIDAY = 'Friday';
  const DAY_SATURDAY = 'Saturday';

  const TYPE_MAINSUBJECT = 1;
  const TYPE_OPTIONALSUBJECT = 2;
  const TYPE_LABSUBJECT = 3;
  const TYPE_DUMMY = 4;

  const NUM_ZERO = 0;
  const NUM_ONE = 1;
  const NUM_TWO = 2;
  const NUM_THREE = 3;
  const NUM_FOUR = 4;
  const NUM_FIVE = 5;
  const NUM_SIX = 6;
  const NUM_SEVEN = 7;

  static function get_days_week(){
    return array(Constants::DAY_MONDAY,Constants::DAY_TUESDAY,Constants::DAY_WEDNESDAY,Constants::DAY_THURSDAY
      ,Constants::DAY_FRIDAY,Constants::DAY_SATURDAY);
  }

  static function get_days_till_friday(){
    return array(Constants::DAY_MONDAY,Constants::DAY_TUESDAY,Constants::DAY_WEDNESDAY,Constants::DAY_THURSDAY
      ,Constants::DAY_FRIDAY);
  }
}
