<?php

/**
 *
 * @author desertzebra
 */
class Model {
 /*
  * State of this model. The state can be one of:
  * STATE_SAVE; ready to save
  * STATE_DELETE;marked as delete
  * STATE_UPDATE;needs updating
  * STATE_LOAD; stable state, with nothing to do.
  */
 protected $state;
 const STATE_DELETE = 2;
 const STATE_SAVE = 1;
 const STATE_UPDATE = 3;
 const STATE_STABLE = 0;
 const STATE_LOAD = 4;
 /*
  * Timestamp, since last state.
  */
 protected $lastUpdateTIme; 

 /*
  * Constructor, which sets the initial condition as load, so the model can be
  * loaded the first time.
  */
 public function __construct(){
     $this->state = self::STATE_LOAD;
     $this->lastUpdateTime = 0;
 }
 /*
  * Parent load method to update the lastUpdateTime and set the state to stable.
  * This should be called at end of the extended load method.
  */
 protected function load(){
     $lastUpdateTime = time();
     $state = self::STATE_STABLE;
//     echo $state + " " + $lastUpdateTime;
 }
 /*
  * Parent save method to update the lastUpdateTime and set the state to stable.
  * This should be called at end of the extended save method.
  */
protected function save(){
     $lastUpdateTime = time();
     $state = self::STATE_STABLE;
//     echo $state + " " + $lastUpdateTime;
}

 /*
  * Check if the lastUpdateTime is not 0 or greater than the updateInterval
  * defined in the config file.
  */
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
 /*
  * Check if the model requires saving.
  */ 
protected function requiresSave(){
    if($this->state === self::STATE_SAVE){
        return true;
    }
    else{
        return false;
    }
}
 /*
  * Check if the model requires deletion.
  */
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
 /*
  * Utility function to get all variables of this object.
  */
public function getAllVars(){
       return get_object_vars($this);
    }
 /*
  * Utility function to load a php class dynamically.
  */
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
