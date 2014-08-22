<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AnswerContent
 *
 * @author desertzebra
 */
require_once "config.php";
require_once "$CFG_YAMA->yamadir/model/Content_Model.php";
require_once "$CFG_YAMA->yamadir/model/content/quiz/Question_Category_Content.php";

class Answer_Content{
    public $id;
    public $question;
    public $text;
    public $format;
    public $fraction;
    public $feedback;
    public $feedbackformat;
    public $tolerance;
    public $tolerancetype;
    public $correctanswerlength;
    public $correctanswerformat;
    public function __construct(){
    
    }
    public function toMoodleObj(){
       $this->answer = $this->text;
       $this->answerformat = $this->format;
    }
   public function populate($answer){
       if(isset($answer->id)){
           $this->id = $answer->id;
           unset($answer->id);
       }
       if(isset($answer->question)){
           $this->question = $answer->question;
           unset($answer->question);
       }
       if(isset($answer->answer)){
           $this->text = $answer->answer;
           unset($answer->answer);
       }else if(isset($answer->text)){
           $this->text = $answer->text;
           unset($answer->text);
       }
       if(isset($answer->answerformat)){
           $this->format = $answer->answerformat;
           unset($answer->answerformat);
       }else if(isset($answer->format)){
           $this->format = $answer->format;
           unset($answer->format);
       }else{
           $this->format = FORMAT_PLAIN;
       }
       if(isset($answer->fraction)){
           $this->fraction = $answer->fraction;
           unset($answer->fraction);
       }else{
           $this->fraction = 1;
       }
       if(isset($answer->feedback)){
           $this->feedback = $answer->feedback;
           unset($answer->feedback);
       }
       if(isset($answer->feedbackformat)){
           $this->feedbackformat = $answer->feedbackformat;
           unset($answer->feedbackformat);
       }else{
           $this->feedbackformat = FORMAT_PLAIN;
       }
       if(isset($answer->tolerance)){
           $this->tolerance = $answer->tolerance;
           unset($answer->tolerance);
       }
       if(isset($answer->tolerancetype)){
           $this->tolerancetype = $answer->tolerancetype;
           unset($answer->tolerancetype);
       }
       if(isset($answer->correctanswerlength)){
           $this->correctanswerlength = $answer->correctanswerlength;
           unset($answer->correctanswerlength);
       }
       if(isset($answer->correctanswerformat)){
           $this->correctanswerformat = $answer->correctanswerformat;
           unset($answer->correctanswerformat);
       }else{
           $this->correctanswerformat = 1;
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
        
        $ret_qAns_str = "Answer($this->id)=\r\n";
        $ret_qAns_str .= "Question Id = $this->question \r\n";
        $ret_qAns_str .= "Answer = $this->text \r\n";
        $ret_qAns_str .= "Answer Format = $this->format \r\n";
        $ret_qAns_str .= "Fraction = $this->fraction \r\n";
        $ret_qAns_str .= "Feedback = $this->feedback \r\n";
        $ret_qAns_str .= "Tolerance = $this->tolerance \r\n";
        $ret_qAns_str .= "Tolerance Type = $this->tolerancetype \r\n";
        $ret_qAns_str .= "Correct Answer Length = $this->correctanswerlength \r\n";
        $ret_qAns_str .= "Correct Answer Format = $this->correctanswerformat \r\n";
        
        return $ret_qAns_str;
     
    }
}
