<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Discussion_Content
 *
 * @author desertzebra
 */
require_once "config.php";
require_once "$CFG_YAMA->yamadir/model/Content_Model.php";
require_once "$CFG_YAMA->moodledir/mod/forum/lib.php";
require_once "$CFG_YAMA->yamadir/model/content/forum/Post_Content.php";
class Discussion_Content extends Post_Content{
    protected $posts;
    protected $forumId;
    
    const SORT_DESC = "p.created Desc";
    const SORT_ASC = "p.created Asc";
    
    public function __construct() {
        $this->posts = array();
        parent::__construct();
    }
    public function toJsonString() {
        $ret_str = parent::toJsonString().",";
        $ret_str .= "\"forum\":\"".$this->forumId."\",";
        $ret_str .= "\"postcount\":\"".count($this->posts)."\",";
        foreach($this->posts as $post){
            $ret_str .= $post.",";
        }
	$ret_str = rtrim($ret_str,',');
        return $ret_str;
    }

    public function __toString() {
        $ret_str = parent::__toString()."\r\n";
        $ret_str .= "forum=".$this->forumId."\r\n";
        $ret_str .= "=Posts(".count($this->posts).")=";
        foreach($this->posts as $post){
            $ret_str .= $post."\r\n";
        }
        return $ret_str;
    }
    public function get_first_post($discussionid=''){
        if(!isset($discussionid)||$discussionid === ''){
          if(isset($this->id)){
            $discussionid = $this->id;
          }else{
            print_error("discussion id not set");
          }
        }
        $firstpost = forum_get_firstpost_from_discussion($discussionid);
        $post_obj = new Post_Content();
        $post_obj->populate($firstpost);

        return $post_obj;
    }

     function delPost($post){
	$postid = 0;
	if(is_object($post)){
		$postid = $post->id;
	}else{
		$postid = $post;
	}
       $post = getPostById($postid);
       $post->deletePerm($this->courseId,$this->forumId);
       //removing the post from this discussion.
       unset($this->posts[findPostById($postid)]);
       
     }
     function addPost($text="",$userid=-1,$format=""){
        global $USER;
        if(empty($userid)|| $userid<0){
            $userid = $USER->id;
        }
        if(is_object($userid)){
            $userid = $userid->id;
        }
        $new_post = new Post_Content();
        $raw_post = new stdClass();
        if(empty($this->id) || $this->id<1){
            
        }else{
            $raw_post->discussion = $this->id;
        }
        //print("raw_post id=$this->id\r\n");
        $raw_post->text = empty($text)?"New Post, default text.\r\nYAMA\r\n":$text."\r\nYAMA\r\n";
        $raw_post->format = empty($format)?editors_get_preferred_format():$format;
        $raw_post->userid = $userid;
        $raw_post->messagetrust = $this->get_messagetrust();
        $raw_post->courseId = $this->courseId;
        //print_r($raw_obj);
        $new_post->populate($raw_post);
        $new_post->add();
        //print_r($new_post);
        //print "+++++++++++++++++++";
        array_push($this->posts, $new_post);
    }
    function save(){
        global $DB;
        //print "\r\nDiscusion Object Before Adding\r\n";
        //print_r($this);
        if($this->state===self::STATE_SAVE){
            if($this->addPerm()){
                print "New discussion($this->id) added\r\n";
                //print "\r\nDiscusion Object\r\n";
                //print_r($this);
                //parent::save();
            }else{
                print_error("Error adding discussion($this->id)\r\n");
            }
        }
        else if($this->state===self::STATE_DELETE){
            if($this->deletePerm()){
                print "discussion($this->id) deleted\r\n";
                unset($this);
                return;
            }else{
                print_error("Error deleting discussion($this->id)\r\n");
            }
        }

//print "<div>+++++++++++++++++++++++++++++++++++++++++++++++++++++++</div>";
//print_r($this->posts[1]);
        if(count($this->posts)>0){
        print "adding posts\r\n\r\n";
        if(empty($this->posts[0]->id) ||
                $this->posts[0]->state===self::STATE_SAVE){
            $this->posts[0]->parent = 0;
            $this->posts[0]->discussion = $this->id;
            $this->posts[0]->save();
            $this->posts[0]->state = self::STATE_LOAD;
            
        }
        $this->firstpost = $this->posts[0]->id;
        $this->timemodified = (empty($this->posts[0]->timeCreated))?time():$this->posts[0]->timeCreated;
        $this->usermodified = (empty($this->posts[0]->timeCreated))?time():$this->posts[0]->owner->id;
        print "\r\nDiscusion Object\r\n";
        //print_r($this);
        $DB->update_record('forum_discussions',$this);
        foreach($this->posts as $post){
            $post->discussion = $this->id;
            $post->save();
            //print_r($post);
        }
        }
        $this->state = self::STATE_LOAD;
        
    }
    function getPostById($id){
        foreach($this->posts as $post){
            if($post->id === $id){
                return $post;
            }
        }
        return null;
    }
    function findPostById($id){
        foreach($this->posts as $key=>$post){
            if($post->id === $id){
                return $key;
            }
        }
        return null;
    }
    function findPostByIndex($id){
                return $this->posts[$id];
    }
    function findPostByText($searchString){
        $retPostArr = array();
        foreach($this->posts as $key=>$post){
            if (strpos($post->text,$searchString) !== false ||
                    strpos($post->name,$searchString) !== false ||
                    strpos($post->subject,$searchString) !== false) {
                array_push($retPostArr,$key);
            }

        }
        return $retPostArr;
    }
    private function deletePerm(){
        global $DB;
       if(!$this->requiresDelete()){
           return false;
       }
       //print_r($this);
       if (! $forum = $DB->get_record("forum", array("id" => $this->forumId))) {
           print_error('invalidforumid', 'forum');
           return false;
        }
       if (!$cm = get_coursemodule_from_instance("forum", $this->forumId, $this->courseId)) {
        print_error("Course Module Not set properly ".$this->cm->id.", $this->forumId, $this->courseId\r\n");
        return false;
        }
        if (!$course = $DB->get_record('course', array('id' => $this->courseId))) {
           print_error('invalidcourseid');
           return false;
        }
        $this->forum = $this->forumId;
        $this->course = $this->courseId;
        //print_r($this);
        return forum_delete_discussion($this, false, $course, $cm, $forum);
        
    }
    private function addPerm(){
        if(!$this->requiresSave()){
            return false;
        }
        
        $this->forum = $this->forumId;
        $this->course = $this->courseId;
        $this->message = $this->text;
        $this->messageformat = $this->format;
        $this->timemodified = time();//$this->modifiedAt;
        $this->created = time();//$this->timeCreated;
        $this->discussion = $this->discussionId;
        if(is_object($this->owner)){
            $this->userid = $this->owner->id;
        }else if(is_numeric($this->owner)){
            $this->userid = $this->owner;
        }
        if(empty($this->modifiedBy->id)){
            $this->usermodified = $this->userid;
        }
        $this->messagetrust = $this->get_messagetrust();
        $this->name = (empty($this->name))?"New YAMA Discussion":  $this->name;
        $this->subject = (empty($this->subject))?"New YAMA Discussion":  $this->subject;
        //print_r($this);
        $message ='';
        $this->id = forum_add_discussion($this,null,$message,$this->userid);
        //print $message;
        print "\r\nNew discussion added. id=".$this->id."\r\n";
        return true;
        
    }
    
    function populate($discussionItem){
       
        if(isset($discussionItem->forum)){
            $this->forumId = $discussionItem->forum;
            unset($discussionItem->forum);
        }else if(isset($discussionItem->forumId)){
            $this->forumId = $discussionItem->forumId;
            unset($discussionItem->forumId);
        }
        parent::populate($discussionItem);
        $this->content_type = "discussion";
         //print_r($discussionItem);
    }
    function load($sort, $tracking=false){
        if($this->requiresSave()){
            $this->save();
        }
        if(!$this->requiresLoad()){
            return;
        }
        $this->state = self::STATE_LOAD;
        //print "\r\nin load\r\n".$this->id."\r\n";
        if(empty($this->id)){
            return null;
        }
        if(empty($sort)){
           if(!($sort === $this->SORT_DESC || $sort === $this->SORT_ASC)){
               $sort = $this->SORT_DESC;
           }
        }
        $posts_raw = forum_get_all_discussion_posts($this->id,$sort,$tracking);
        //print "\r\n\r\n\r\n\r\n\r\n==========\r\n";
        //print_r($posts_raw);
        foreach($posts_raw as $post){
            $post_obj = new Post_Content();
            $post_obj->populate($post);
            array_push($this->posts,$post_obj);
        }
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
}
