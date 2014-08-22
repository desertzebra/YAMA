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
global $CFG;
require_once "$CFG_YAMA->yamadir/model/content/quiz/Question_Category_Content.php";
require_once "$CFG_YAMA->moodledir/lib/questionlib.php";

class Multi_Question_Category_Content extends Content_Model{
   public $contexts;
   public $categories;
   
   public function __construct(){
       $this->contexts = array();
       $this->categories = array();
       parent::__construct();
    }
    public function addCategory($category){
        $question_cat = new Question_Category_Content(1);
        $question_cat->populate($category);
        $question_cat->load();
        
        $this->categories[$question_cat->contextid]= $question_cat;
    }
    public function addContext($context_obj){
        $inArrId = array();
        //array_push($this->contexts, $context_obj);
        foreach($context_obj->parents as $context){
            if(array_search($context->id, $inArrId)){
                //print("\r\n$context->id found in array:");
                //print_r($inArrId);
                continue;
            }
            array_push($this->contexts, $context);
            array_push($inArrId,$context->id);
        }
        //print_r($this->contexts);
    }
    function getCategories(){
        return $this->categories;
    }
    function getContexts(){
        return $this->contexts;
    }
    public function load(){
        //print_r($this);
        if(count($this->contexts)<1){
            print_error("No contexts defined");
        }
            $pcontexts = array();
        foreach ($this->contexts as $context) {
            $pcontexts[] = $context->id;
        }
        $contextslist = join($pcontexts, ', ');
        //print "contexts " . $contextslist."\r\n";
        $categories = get_categories_for_contexts($contextslist);
            
        //$categories = question_add_tops($categories, $pcontexts);

        if(empty($categories)){
            //print_error("No Categories Found");
           return false; 
        }
        //print_r($categories);
        foreach($categories as $cat){
            $this->addCategory($cat);
        }
        parent::load();
        
    }
    
    public function __toString() {
       $ret_cat_str = "".parent::__toString()."";
        foreach($this->categories as $cat){
            $ret_cat_str .= "\tCategory($cat->id) = $cat \r\n";
        }
        return $ret_cat_str;
    }

}
