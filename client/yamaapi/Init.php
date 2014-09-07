<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if(file_exists('config.php')){
  include_once('config.php');
}elseif(file_exists('config_client.php')){
  include_once('config_client.php');
  include_once($CFG_YAMAAPI->yamadir.'/config.php');
}elseif(file_exists("../config_client.php")){
  include_once('../config_client.php');
  include_once($CFG_YAMAAPI->yamadir.'/config.php');
}else{
  echo "<p>Cannot find config files to load.</p>\r\n";
  echo "<p>current dir=".getcwd() . "</p>\r\n";
  die();
}
$path = $CFG_YAMA->moodledir;
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
define('CLI_SCRIPT',0);
require_once($CFG_YAMA->moodledir.'/config.php');
require_once($CFG_YAMA->moodledir.'/lib/externallib.php');
global $CFG;
$CFG->debug = 99999;
require_once "$CFG_YAMA->yamadir/utility/User.php";
require_once "$CFG_YAMA->yamadir/utility/Course.php";
class Init{
private $active_user;
private $active_course;
private $associated_users;
private $enrollable_courses;
function __construct(){
  $this->active_user = new User(false);  
  $this->active_course = new Course(false);
}
function initUser($userid){
//print "<p>userid=$userid</p>";
    $this->active_user->setUserAttribute('id',$userid);
    $this->active_user->load();
}
/*
 * Login the user
 */
function loginUser($username,$password){
    $status = $this->active_user->login($username,$password);
    if(is_object($status)){
        $this->initUser($status->id);
	return '';
    }else{
	return $status;
    }
}
/*
 * Logout the user
 */
function logoutUser(){
    $this->active_user->logout();
    $this->active_user = null;
    $this->active_course = null;
}
function initCourse($course){
    $this->active_course->load($course);
}
function getCoursesForActiveUser(){
    if($this->checkActiveUser()){
        return $this->active_user->getCourses();
    }else{
        return null;
    }
}
function getMessages($userTo=null){
	return $this->active_user->getMessages($userTo);
}
function checkActiveUser(){
    if(isset($this->active_user)){
      $userid = $this->active_user->getUserAttribute('id');
      $fullname = $this->active_user->getUserAttribute('fullname');
    }
    if(isset($userid) && $userid>0){
	if(isset($fullname) && trim($fullname)!==''){
            return true;
	}else{
	    $this->active_user->load();
	    return true;
	}
   }else{
        //print_error('No active user defined');
        return false;
   }	

}
function checkActiveCourse(){
    if(isset($this->active_course)){
        $courseid = $this->active_course->getCourseAttribute('id');
        $coursename = $this->active_course->getCourseAttribute('name');
    }
    if(isset($courseid) && $courseid>0){
      if(isset($coursename) && trim($coursename)!==''){
        return true;
      }else{
        $this->active_course->load();
        return true;
      }
    }else{
      return false;
    }
}
function getCourseContents($course=null){
if(!isset($course) && !$this->checkActiveCourse()){
	$this->initCourse($course);
	$this->load();
}
return $this->active_course->getContents();
}
function getUserJsonStr(){
   if($this->checkActiveUser()){
       return $this->active_user->toJsonString();
   }else{
       return '';
   }
}
function getUserAttr($key){
    if($this->checkActiveUser()){
        return $this->active_user->getUserAttribute($key);
    }else{
        return null;
    }
}
function getCourseAttr($key){
    if($this->checkActiveCourse()){
        return $this->active_course->getCourseAttribute($key);
    }else{
        return null;
    }
}
function getContent($contentType,$contentId){
$contentList = $this->active_course->getContentByType($contentType);
    foreach($contentList as $content){
        if($content->id===$contentId){
            return $content;
        }
    }
return null;
//print_error("Content Not found with type=$contentType and id=$contentId");
}
function makeActive(){
  $this->active_user->makeActive();
}
function save(){
   print "\r\nSaving active course\r\n";
   $this->active_course->save();
}
function start(){
    $this->initUser(4);
    $courseList = $this->getCoursesForActiveUser();
    $this->initCourse($courseList[0]);
    
   print $this->active_course."\r\n";
   //$forum_content = $this->active_course->getContentByType("forum");
   print "Fetching all quiz contents from the course=".
           $this->active_course->getCourseAttribute('id')."\r\n";
   $quiz_content = $this->active_course->getContentByType("quiz");
   print "-----------------------------\r\n\r\n";
   //print "$quiz_content";
   print"Categories=\r\n";
   print $quiz_content[0]->getCategories();
   print "-----------------------------\r\n\r\n";
   print"Types=\r\n";
   print $quiz_content[0]->getTypes();
   print "-----------------------------\r\n\r\n";
   $selType = 'calculated';
   $selCat = 4;
   $qorder = $quiz_content[0]->addQuestion("Sample Question\r\nYAMA\r\n",
           FORMAT_PLAIN, $selType,$selCat,"Question");
   print "Setting options for $qorder question\r\n";
   $options = new Question_Options();
   $options->shuffleanswers = 1;
   $options->correctfeedback = "Correct Answer";
   $options->incorrectfeedback = "Incorrect Answer";
   $options->partiallycorrectfeedback = "Partially Correct";
   //print $quiz_content[0]->getType($selType)->get_numbering_styles();
   $options->answernumbering='ABC';
   $answer_arr = array();
   $answer= new Answer_Content();
   $answer->text = "Sample Answer";
   array_push($answer_arr, $answer);
    $options->answers = $answer_arr;

   $quiz_content[0]->setOptions($qorder,$options);
      
   print "-----------------------------\r\n\r\n";
   //foreach($forum_content as $forum){
   //print "\r\n".$quiz_content[0]."\r\n";
   $this->active_user->makeActive();
   print "\r\nSaving active course\r\n";
   
   $this->active_course->save();
   
//   $new_dis_id = $forum_content[2]->addDiscussion("\r\n\r\nHELLO!!\r\n",  $this->active_user->getUserAttribute("id"));
//   print_r($forum_content[2]->discussions[$new_dis_id]);
//   $discussion = $forum_content[2]->findDiscussionByIndex($new_dis_id);
//   if($discussion!==null){
//       $discussion->addPost("\r\n\r\nNew Post(".time().")!!\r\n",  $this->active_user->getUserAttribute("id"));
//   }else{
//       print_error("discussion $new_dis_id not found\r\n");
//   }
//   $discussion_array = $forum_content[2]->findDiscussionByNumberOfPosts(0);
//   foreach($discussion_array as $key){
//       //print("Deletion index =".$key."\r\n");
//       //print("Deletion ID =".$forum_content[2]->discussions[$key]->id."\r\n");
//       if(empty($forum_content[2]->discussions[$key]->forumId)){
//           continue;
//       }
//       $forum_content[2]->deleteDiscussion($key);
//   }
//   $forum_content[2]->save();
//       print "\r\nFORUM=".$forum_content[2]."\r\n";
//       
   //}
}

function clear_models(){
    
}
function save_state(){
    
}
}

