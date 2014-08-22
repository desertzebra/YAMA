<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Post_Content
 *
 * @author desertzebra
 */
require_once "config.php";
require_once "$CFG_YAMA->yamadir/model/Content_Model.php";
require_once "$CFG_YAMA->moodledir/mod/forum/lib.php";

class Post_Content extends Content_Model{
    
    protected $modifiedBy;
    protected $modifiedAt;
    protected $discussionId;
    protected $courseId;
    
    public $parent;
    public $mailed;
    public $mailnow;
    public $totalscore;
    public $children;
    public $messagetrust;
    
    public function __construct() {
        $this->modifiedBy = new User_Model(true);
        $this->children = array();
        parent::__construct();
    }
    
    function get_mailnow(){
        if(empty($this->mailnow)){
            return 0;
        }
        return 1;
    }
    function save($courseId=0,$forumId=0,$skipCompletion = false){
        if($this->state===self::STATE_SAVE){
            if($this->addPerm()){
                print "New post added\r\n";
                parent::save();
            }else{
                print_error("Error adding post($this->id)\r\n");
            }
        }
        else if($this->state===self::STATE_DELETE){
            if($this->deletePerm($courseId,$forumId,$skipCompletion)){
                print "post($this->id) deleted\r\n";
                unset($this);
                parent::save();
            }else{
                print_error("Error deleting post($this->id)\r\n");
            }
        }
    }
    public function populate($post){
        
        if(isset($post->parent)){
            $this->parent = $post->parent;
            unset($post->parent);
        }
        if(isset($post->discussion)){
            $this->discussionId = $post->discussion;
            unset($post->discussion);
        }
        if(isset($post->userid)){
            $post->owner = $post->userid;
            unset($post->userid);
        }
        if(isset($post->created)){
            $post->timeCreated = $post->created;
            unset($post->created);
        }
        if(isset($post->usermodified) &&
                isset($post->timemodified) ){
            $this->modifiedBy->id = $post->usermodified;
            $this->modifiedAt = $post->timemodified;
            $this->modifiedBy->load();
            unset($post->usermodified);
            unset($post->timemodified);
        }else if(isset($post->modifiedBy) &&
                isset($post->modifiedAt) ){
            $this->modifiedBy->id = $post->modifiedBy;
            $this->modifiedAt = $post->modifiedAt;
            $this->modifiedBy->load();
            unset($post->modifiedBy);
            unset($post->modifiedAt);
        }
        if(isset($post->message)){
            $post->text = $post->message;
            unset($post->message);
        }
        if(isset($post->messageformat)){
            $this->format = $post->messageformat;
            unset($post->messageformat);
        }
        if(isset($post->messagetrust)){
            $this->messagetrust = $post->messagetrust;
            unset($post->messagetrust);
        }
        if(isset($post->mailed)){
            $this->mailed = $post->mailed;
            unset($post->mailed);
        }
        if(isset($post->mailnow)){
            $this->mailnow = $post->mailnow;
            unset($post->mailnow);
        }else{
            $this->mailnow = 0;
        }
        if(isset($post->totalscore)){
            $this->totalscore = $post->totalscore;
            unset($post->totalscore);
        }else{
            $this->totalscore = 0;
        }
        if(isset($post->course)){
            $this->courseId = $post->course;
            unset($post->courseId);
        }else if(isset($post->courseId)){
            $this->courseId = $post->courseId;
            unset($post->courseId);
        }
        if(isset($post->children)){
            $this->addChildren($post->children);
            unset($post->children);
        }
        parent::populate($post);
        $this->content_type="post";
        
      /*  print "Remaining Post = ";
        print_r($post);
        print "--------\r\n";
        */
    }
    
    protected function addChildren($children){
            foreach($children as $post){
                $post_obj = new Post_Content();
                $post_obj->populate($post);
                array_push($this->children, $post_obj);
            }
    }
    function add(){
        $this->state = self::STATE_SAVE;
        foreach($this->children as $post){
            $post->state = self::STATE_SAVE;
        }
    }
    
    private function addPerm(){
        if(!$this->requiresSave()){
            return false;
        }
        
        $this->message = $this->text;
        $this->messageformat = $this->format;
        $this->timemodified = time();//$this->modifiedAt;
        //$this->usermodified = (empty($this->modifiedBy->id))?$this->owner->id:$this>modifiedBy->id;
        $this->created = time();//$this->timeCreated;
        $this->course = $this->courseId;
        $this->subject = (empty($this->subject))?"New YAMA Post":  $this->subject;
        if(!empty($this->discussionId)){
            $this->discussion = $this->discussionId;
        }
        if(empty($this->discussion) || $this->discussion<1){
            print_error("This post has no associated discussion.\r\n");
            return false;
        }
        $this->userid = $this->owner->id;
        if(empty($this->modifiedBy->id)){
            $this->usermodified = $this->userid;
        }else{
            $this->usermodified = $this->modifiedBy->id;
        }
        $this->messagetrust = (empty($this->messagetrust))?0:$this->messagetrust;
        $message = '';
        //print "Adding new post, cm=".$this->discussion."\r\n";
        //print_r($this);
        $this->id = forum_add_new_post($this,null,$message);
        print "New post added. id=".$this->id."\r\n$message\r\n";
        $this->state = self::STATE_LOAD;
        return true;
        
    }
    function delete(){
        $this->state = self::STATE_DELETE;
        foreach($this->children as $post){
            $post->state = self::STATE_SAVE;
        }
    }
    private function deletePerm($courseId,$forumId,$skipCompletion = false){
        global $DB;
        if($courseId<1 || $forumId<1){
            return false;
        }
       if(!$this->requiresDelete()){
           return false;
       }
       if (! $forum = $DB->get_record("forum", array("id" => $forumId))) {
            print_error('invalidforumid', 'forum');
            return false;
       }
       if (!$cm = get_coursemodule_from_instance("forum", $forumId, $courseId)) {
            print_error('invalidcoursemodule');
            return false;
        }
        if (!$course = $DB->get_record('course', array('id' => $courseId))) {
           print_error('invalidcourseid');
           return false;
        }
        $this->discussion = $this->discussionId;
        $this->forum = $forumId;
        $this->course = $courseId;
        return forum_delete_post($this, true, $course, $cm, $forum,$skipCompletion);

    }
   
    function get_messagetrust(){
        if (!$cm = get_coursemodule_from_instance('forum', $this->forumId, $this->courseId)) { 
            print_error('invalidcoursemodule');
        }
        $modcontext = context_module::instance($cm->id);
        $trust = trusttext_trusted($modcontext);
        return (empty($trust))?0:trust;
        
    }
    public function toJsonString() {
        $ret_str = parent::toJsonString().",";
        $ret_str .= "\"id\":\"".$this->id."\","
                . "\"parent\":\"".$this->parent."\","
                . "\"discussion\":\"".$this->discussionId."\","
                . "\"modifiedBy\":\"".$this->modifiedBy."\","
                . "\"modifiedAt\":\"".$this->modifiedAt."\","
                . "\"mailed\":\"".$this->mailed."\","
                . "\"mailnow\":\"".$this->mailnow."\","
                . "\"total score\":\"".$this->totalscore."\",";
        $ret_str .= "\"course:\"".$this->courseId."\"";

        return $ret_str;
    }

    public function __toString() {
        $ret_str = parent::__toString()."\r\n";
        $ret_str .= "\r\nPost=\r\n".$this->id.""
                . "parent=".$this->parent."\r\n"
                . "discussion=".$this->discussionId."\r\n"
                . "modifiedBy=".$this->modifiedBy."\r\n"
                . "modifiedAt=".$this->modifiedAt."\r\n"
                . "mailed=".$this->mailed."\r\n"
                . "mailnow=".$this->mailnow."\r\n"
                . "total score=".$this->totalscore."\r\n";
        $ret_str .= "course=".$this->courseId."\r\n";
        
        return $ret_str;
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
