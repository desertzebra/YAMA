<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Course_Model
 *
 * @author desertzebra
 */
require_once('config.php');
require_once($CFG_YAMA->moodledir."/course/lib.php");
require_once "$CFG_YAMA->yamadir/model/Model.php";
class Course_Model extends Model{
    
    private $id;
    private $categoryid;
    private $name;
    private $shortname;
    private $summary;
    private $format;
    private $participants;
    private $managers;
    private $contents;
    private $notifications;
    private $startTime;
    private $endTime;
    private $timecreated;
    private $timemodified;
    private $lang;
    private $miniLoad;
    private $sortField;

    function __construct($miniLoad=false) {
        $this->participants = array();
        $this->managers = array();
        $this->contents = array();
        $this->notifications = array();
        $this->miniLoad = $miniLoad;
        
    }
    function cmp($a, $b)
    {
        $sortF = $this->sortField;
                return $a->$sortF < $b->$sortF;
    }

    private function sortContents($sortField){
	$this->sortField = $sortField;
    	usort($this->contents, 'cmp');
    }
    function getContentByType($type="", $sortField='section_num'){
        if(!isset($this->contents) || count($this->contents)<1){
            return null;
        }
        if(isset($sortField) && !$sortField==='' && in_array($sortField,get_object_vars($this))){
  	    $this->sortContents($sortField);
	}
        if(empty($type)){
            return $this->contents;
        }else{
        $sub_list = array();
        foreach($this->contents as $content){
            if(!is_object($content)){
                continue;
            }
            //print "type=".$content->content_type."\r\n";
            if($content->content_type===$type){
                array_push($sub_list,$content);
            }
        }
//print_r($sub_list);
        return $sub_list;
        }
    }
    function populate($course) {
        if(is_numeric($course)){
            $this->id = $course;
            return;
        }elseif(is_array($course)){
            $course = (object)$course;
        }
        
        if(isset($course->id)){
            $this->id = $course->id;
        }elseif(!isset($this->id)){
            print_error("Course ID($this->id) not set\r\n");
        }
        
        if(isset($course->fullname)){
            $this->name = $course->fullname;
        }else if(isset($course->name)){
            $this->name = $course->name;
        }
        if(isset($course->shortname)){
            $this->shortname = $course->shortname;
        }
        if($this->miniLoad){
            return;
        }else{
            if(isset($course->summary)){
                $this->summary = $course->summary;
            }
            if(isset($course->category)){
                $this->categoryid = $course->category;
            }
            if(isset($course->format)){
                $this->format = $course->format;
            }
            if(isset($course->startdate)){
                $this->startTime = $course->startdate;
            }
            if(isset($course->enddate)){
                $this->endTime = $course->enddate;
            }
            if(isset($course->timecreated)){
                $this->timecreated = $course->timecreated;
            }
            if(isset($course->timemodified)){
                $this->timemodified = $course->timemodified;
            }
            if(isset($course->lang)){
                $this->lang = $course->lang;
            }
            
        }
     
    }
    function loadDetails(){
        global $DB;
        if($this->miniLoad){
            return;
        }
        if (! $course = $DB->get_record("course", array("id" => $this->id))) {
            print_error('coursemisconf');
        }
        $this->populate($course);
    }
    function loadById($id){
        $this->id = $id;
        if(requiresUpdate()){
            $this->load();
        }
    }
    function load(){
        if($this->requiresSave()){
            $this->save();
        }
        if(!$this->requiresLoad()){
            return;
        }
        $this->state = self::STATE_LOAD;
        //print("\r\nLoading Course Details\r\n");
        $this->loadDetails();
        //print("\r\nLoading Course Activities\r\n");
        $this->loadActivities();
        parent::load();
    }
    public function save(){
        
        foreach($this->contents as $cont){
            if(is_a($cont, "Quiz_Content")){
                print "\r\nSaving $cont\r\n";
                $cont->save();
            }
        }
        
        parent::save();
    }
    function loadActivities(){
        $activities = get_array_of_activities($this->id);
        //print_r($activities);
        foreach($activities as $act_item){
            $activity_obj = $this->parse_obj($act_item);
            array_push($this->contents,$activity_obj);
        }
    }
    function loadActivityByType($type){
        if(!$type_arr = get_all_instances_in_course($type,$this)){
            print "No activities of $type found in course($this->id)";
            return false;
        }
        foreach($type_arr as $type_el){
            $type_obj = $this->parse_obj($type_el);
            array_push($this->contents, $type_obj);
        }
    }
    function parse_obj($activity){
        global $CFG_YAMA;
        $mod = $activity->mod;
        //print "mod=".$mod."\r\n";
        $content_obj = null;
        if(!empty($CFG_YAMA->$mod)){
//print_r($activity);
            $mod_path = $CFG_YAMA->$mod->path;
            $mod_class = $CFG_YAMA->$mod->cn;
            require_once "$mod_path";
            $content_obj = new $mod_class();
            //print "calling $content_obj populate\r\n";
            $content_obj->populate($activity);
            //print "calling $mod load\r\n";
            $content_obj->load();
        }else{
            //$content_obj = new Content_Model();
            //$content_obj->populate($activity);
        }
        return $content_obj;
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
        $course_str =  "\"id\":\"".$this->id . "\"," .
                        "\"name\":\"".$this->name . "\",".
                        "\"shortname\":\"".$this->shortname."\",";
        if(!$this->miniLoad){
            $course_str .= "\"summary\":\"".$this->summary ."\","
                    ."\"categoryid\":\"".$this->categoryid ."\","
                    ."\"format\":\"".$this->format ."\","
                    ."\"startdate\":\"".$this->startTime ."\","
                    ."\"enddate\":\"".$this->endTime ."\","
                    ."\"timecreated\":\"".$this->timecreated ."\","
                    ."\"timemodified\":\"".$this->timemodified ."\","
                    ."\"lang\":\"".$this->lang ."\",";
        $course_str .= "\"participants\":{";
        foreach($this->participants as $user){
            $course_str .= $user->toJsonString().",";
        }
        $course_str = rtrim($course_str,',');
        $course_str = "}";
        $course_str .= "\"managers\":{";

        foreach($this->managers as $user){
            $course_str .= $user->toJsonString().",";
        }
	$course_str = rtrim($course_str,',');
        $course_str = "}";
        foreach($this->contents as $content){
            //$course_str .= $content."\r\n";
        }
        $course_str .= "\"notifications\":{";
        foreach($this->notifications as $notice){
            $course_str .= $notice->toJsonString().",";
        }
	$course_str = rtrim($course_str,',');
        $course_str = "}";
        //print "------------------------";
        }
        return $course_str;
    }

    function __toString() {
        $course_str =  "\r\n"."id=".$this->id . "\r\n" .
                        "name=".$this->name . "\r\n".
                        "shortname=".$this->shortname."\r\n";
        if(!$this->miniLoad){
            $course_str .= "summary=".$this->summary ."\r\n"
                    ."categoryid=".$this->categoryid ."\r\n"
                    ."format=".$this->format ."\r\n"
                    ."startdate=".$this->startTime ."\r\n"
                    ."enddate=".$this->endTime ."\r\n"
                    ."timecreated=".$this->timecreated ."\r\n"
                    ."timemodified=".$this->timemodified ."\r\n"
                    ."lang=".$this->lang ."\r\n";
        $course_str .= "\r\nParticipants\r\n";
        foreach($this->participants as $user){
            $course_str .= $user."\r\n";
        }
        foreach($this->managers as $user){
            $course_str .= $user."\r\n";
        }
        foreach($this->contents as $content){
            //$course_str .= $content."\r\n";
        }
        $course_str .= "\r\nNotifications\r\n";
        foreach($this->notifications as $notice){
            $course_str .= $notice."\r\n";
        }
        //print "------------------------";
        }
        return $course_str;
    }
    
}
