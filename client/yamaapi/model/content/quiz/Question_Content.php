<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Question_Content
 *
 * @author desertzebra
 */
require_once "config.php";
global $CFG_YAMA;
require_once "$CFG_YAMA->yamadir/model/Content_Model.php";
require_once "$CFG_YAMA->yamadir/model/content/quiz/Question_Category_Content.php";
require_once "$CFG_YAMA->yamadir/model/content/quiz/Question_Options.php";

class Question_Content extends Content_Model{
   public $categoryObj;
   public $parent;
   public $generalfeedback;
   public $generalfeedbackformat;
   public $defaultmark;
   public $penalty;
   public $qtype;
   public $qtypeobj;
   public $length;
   public $stamp;
   public $version;
   public $hidden;
   public $maxmark;
   public $instance;
   public $contextid;
   public $options;
   public $hints;
   public $qorder;
  

   public function __construct(){
    $this->options = new Question_Options();
    $this->hints = array();
    $this->categoryObj = new Question_Category_Content(0);
    
    parent::__construct();
   }
   public function populate($question){
       if(isset($question->id) && $question->id>-1){
           $this->id = $question->id;
       } 
       if(!empty($question->category)){
           if(is_numeric($question->category)){
               //print_r($this->category);
               $this->categoryObj->id = $question->category;
               $this->categoryObj->miniLoad = 0;
               $this->categoryObj->load();
           }else{
               $this->categoryObj = $question->category;
           }
           unset($question->category);
       }else{
           if(!isset($this->categoryObj)){
                print("<p>no question category($question->category) defined</p>");
           }
       }
       if(isset($question->qtype)){
           $this->qtype = $question->qtype;
           unset($question->qtype);
       }elseif(isset($question->type)){
           $this->qtype = $question->type;
           unset($question->type);
       }else{
           if(!isset($this->qtype)){
           print_error("No question type($this->qtype), has been set.");
           }
       }

	$this->qtypeobj = question_bank::get_qtype($this->qtype);

       if(isset($question->parent)){
           $this->parent = $question->parent;
           unset($question->parent);
       }
       if(isset($question->questiontext)){
           $this->text = $question->questiontext;
           unset($question->questiontext);
       }
       if(isset($question->questiontextformat)){
           $this->format = $question->questiontextformat;
           unset($question->questiontextformat);
       }
       if(isset($question->generalfeedback)){
           $this->generalfeedback = $question->generalfeedback;
           unset($question->generalfeedback);
       }
       if(isset($question->generalfeedbackformat)){
           $this->generalfeedbackformat = $question->generalfeedbackformat;
           unset($question->generalfeedbackformat);
       }
       if(isset($question->defaultmark) && !empty($question->defaultmark)){
           $this->defaultmark = $question->defaultmark;
           unset($question->defaultmark);
       }else{
           $this->defaultmark = 1;
       }
       if(isset($question->penalty) && !empty($question->penalty)){
           $this->penalty = $question->penalty;
           unset($question->penalty);
       }else{
           $this->penalty = 0;
       }
       if(isset($question->maxmark) && !empty($question->maxmark)){
           $this->maxmark = $question->maxmark;
           unset($question->maxmark);
       }else{
           $this->maxmark = 1;
       }
       if(isset($question->length)){
           $this->length = $question->length;
           unset($question->length);
       }
       if(isset($question->stamp)){
           $this->stamp = $question->stamp;
           unset($question->stamp);
       }
       if(isset($question->version)){
           $this->version = $question->version;
           unset($question->version);
       }else{
           $this->version = 0;
       }
       if(isset($question->hidden)){
           $this->hidden = $question->hidden;
           $this->isVisible = !$question->hidden;
           unset($question->hidden);
       }else{
           $this->hidden = 0;
           $this->isVisible = 1;
       }
       if(isset($question->contextid)){
           $this->contextid = $question->contextid;
           unset($question->contextid);
       }
       if(isset($question->instance)){
           $this->instance = $question->instance;
           unset($question->instance);
       }
       //print "\r\n>>>Adding options\r\n";
       if(isset($question->options)){
           $question->options = (object)$question->options;
           $this->options->populate($question->options);
       }
       //print "\r\n>>>Done\r\n";
       if(isset($question->hints)){
           $this->hints = $question->hints;
           unset($question->hints);
       }
       if(isset($question->modifiedby)){
           $this->modifiedby = $question->modifiedby;
       }
       if(isset($question->timemodified)){
           $this->timemodified = $question->timemodified;
       }
       if(isset($question->createdby)){
           $this->createdby = $question->createdby;
       }
       if(isset($question->timecreated)){
           $this->timecreated = $question->timecreated;
       }
       parent::populate($question);
        
   }
   public function toMoodleObj(){
       if($this->id===0){
           print "<p>Page Break, not converting to Moodle Object</p>";
           return;
       }
       if(!isset($this->qtype)){
           print_r($this);
           print_error("qtype($this->qtype,$this->id) not set");
       }
       $this->questiontext = $this->text;
       $this->questiontextformat = $this->format;
       $this->category = $this->categoryObj->id;
       if(!question_bank::qtype_enabled($this->qtype)){
           //if($this->qtype==='new_page'){
             return;
           //}else{
           //print_error("qtype($this->qtype) not allowed");
           //}
       }
        $this->options->toMoodleObj();
        $this->form = new stdClass();
        $this->form->questiontext['text'] = $this->text;
        $this->form->questiontext['format'] = $this->format;
        $this->form->generalfeedback['text'] = $this->generalfeedback;
        $this->form->generalfeedback['format'] = $this->generalfeedbackformat;
        $this->form->name = $this->name;
        $this->form->parent= $this->parent;
        $this->form->penalty = $this->penalty;
        $this->form->defaultmark = $this->defaultmark;
        $this->form->category = $this->categoryObj->id.",".$this->categoryObj->contextid;
        $categorycontext = context::instance_by_id($this->categoryObj->contextid);
        $addpermission = has_capability('moodle/question:add', $categorycontext);
        if(!isset($this->hidden)){
            // Checking isVisible field and reversing its value
            $this->hidden = (empty($this->isVisible)||!$this->isVisible)?0:1;
        }
        
        
        $option_arr = (array)$this->options;
        foreach ($option_arr as $optKey=>$optVal){
            //print "$optKey\r\n";
            if($optKey==='answers'){
               
               //$allAnsArray = array();
               foreach($optVal as $key=>$ans){
                   //print "!!!$ans\r\n";
                   //$ans = array($ans);
                   $this->form->answers[$key] = $ans->text;
                   //$allAnsArray[$key]['format'] = $ans->format;
                   $this->form->fraction[$key] = $ans->fraction;
                   $this->form->feedback[$key]['text'] = $ans->feedback;
                   $this->form->feedback[$key]['format'] = $ans->feedbackformat;
                   $this->form->tolerance[$key] = $ans->tolerance;
                     $this->form->tolerancetype[$key] = $ans->tolerancetype;
                   $this->form->correctanswerlength[$key] = $ans->correctanswerlength;
                   $this->form->correctanswerformat[$key] = $ans->correctanswerformat;
               }
               //print_r($allAnsArray); 
               //$this->form->$optKey = $allAnsArray;
            }else{
                $this->form->$optKey = $optVal;
            }
        }
        
   }
   public function validate($data=null){
       if($data===null){
           $data=$this;
       }
       $errors = array();
       if($data->text==""){
         $errors['text'] = '<p>Question Text cannot be empty</p>';
       }
       if($data->name==""){
         $errors['name'] = '<p>Question Name cannot be empty</p>';
       }
       return $errors;
   }
   public function getNumberingStyles(){
       if(!isset($this->qtypeobj)){
           $this->qtypeobj = question_bank::get_qtype($this->qtype);
       }
       if(method_exists($this->qtypeobj, 'get_numbering_styles')){
           return $this->qtypeobj->get_numbering_styles();
       }else{
           return question_bank::get_qtype('multi-choice')->get_numbering_styles();
       }
   }
   public function save(){
       global $USER;
       /*if(!$this->requiresSave()){
            return false;
       }*/
       //print_r($this);
       if(!isset($USER->id)){
           print_error("No active user");
       }
print "<p>id=".$this->id."</p>";
       if($this->id===0){
           print "<p>No save required for new page</p>";
           return;
       }
       $this->toMoodleObj();
print "<p>Validating this question</p>";
       $errors = $this->validate();
       if(!empty($errors)){
           return $errors;
       }
       if(!empty($this->qtypeobj)){
           //print "<p>Saving question</p>\r\n";
           $retStat = $this->qtypeobj->save_question($this,$this->form);
           //print_r($retStat);
           return $retStat;
       }
       
   }
   function load(){
        global $DB;
        if($this->requiresSave()){
            $this->save();
        }
        if(!$this->requiresLoad()){
            return;
        }
        $this->state = self::STATE_LOAD;
        
        $this->populate($quiz_raw);
        
        parent::load();
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
        $ret_question_str = "***************************\r\n"
        . "Question($this->id,$this->qorder)=\r\n".parent::__toString(). "\r\n";
        $ret_question_str .= "---------------------------\r\n";
        $ret_question_str .= "Category = $this->categoryObj \r\n";
        $ret_question_str .= "Parent = $this->parent \r\n";
        $ret_question_str .= "General Feedback = $this->generalfeedback \r\n";
        $ret_question_str .= "General Feedback Format = $this->generalfeedbackformat \r\n";
        $ret_question_str .= "Default Mark = $this->defaultmark \r\n";
        $ret_question_str .= "Max Mark = $this->maxmark \r\n";
        $ret_question_str .= "Penalty = $this->penalty \r\n";
        $ret_question_str .= "Question Type = $this->qtype \r\n";
        $ret_question_str .= "Length = $this->length \r\n";
        $ret_question_str .= "Stamp = $this->stamp \r\n";
        $ret_question_str .= "Version = $this->version \r\n";
        $ret_question_str .= "Hidden = $this->hidden \r\n";
        $ret_question_str .= "Context id = $this->contextid \r\n";
        $ret_question_str .= "Instance = $this->instance \r\n";
        $ret_question_str .= "---------------------------\r\n"
        . "\tOptions=".$this->options. "\r\n";
        $ret_question_str = "***************************\r\n";
        return $ret_question_str;
     
    }
}
