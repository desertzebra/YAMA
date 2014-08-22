<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Quiz_Content
 *
 * @author desertzebra
 */
require_once "config.php";
require_once "$CFG_YAMA->yamadir/model/Content_Model.php";
require_once "$CFG_YAMA->moodledir/lib/questionlib.php";

require_once "$CFG_YAMA->yamadir/model/content/quiz/Multi_Question_Content.php";


class Question_Category_Content extends Content_Model {
    public $contextid;
    public $questions;
    public $subcategories;
    public $stamp;
    public $sortorder;
    protected $miniLoad;
        function __construct($minL=0) {
        $this->content_type = "question_category";
        $this->miniLoad = $minL;
        if($this->miniLoad!==0){
        $this->subcategories = array();
        $this->questions = new Multi_Question_Content(-1,array());
        }
        parent::__construct();
    }

    function populate($cat) {
        if(isset($cat->info)){
            $this->text = $cat->info;
            unset($cat->info);
        }
        if(isset($cat->infoformat)){
            $this->format = $cat->infoformat;
            unset($cat->infoformat);
        }else{
            $this->format = 0;
        }
        if(isset($cat->stamp)){
            $this->stamp = $cat->stamp;
            unset($cat->stamp);
        }
        if(isset($cat->sortorder)){
            $this->sortorder = $cat->sortorder;
            unset($cat->sortorder);
        }
        if(isset($cat->contextid)){
            $this->contextid = $cat->contextid;
            unset($cat->contextid);
        }
        parent::populate($cat);
        
    }
    
    function save(){
    }
    public function load(){
        global $DB;
        if($this->requiresSave()){
            $this->save();
        }
        if(!$this->requiresLoad()){
            return;
        }
        $this->state = self::STATE_LOAD;
        if(!$category = $DB->get_record('question_categories', array('id'=>$this->id))){
            print_error("Unable to fetch Category($this->id)");
        }
        $this->populate($category);
        if($this->miniLoad!==0){
            $this->loadSubCat();
            $this->loadQuestions();
        }
        parent::load();
    }
    public function loadQuestions(){
        global $DB;
        if(!isset($this->id)){
            print_error("Id($this->id) not set");
        }else{
            //print "\r\nCat($this->id) load Questions\r\n";
            $this->questions->cat_ids = $this->getCatIds();
            $this->questions->load();
        }
    }
    public function getQuestions(){
        return $this->questions->getQuestions();
    }
    public function getCatIds(){
        $catids= array();
        array_push($catids, $this->id);
        foreach($this->subcategories as $subcat){
            array_push($catids, $subcat->id);
        }
        return $catids;
    }
    public function loadSubCat(){
        if(!isset($this->id)){
            return false;
        }else{
            $subcatsId = question_categorylist($this->id);
            foreach($subcatsId as $id){
                if($id===$this->id){
                    continue;
                }
                $subcat = new Question_Category_Content(false);
                $subcat->id = $id;
                $subcat->load();
                array_push($this->subcategories, $subcat);
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
        $ret_cat_str = "***************************\r\n"
        . "Category=".parent::__toString(). "\r\n";
        $ret_cat_str .= "Context id=$this->contextid\r\n";
        $ret_cat_str .= "Stamp=$this->stamp\r\n";
        $ret_cat_str .= "Sort Order=$this->sortorder\r\n";
        if($this->miniLoad!==0){
            foreach($this->subcategories as $subcat){
                $ret_cat_str .= "subcat($subcat->id)= $subcat \r\n";
            }
            $ret_cat_str .= "Questions: $this->questions";
        }
        $ret_cat_str = "--------------------------------\r\n";
        return $ret_cat_str;
     
    }

}
