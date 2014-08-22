<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author desertzebra
 */

require_once 'config.php';
require_once "$CFG_YAMA->yamadir/model/User_Model.php";

class User {
    private $user;
    function __construct($mini=false) {
        $this->user = new User_Model($mini);
    }
    function getContacts(){
        return $this->user->getContacts();
    }
    function addContact($userId){
        return $this->user->addContact($userId);
    }
    function removeContact($userId){
        return $this->user->removeContact($userId);
    }
    function getUserAttribute($attribute_name){
        return $this->user->$attribute_name;
    }
    function setUserAttribute($attribute_name,$attribute_value){
        $this->user->$attribute_name = $attribute_value;
    }
    function getMessages($userTo=null){
        return $this->user->getMessages($userTo);
    }
    function getCourses(){
        return $this->user->getCourses();
    }
    function getUser(){
        return $this->user;
    }
    function makeActive(){
        $this->user->makeActive();
    }
    function login($username, $password){
        $myUsername = $this->user->login($password);
        if($myUsername!=$username){
            return false;
        }
    }
    function logout(){
        $this->user->logout();
    }
    function __toString(){
        return "".$this->user;
    }
    function load(){
        $this->user->load();
    }
    function loadUserById($userId){
        return $this->user->loadById($userId);
    }
    function save(){
        $this->user->save();
    }

    public function sendMessage($userid, $message) {
        $this->user->sendMessage($userid,$message);
    }

    public function objectToArray($el){
        if (is_object($el)) {
		// Gets the properties of the object
		$el = $el->getAllVars();
	}

	if (is_array($el)) {
		return array_map(array($this, 'objectToArray'), $el);
	} else {
		// Return array
		return $el;
	}
    }
    public function  toJsonString(){
        //$my_json = json_encode($this->objectToArray($this->user));
        //$my_json = json_encode($this->objectToArray($this->user));
        $my_json = $this->user->toJsonString();
        return $my_json;
    }
}
