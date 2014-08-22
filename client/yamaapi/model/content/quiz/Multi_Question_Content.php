<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Multi_Question_Content
 *
 * @author desertzebra
 */
require_once "config.php";
global $CFG;
require_once "$CFG_YAMA->yamadir/model/content/quiz/Question_Content.php";
require_once "$CFG_YAMA->moodledir/lib/questionlib.php";

class Multi_Question_Content extends Content_Model{
   private $question_objs;
   public $quiz_id;
   public $cat_ids;
   public $qorder_Ite;
   public function __construct($quiz_id=-1,$cat_ids=null){
       $this->question_objs = array();
       $this->quiz_id = $quiz_id;
       $this->cat_ids = $cat_ids;
       $this->qorder_Ite=0;
       parent::__construct();
    }
    public function clear(){
        $this->question_objs = array();
        $this->qorder_Ite = 0;
    }
    public function add($id=-1,$question=null,$qtype=""){
	global $CFG_YAMA;
        if(!isset($question) || $question === null){
	    if($qtype!=="" && Model::loadclass('Question_Content_'.$qtype,"$CFG_YAMA->yamadir/model/content/quiz/Question_Content_$qtype.php")){
              print "<p>file found Question_Content_".$qtype."</p>";
              $questionClass = "Question_Content_".$qtype;
              $question = new $questionClass();
            }else{ 
              $question = new Question_Content();
            }
            $question->id = $id;
            $question->qorder = $this->qorder_Ite;
        }
        $this->question_objs[$this->qorder_Ite++]= $question;
    }
    public function removeByQId($id){
        if($id<0){
            return false;
        }
        $key = $this->getOrderByQuestionId($id);
        print_notice("removing question($id) at pos=$key\r\n");
        unset($this->question_objs[$key]);
    }
    public function removeByPos($pos){
        if($pos<0){
            return false;
        }
        print_notice("removing question(".$this->question_objs[$pos]->id.") at pos=$pos\r\n");
        unset($this->question_objs[$pos]);
    }
    public function getQuestions(){
        return $this->question_objs;
    }
    public function delQuestion($question){
        return $question->id;
    }
    public function getQuestionsToDelete(){
        $delQuesList = array();
        foreach($this->question_objs as $question){
            if(!empty($question->id) && $question->state===STATE_DELETE){
                array_push($delQuesList,$question);
            }
        }
        return $delQuesList;
    }
    public function getQuestionIds(){
        $qIds= array();
        foreach($this->question_objs as $quest){
            if($quest->id<0){
                continue;
            }
                array_push($qIds, $quest->id);
        }
        return $qIds;
    }
    public function saveQuestions(){
        $qIds= array();
        foreach($this->question_objs as $quest){
            if(!isset($quest->id) || $quest->id<0){
                print_r($quest);
                $errors = $quest->save();
		if(!empty($errors)){
		  print "<p>Unable to save the question</p>";
		  print_r($errors);
		  continue;
		}
                array_push($qIds, $quest->id);
            }
        }
        return $qIds;
    }
    public function saveQuestion($question){
        if(!isset($question->id) || $question->id<0){
                //print_r($quest);
                $errors = $question->save();
		if(!empty($errors)){
                  print "Unable to save the question";
                  print_r($errors);
                  return -1;
                }

                return $question->id;
            }
    }
    public function load(){
        //print_r($this);
        if($this->quiz_id!==-1){
            $questions = $this->loadById();
        }else{
            $questions = $this->loadById(null, false);
        }
        if(empty($questions)){
/*            print( "No Questions Found for ".
                   ($this->quiz_id===-1)?"Cat=". implode(',',$this->cat_ids):"quiz_id=".$this->quiz_id);
  */         
            
           return false; 
        }
        //print_r($questions);
        foreach($questions as $quest){
            //print("\r\nIDDDDDD=".$quest->id."\r\n\r\n");
            $this->addQuestion($quest);
        }
        
    }
    public function loadById($questionIds=null, $isQuiz=true){
        global $DB;
        if($isQuiz){
            if($questionIds === null){
            $questionIds = $this->getQuestionIds();
            }
            //print_r($questionIds);
            $questions = question_preload_questions($questionIds,
                'qqi.grade AS maxmark, qqi.id AS instance',
                "{quiz_question_instances} qqi ON qqi.quiz = $this->quiz_id AND q.id = qqi.question");
           //print_r($questions);
        }else{
            if($this->cat_ids===null){
                print_error("Category Ids were null");
            }
            //print  "\r\nCat_ids=\r\n";
            //print_r($this->cat_ids);
            $questions = $DB->get_records_list('question', 'category', $this->cat_ids,
                '', 'id, category, qtype, name, questiontext, questiontextformat');
        }
        //print_r($questions);
        if (!get_question_options($questions)) {
            print_error('Could not load the question options');
        }
        
        return $questions;
    }
    public function setOptions($qorder, $options){
        if($qorder<0 || $qorder>=$this->qorder_Ite){
            print_error("Incorrect Iterator($qorder)");
            return -1;
        }else{
            $question = new stdClass();
            $question->options = $options;
            $question->options->question = $this->question_objs[$qorder]->id;
             //print_r($options);
            $this->question_objs[$qorder]->populate($question);
            //print "Question Object after setting Options\r\n";
            //print_r($this->question_objs[$qorder]);
            return $qorder;
        }
    }
    public function addQuestion($quest){
	global $CFG_YAMA;
        $quest = (object) $quest;
        //print_r($this->question_objs);
//        print "<p>quest->id=".$quest->id."</p>\r\n";
        if(empty($quest->id) || $quest->id===0){
	    if(Model::loadclass('Question_Content_'.$quest->qtype,"$CFG_YAMA->yamadir/model/content/quiz/Question_Content_$quest->qtype.php")){
              print "<p>file found Question_Content_".$quest->qtype."</p>";
              $questionClass = "Question_Content_".$quest->qtype;
              $question_obj = new $questionClass();
            }else{ 
              $question_obj = new Question_Content();
            }
            $question_obj->populate($quest);
            $question_obj->state = MODEL::STATE_SAVE;
            if(!empty($question_obj->qorder)){
              $first_array = array_splice ($this->question_objs, 0, $question_obj->qorder);
              $this->question_objs = array_merge ($first_array, $question_obj, $this->question_objs); 
              $this->qorder_Ite++;
print "<p>Added new element at $question_obj->qorder. Array length $this->qorder_Ite.</p>";
              return $question_obj->qorder;
            }else{
            $question_obj->qorder = $this->qorder_Ite;
            $this->question_objs[$this->qorder_Ite]= $question_obj;
            }
            return $this->qorder_Ite++;
        }else{
            $pos= $this->getOrderByQuestionId($quest->id);
            //print "pos=$pos\r\n";
            if($pos===-1){
	    if(isset($quest->qtype) && Model::loadclass('Question_Content_'.$quest->qtype,"$CFG_YAMA->yamadir/model/content/quiz/Question_Content_$quest->qtype.php")){
              print "<p>file found Question_Content_".$quest->qtype."</p>";
              $questionClass = "Question_Content_".$quest->qtype;
              $question_obj = new $questionClass();
	    }else{ 
              $question_obj = new Question_Content();
            }
            //print "mini AGAIN=".$question_obj->category->miniLoad."\r\n";
            $question_obj->populate($quest);
            $question_obj->state = MODEL::STATE_LOAD;
            $question_obj->qorder = $this->qorder_Ite;
            //print_r($question_obj);
            $this->question_objs[$this->qorder_Ite]= $question_obj;
            return $this->qorder_Ite++;
            }else{
                $this->question_objs[$pos]->populate($quest);
                $question_obj->state = MODEL::STATE_LOAD;
                return $pos;
            }
        }
    }
    public function getIdsInStr(){
        $ids = $this->getQuestionIds();
        $retStr = implode(",",$ids);
        //print("return question string=".$retStr."\r\n");
        return $retStr;
    }
    public function save(){
        //print "Saving Questions \r\n";
        return implode(",",$this->saveQuestions());
    }
    public function getOrderByQuestionId($id){
        //print "in getOrderByQuestionId($id)\r\n";
        //print_r($this->question_objs);
        foreach($this->question_objs as $key=>$quest){
            //print "id check=$quest->id vs $id at $key \r\n";
            if($quest->id===$id){
                return $key;
            }
        }
        return -1;
    }
    public function __toString() {
       $ret_quiz_str = "".parent::__toString()."";
        foreach($this->question_objs as $quest){
            $ret_quiz_str .= "\tQuestion = $quest \r\n";
        }
        return $ret_quiz_str;
    }

}
