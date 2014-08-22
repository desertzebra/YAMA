<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Context_Model
 *
 * @author desertzebra
 */
class Context_Model extends Content_Model{
    
    public $parents;
    public $contextlevel;
    public $instanceid;
    public $path;
    public $depth;
    public $isCm;
    
    public function __construct($isCm) {
        $this->parents = array();
        $this->isCm = $isCm;
        parent::__construct();
    }
    
    public function getParents(context $MContextInstance){
        /*print_r($MContextInstance);
        $ret = $MContextInstance->get_parent_context();
        
        print ">>>>>".$ret."\r\n";
         * 
         */
        //$this->parents = array_values($MoodleContextInstance->get_parent_context(true));
        $mcontexts = new question_edit_contexts($MContextInstance);
        $this->parents = $mcontexts->all();
        //print "\r\nPARENTS\r\n";
        //print_r($this->parents);
    }
    public function loadById(){
        if($this->isCm){
            //print("Context module with id($this->id)");
            return context_module::instance($this->id);
        }else{
            //print("Context instance with id($this->id)");
            return context::instance_by_id($this->id);
        }
    }
    public function populate($context){
        if(isset($context->_id)){
            $this->id = $context->_id;
            //unset($context->id);
        }
        if(isset($context->_contextlevel)){
            $this->contextlevel = $context->_contextlevel;
            //unset($context->contextlevel);
        }
        if(isset($context->_instanceid)){
            $this->instanceid = $context->_instanceid;
            //unset($context->instanceid);
        }
        if(isset($context->_path)){
            $this->path = $context->_path;
            //unset($context->path);
        }
        if(isset($context->_depth)){
            $this->depth = $context->_depth;
            //unset($context->depth);
        }
    }
    public function load(){
        if($this->requiresSave()){
            $this->save();
        }
        if(!$this->requiresLoad()){
            return;
        }
        $this->state = self::STATE_LOAD;
        $moodleContextObj = $this->loadById();
        //print_r($moodleContextObj);
        if($moodleContextObj===null){
                return false;
        }
        $this->populate($moodleContextObj);
        //print_r($this);
        //print "Get Parents\r\n";
        $this->getParents($moodleContextObj);
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
    function toJsonString() {
        $ret_context_str = "";
        $ret_context_str .= "\"id\":\"$this->id\",";
        $ret_context_str .= "\"contextlevel\":\"$this->contextlevel\",";
        $ret_context_str .= "\"instanceid\":\"$this->instanceid\",";
        $ret_context_str .= "\"path\":\"$this->path\",";
        $ret_context_str .= "\"depth\":\"$this->depth\",";
        foreach($this->parents as $pcontext){
            $ret_context_str .= "\"parent\":\"$pcontext\",";
        }
	$ret_context_str = rtrim($ret_context_str,',');
        return $ret_context_str;

    }

    function __toString() {
        $ret_context_str = "\r\n". "Context=\r\n";
        $ret_context_str .= "Id = $this->id \r\n";
        $ret_context_str .= "Context Level = $this->contextlevel \r\n";
        $ret_context_str .= "Instance Id = $this->instanceid \r\n";        
        $ret_context_str .= "Path = $this->path \r\n";
        $ret_context_str .= "Depth = $this->depth \r\n";
        foreach($this->parents as $pcontext){
            $ret_context_str .= "Parent = $pcontext \r\n";
        }
        
        return $ret_context_str;
     
    }
}
