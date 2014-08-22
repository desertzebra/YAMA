<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Course
 *
 * @author desertzebra
 */
require_once 'config.php';
require_once "$CFG_YAMA->yamadir/model/Course_Model.php";

class Course {
    private $course;
    function __construct($mini=false) {
        $this->course = new Course_Model($mini);
    }
    function getCourseAttribute($attribute){
        return $this->course->$attribute;
    }
    function setCourseAttribute($attr_name,$attr_value){
        $this->course->$attr_name = $attr_value;
    }
    function __toString() {
        return $this->course."";
    }
    function load($course){
        $this->course->populate($course);
        $this->course->load();
    }
    function save(){
        $this->course->save();
    }
    function getContents(){
        return $this->course->getContentByType();
    }
    function getContentByType($type){
        return $this->course->getContentByType($type);
    }
}
