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

class Question_Content_calculated extends Question_Content{

   public function __construct(){
    parent::__construct();
   }
   public function populate($question){
       parent::populate($question);
        
   }
   public function toMoodleObj(){
   	parent::toMoodleObj();  
   }
   public function validate($data=null){
        $errors = parent::validate($data);
	if($data===null){
	   $data = $this;
	}
	
	$qtext = "";
	$questionText = $data->text;
	$possibledatasets_q = $this->qtypeobj->find_dataset_names($questionText);	
	foreach ($possibledatasets as $name => $value) {
            $questionText = str_replace('{'.$name.'}', '1', $questionText);
        }
        while (preg_match('~\{=([^[:space:]}]*)}~', $questionText, $regs1)) {
            $qtextsplits = explode($regs1[0], $questionText, 2);
            $qtext = $qtext.$qtextsplits[0];
            $questionText = $qtextsplits[1];
            if (!empty($regs1[1]) && $formulaerrors =
                    qtype_calculated_find_formula_errors($regs1[1])) {
                if (!isset($errors['questiontext'])) {
                    $errors['questiontext'] = $formulaerrors.':'.$regs1[1];
                } else {
                    $errors['questiontext'] .= '<br/>'.$formulaerrors.':'.$regs1[1];
                }
            }
        }
	// Check that the answers use datasets.
        $answers = $data->options->answers;
        $mandatorydatasets = array();
        foreach ($answers as $key => $answer) {
            $mandatorydatasets += $this->qtypeobj->find_dataset_names($answer->text);
        }
        if (empty($mandatorydatasets)) {
            foreach ($answers as $key => $answer) {
                $errors['answeroptions['.$key.']'] =
                        get_string('atleastonewildcard', 'qtype_calculated');
            }
        }
	
	// Validate the answer format.
        foreach ($answers as $key => $answer) {
            $trimmedanswer = trim($answer->text);
            if ($trimmedanswer) {
                if ($answer->correctanswerformat == 2 &&
                        $answer->correctanswerlength == '0') {
                    $errors['answerdisplay['.$key.']'] =
                            get_string('zerosignificantfiguresnotallowed', 'qtype_calculated');
                }
            }
        }

        return $errors;



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
print "<p>Calling Validate</p>";
print_r($errors);
       $errors = $this->validate();
       if(!empty($errors)){
           print "<p>Found errors, while saving question</p>\r\n";
           return $errors;
       }
       if(!empty($this->qtypeobj)){
           print "<p>Saving question calculated</p>\r\n";
           //$retStat = $this->qtypeobj->save_question($this,$this->form);
           //print_r($retStat);
          return 1;//$retStat; 
       }
       
   }

}
