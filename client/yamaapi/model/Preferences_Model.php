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
class Preferences_Model extends Model {
    private $preferences;
    
    public function __construct(){
        $this->preferences = array();
    }
    public function get_preference_by_name($p_name){
        foreach($this->preferences as $name=>$value){
            if($name == $p_name){
                return array($name=>$value);
            }
        }
    }
    public function get_preferences(){
        return $this->preferences;
    }
    public function add_preference($p_name,$p_value){
        $this->preferences[$p_name] = $p_value;
    }
}
