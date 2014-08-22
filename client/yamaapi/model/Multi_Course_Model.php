<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Multi_Course_Model
 *
 * @author desertzebra
 */
require_once('config.php');
require_once($CFG_YAMA->moodledir . "/course/lib.php");
require_once "$CFG_YAMA->yamadir/model/Course_Model.php";

class Multi_Course_Model extends Model{
    private $courseList;
    
    function __construct() {
        $this->courseList = array();
    }
    
    function addCourse($course){
        $course_obj = new Course_Model();
        if(is_numeric($course)){
            $course_obj->loadById($course);
        }else if(is_object($course)){
            $course_obj->id = $course->id;
            $course_obj->load();
        }else{
            print "$course is not a valid argument\r\n";
        }
        array_push($this->courseList,$course_obj);
print "Course count=".count($this->courseList);
    }
    function removeCourse($course){
        if(is_numeric($course)){
            $key = find_course_by_id($course);
        }
        if(is_string($course)){
            $key = find_course_by_name($course);
        }
        $this->courseList[$key] = null;
    }
    function getCourse($course){
        if(is_numeric($course)){
            $key = find_course_by_id($course);
        }
        if(is_string($course)){
            $key = find_course_by_name($course);
        }
        return $this->courseList[$key];
    }
    function getAllCourses(){
        return $this->courseList;
    }
    function find_course_by_id($id){
        foreach($this->scourseList as $key=>$item){
            if($item===null){
                continue;
            }
            else if($item->id===$id){
                return $key;
            }
        }
    }
    function find_course_by_name($name){
        foreach($this->scourseList as $key=>$item){
            if($item===null){
                continue;
            }
            else if($item->name===$name){
                return $key;
            }
        }
    }
    function toJsonString() {
        $courseList_str = "{";
        foreach($this->courseList as $course){
            if($course!=null){
            $courseList_str .= $course->toJsonString().",";
            }
        }
	$courseList_str = rtrim($courseList_str,',');
        return $courseList_str."}";
    }
    function __toString() {
        $courseList_str = "";
        foreach($this->courseList as $course){
            if($course!=null){
            $courseList_str .= "\r\n$course\r\n";
            }
        }
        return $courseList_str;
    }
}
