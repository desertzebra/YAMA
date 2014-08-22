<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Question_Options
 *
 * @author desertzebra
 */
require_once "config.php";
require_once "$CFG_YAMA->yamadir/model/Content_Model.php";
require_once "$CFG_YAMA->yamadir/model/content/quiz/Question_Category_Content.php";
require_once "$CFG_YAMA->yamadir/model/content/quiz/Answer_Content.php";
class Question_Options{
    public $id;
    public $questionId;
    public $synchronize;
    public $single;
    public $shuffleanswers;
    public $correctfeedback;
    public $correctfeedbackformat;
    public $partiallycorrectfeedback;
    public $partiallycorrectfeedbackformat;
    public $incorrectfeedback;
    public $incorrectfeedbackformat;
    public $answernumbering;
    public $shownumcorrect;
    public $answers;
    public $units;
    public $unitgradingtype;
    public $unitpenalty;
    public $showunits;
    public $unitsleft;
    
    public function __construct(){
    $this->answers = array();
    $this->units = array();
   }
   public function populate($option){
       //print "\r\n>>>Populate Options\r\n";
       //print_r($option);
       if(isset($option->id)){
           $this->id = $option->id;
           unset($option->id);
       }
       if(isset($option->question)){
           $this->questionId = $option->question;
           unset($option->question);
       }else if(isset($option->questionId)){
           $this->questionId = $option->questionId;
           unset($option->questionId);
       }
       if(isset($option->synchronize)){
           $this->synchronize = $option->synchronize;
           unset($option->synchronize);
       }else{
           $this->synchronize = 0;
       }
       if(isset($option->single)){
           $this->single = $option->single;
           unset($option->single);
       }else{
           $this->single = 0;
       }
       if(isset($option->shuffleanswers)){
           $this->shuffleanswers = $option->shuffleanswers;
           unset($option->shuffleanswers);
       }else{
           $this->shuffleanswers = 0;
       }
       if(isset($option->correctfeedback)){
           $this->correctfeedback = $option->correctfeedback;
           unset($option->correctfeedback);
       }
       if(isset($option->correctfeedbackformat)){
               $this->correctfeedbackformat = $option->correctfeedbackformat;
       }else{
               $this->correctfeedbackformat = 0;
       }
       if(isset($option->partiallycorrectfeedback)){
           $this->partiallycorrectfeedback = $option->partiallycorrectfeedback;
           unset($option->partiallycorrectfeedback);
       }
       if(isset($option->partiallycorrectfeedbackformat)){
               $this->partiallycorrectfeedbackformat = $option->partiallycorrectfeedbackformat;
       }else{
               $this->partiallycorrectfeedbackformat = 0;
       }
       if(isset($option->incorrectfeedback)){
           $this->incorrectfeedback = $option->incorrectfeedback;
           unset($option->incorrectfeedback);
       }
       if(isset($option->incorrectfeedbackformat)){
               $this->incorrectfeedbackformat = $option->incorrectfeedbackformat;
       }else{
               $this->incorrectfeedbackformat = 0;
       }
       if(isset($option->answernumbering)){
           $this->answernumbering = $option->answernumbering;
           unset($option->answernumbering);
       }else{
           $this->answernumbering = 'abc';
       }
       if(isset($option->showcorrect)){
           $this->showcorrect = $option->showcorrect;
           unset($option->showcorrect);
       }else{
           $this->showcorrect = 0;
       }
       if(isset($option->units)){
           $this->units = $option->units;
           unset($option->units);
       }
       if(isset($option->unitgradingtype)){
           $this->unitgradingtype = $option->unitgradingtype;
           unset($option->unitgradingtype);
       }else{
           $this->unitgradingtype = 0;
       }
       if(isset($option->unitpenalty)){
           $this->unitpenalty = $option->unitpenalty;
           unset($option->unitpenalty);
       }
       if(isset($option->showunits)){
           $this->showunits = $option->showunits;
           unset($option->showunits);
       }
       if(isset($option->unitsleft)){
           $this->unitsleft = $option->unitsleft;
           unset($option->unitsleft);
       }else{
           $this->unitsleft = 0;
       }
       if(isset($option->answers)){
           foreach($option->answers as $ans){
               $ans_obj = new Answer_Content();
               $ans_obj->populate($ans);
               array_push($this->answers, $ans_obj);
           }
       }
       
   }
   function toMoodleObj(){
       if(isset($this->answers)){
           foreach($this->answers as $answerObj){
             $answerObj->toMoodleObj();
           }
       }
   }
   function __get($name) {
        $func = 'get_' . $name;
        if (method_exists($this, $func)) {
            return $this->$func();
        } else {
            return $this->$name;
        }
    }
    
    function __set($name, $value) {
        $func = 'set_' . $name;
        if (method_exists($this, $func)) {
            return $this->$func($value);
        } else {
            $this->$name = $value;
        }
    }

    function __toString() {
        $ret_qOption_str = "Option($this->id)=\r\n";
        $ret_qOption_str .= "Question Id = $this->questionId \r\n";
        $ret_qOption_str .= "Synchronize = $this->synchronize \r\n";
        $ret_qOption_str .= "Single = $this->single \r\n";
        $ret_qOption_str .= "Shuffle Answers = $this->shuffleanswers \r\n";
        $ret_qOption_str .= "Correct Feedback = $this->correctfeedback \r\n";
        $ret_qOption_str .= "Partially Correct Feedback = $this->partiallycorrectfeedback \r\n";
        $ret_qOption_str .= "Incorrect Feedback = $this->incorrectfeedback \r\n";
        $ret_qOption_str .= "Answer Numbering = $this->answernumbering \r\n";
        
        $ret_qOption_str .= "Show Correct = $this->shownumcorrect \r\n";
        $ret_qOption_str .= "Answers =\r\n";
        foreach($this->answers as $ans){
            $ret_qOption_str .= "Answer($ans->id) = $ans \r\n";
        }
        $ret_qOption_str .= "Units =\r\n";
        foreach($this->units as $unit){
            $ret_qOption_str .= "$unit \r\n";
        }
        $ret_qOption_str .= "Unit Grading Type = $this->unitgradingtype \r\n";
        $ret_qOption_str .= "Unit Penalty = $this->unitpenalty \r\n";
        $ret_qOption_str .= "Units Left = $this->unitsleft \r\n";
        $ret_qOption_str .= "Show Units = $this->showunits \r\n";
        return $ret_qOption_str;
     
    }
}
