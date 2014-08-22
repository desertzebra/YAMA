<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author desertzebra
 */
class Model {
 protected $state;
 protected $lastUpdateTime;
 const STATE_DELETE = 2;
 const STATE_SAVE = 1;
 const STATE_UPDATE = 3;
 const STATE_LOAD = 0;
 
 public function __construct(){
     $this->state = self::STATE_LOAD;
     $this->lastUpdateTime = 0;
}
 protected function load(){
     $lastUpdateTime = time();
     $state = "updated";
//     echo $state + " " + $lastUpdateTime;
 }
protected function save(){
     $lastUpdateTime = time();
     $state = "save";
//     echo $state + " " + $lastUpdateTime;
}
protected function requiresLoad(){
    if(!isset($this->lastUpdateTime) || 
            $this->lastUpdateTime==0 || 
            time()-$this->lastUpdateTime<$CFG_YAMA->updateInterval){
  //      print("require load=true;".$this->lastUpdateTime."\r\n");
        return true;
    }
    else{
  //      print("require load=false;".$this->lastUpdateTime."||\r\n");
        return false;
    }
}
protected function requiresSave(){
    if($this->state === self::STATE_SAVE){
        return true;
    }
    else{
        return false;
    }
}
protected function requiresDelete(){
    if($this->state === self::STATE_DELETE){
        return true;
    }
    else{
        return false;
    }
}
protected function requiresUPDATE(){
    if($this->state === self::STATE_UPDATE){
        return true;
    }
    else{
        return false;
    }
}
public function getAllVars(){
       return get_object_vars($this);
    }
public static function loadclass($classname,$filename=""){
  if(class_exists($classname)){
    return true;
  }else{
    if($filename!=="" && file_exists($filename)){
      require_once("$filename");
      return true;
    }elseif(file_exists($classname."php")){
      require_once($classname."php");
      return true;
    }else{
      return false;
    }
  }
}
}
