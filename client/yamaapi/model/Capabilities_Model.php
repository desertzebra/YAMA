<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Preferences
 *
 * @author desertzebra
 */
class Capabilities_Model extends Model {
    private $capabilities;
    
    public function __construct(){
        $this->capabilities = array();
    }
    public function get_capability_by_name($p_name){
        foreach($this->capabilities as $name=>$value){
            if($name == $p_name){
                return array($name=>$value);
            }
        }
    }
    public function get_capabilities(){
        return $this->capabilities;
    }
    public function add_capability($p_name,$p_value){
        $this->capabilities[$p_name] = $p_value;
    }
}
