<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Forum_Content
 *
 * @author desertzebra
 */
require_once "config.php";
require_once "$CFG_YAMA->yamadir/model/Content_Model.php";
require_once "$CFG_YAMA->moodledir/mod/forum/lib.php";
require_once "$CFG_YAMA->yamadir/model/content/forum/Discussion_Content.php";

class Forum_Content extends Content_Model {

    public $assessed;
    public $ratingtime;
    public $assesstimestart;
    public $assesstimefinish;
    public $scale;
    protected $courseid;
    protected $introFormat;
    protected $forceSubscribe;
    protected $discussions;
    protected $cm_obj;
            
    function __construct() {
        //$this->content = new Content_Model();
        $this->discussions = array();
        $this->cm_obj=null;
        $this->content_type = "forum";
        parent::__construct();
    }

    function populate($forum) {
        //print "forum populate\r\n";
        //print_r($forum);
        if(isset($forum->assessed)){
            $this->assessed = $forum->assessed;
            unset($forum->assessed);
        }
        if(isset($forum->ratingtime)){
            $this->ratingtime = $forum->ratingtime;
            unset($forum->ratingtime);
        }
        if(isset($forum->assesstimestart)){
            $this->assesstimestart = $forum->assesstimestart;
            unset($forum->assesstimestart);
        }
        if(isset($forum->assesstimefinish)){
            $this->assesstimefinish = $forum->assesstimefinish;
            unset($forum->assesstimefinish);
        }
        if(isset($forum->course)){
            if(is_numeric($forum->course)){
                $this->courseid = $forum->course;
                unset($forum->course);
            }else if(is_object($forum->course)){
                $this->courseid = $forum->course->id;
                unset($forum->course);
            }
        }else if(isset($forum->courseid)){
            $this->courseid = $forum->courseid;
            unset($forum->courseid);
        }
        
        if(isset($forum->introFormat)){
            $this->introFormat = $forum->introFormat;
            unset($forum->introFormat);
        }
        if(isset($forum->scale)){
            $this->scale = $forum->scale;
            unset($forum->scale);
        }
        if(isset($forum->forceSubscribe)){
            $this->forceSubscribe = $forum->forceSubscribe;
            unset($forum->forceSubscribe);
        }
        if(isset($forum->intro)){
            $this->text = $forum->intro;
            unset($forum->intro);
        }else if(isset ($forum->text)){
            $this->text = $forum->text;
            unset($forum->text);
        }
        if(isset($forum->cm)){
            $this->cm = $forum->cm;
            //print_r($this->cm);
            if (! $this->cm_obj = get_coursemodule_from_id('forum', $this->cm)) {
                print_error('invalidcoursemodule');
            }
            unset($forum->cm);
        }
        if(isset($forum->discussions)){
            if(is_array($forum->discussions)){
                $this->discussions = $forum->discussions;
                unset($forum->discussions);
            }else{
                array_push($this->discussions,$forum->discussions);
                unset($forum->discussions);
            }
        }
        //print_r($forum);
        parent::populate($forum);
    }
    function get_courseid(){
        if(!isset($this->courseid)){
                if (! $forum_raw = $DB->get_record("forum", array("id" => $this->id))) {
                    print_error('invalidforumid', 'forum');
                }
                if(!isset($forum_raw->course)){
                    print_error("\r\n in load(): forum course not set.\r\n");
                return -1;
                }
            $this->courseid = $forum_raw->course;
            }
            return $this->courseid;
    }
    function get_cm_obj(){
        if(!isset($this->cm_obj)){
        if(isset($this->cm)){
            if (! $this->cm_obj = get_coursemodule_from_id('forum', $this->cm)) {
                print_error('invalidcoursemodule');
                return null;
            }
        }else{
            $courseid = $this->get_courseid();
            if(!empty($courseid)){
                if (!$this->cm_obj = get_coursemodule_from_instance("forum", $this->id, $this->get_courseid())) {
                print_error('incorrect parameter forum id or course id');
                return null;
            }
            }else{
                return null;
            }
            
        }


     
        }
           return $this->cm_obj;
    }
    function deleteDiscussion($key){
        if($key<0 || $key>count($this->discussions)){
            print_error("incorrect key for discussion");
        }
        if(!isset($this->discussions[$key])){
            print_error("discussion not found for $key");
        }
        if(!isset($this->discussions[$key]->forumId)){
            $this->discussions[$key]->forumId = $this->id;
        }
        if(!isset($this->discussions[$key]->courseId)){
            $this->discussions[$key]->courseId = $this->courseid;
        }
        $this->discussions[$key]->delete();
    }
    function addDiscussion($text="",$userid=-1,$format=""){
        global $USER;
        if(empty($userid) || $userid<0){
            $userid = $USER->id;
        }
        else if(is_object($userid)){
            $userid = $userid->id;
        }
print "<p>chuth = $userid</p>";
        $new_discussion = new Discussion_Content();
        $raw_obj = new stdClass();
        $raw_obj->forumId = $this->id;
        $raw_obj->courseId = $this->get_courseid();
        $raw_obj->text = empty($text)?"New discussion default text.\r\nYAMA\r\n":$text."\r\nYAMA\r\n";
        $raw_obj->format = empty($format)?editors_get_preferred_format():$format;
        $raw_obj->userid = $userid;
        //print_r($raw_obj);
        $new_discussion->populate($raw_obj);
        $new_discussion->add();
        //print_r($new_discussion);
        //print "+++++++++++++++++++";
        array_push($this->discussions, $new_discussion);
        return count($this->discussions)-1;
    }
    function getDiscussionById($id){
	return findDiscussionByIndex(findDiscussionById($id));
    }
    function findDiscussionById($id){
        foreach($this->discussions as $key=>$discuss){
            if($discuss->id === $id){
                return $key;
            }
        }
        return null;
    }
    function findDiscussionByIndex($id){
		if($id==null) return null;
                return $this->discussions[$id];
    }
    function findDiscussionByNumberOfPosts($numOfPosts){
        $retDiscussionArr = array();
        foreach($this->discussions as $key=>$discuss){
            if(count($discuss->posts)===$numOfPosts){
                array_push($retDiscussionArr, $key);
            }
        }
        return $retDiscussionArr;
    }
    function save(){
        foreach($this->discussions as $discussionObj){
            if($discussionObj->state!==self::STATE_LOAD){
                $discussionObj->save();
            }
        }
    }
    function load(){
        global $DB;
        if($this->requiresSave()){
            $this->save();
        }
        if(!$this->requiresLoad()){
            return;
        }
        $this->state = self::STATE_LOAD;
        if($this->get_cm_obj()!==null){
            if (! $forum_raw = $DB->get_record("forum", array("id" => $this->cm_obj->instance))) {
            print_error('invalidforumid', 'forum');
            }
        }else{
            if(!isset($this->id)){
            print_error("\r\n in load(): forum id not set.\r\n");
            return;
            }
            
            if (! $forum_raw = $DB->get_record("forum", array("id" => $this->id))) {
            print_error('invalidforumid', 'forum');
            }
            if(!isset($forum_raw->course)){
            print_error("\r\n in load(): forum course not set.\r\n");
            return;
            }
            if (! $course = $DB->get_record("course", array("id" => $forum_raw->course))) {
                print_error('coursemisconf');
                return;
            }
            if (!$this->cm_obj = get_coursemodule_from_instance("forum", $forum_raw->id, $course->id)) {
                print_error('incorrect parameter forum id or course id');
                return;
            }
        }
        $this->populate($forum_raw);
        //print "\r\nDiscussions=\r\n";
            $discussions_array = forum_get_discussions($this->cm_obj);
        //print_r($discussions_array);
        foreach($discussions_array as $discuss_item){
            $discuss_obj = new Discussion_Content();
            $discuss_obj->populate((object)$discuss_item);
            $discuss_obj->load(Discussion_Content::SORT_DESC);
            array_push($this->discussions, $discuss_obj);
            //print_r($discuss_item);
        }
        parent::load();
    }
    
    function get_user_posts($forumid, $user){
        global $USER;
        if(!isset($user)||!isset($user->id)||$user->id==0){
            $user = new User_Model(true);
            $user->id = $USER->id;
        }
        $posts = forum_get_user_posts($forumid, $user->id);
        return $posts;
    }
    function get_all_user_posts($user,array $courses,$hasAccess=false,
            $discussionOnly=false,$limitFrom=0,$limitNum=100){
        global $USER;
        if(!isset($user)||!isset($user->id)||$user->id==0){
            $user = new User_Model(true);
            $user->id = $USER->id;
        }
        $forums = forum_get_posts_by_user($user,$courses,$hasAccess,
                $discussionOnly,$limitFrom,$limitNum);
    }
    function get_all_courses_with_posts($user,$discussionOnly=false,
            $includeContexts=false,$limitFrom=0,$limitNum=50){
        global $USER;
        if(!isset($user)||!isset($user->id)||$user->id==0){
            $user = new User_Model(true);
            $user->id = $USER->id;
        }
        $allcourses = forum_get_courses_user_posted_in($user, $discussionOnly,
                $includeContexts,$limitFrom,$limitNum);
        return $allcourses;
    }
    function get_all_forums($user, array $courseids=null,
            $discussionOnly=false,$limitFrom=0,$limitNum=100){
        global $USER;
        if(!isset($user)||!isset($user->id)||$user->id==0){
            $user = new User_Model(true);
            $user->id = $USER->id;
        }
        $allcourses = forum_get_forums_user_posted_in($user,$courseids,
                $discussionOnly,$limitFrom,$limitNum);
        return $allcourses;
    }
    function get_first_post($discussionid){
        if(isset($discussionid)){
            $firstpost = forum_get_firstpost_from_discussion($discussionid);
        }
        return $firstpost;
    }
    function getUnsubscribedList($userid){
        global $USER;
        if(!isset($USER->id) || $USER->id != $userid){
            $USER->id = $userid;
        }
        $unsubscribbedForums = forum_get_optional_subscribed_forums();
        return $unsubscribbedForums;
    }
    function user_post_cap($userid=0,$group=null){
        global $USER;
        
        if($userid!=0){
            $USER->id = $userid;
        }
        if(!isset($USER->id)){
            print_error("No active user found");
            return false;
        }
        $forum = new stdClass();
        $forum->id = $this->id;
        $forum->course = $this->courseid;
        $retChk = forum_user_can_post_discussion($forum,$group);
        return $retChk;
    }
    function user_reply_cap($user,$discussion_pos=0){
        global $USER;
        if(!isset($user)||!isset($user->id)||$user->id==0){
            $user = new User_Model(true);
            $user->id = $USER->id;
        }
        $forum = new stdClass();
        $forum->id = $this->id;
        $forum->course = $this->courseid;
        $discussion = $this->discussions[$discussion_pos];
        return forum_user_can_post($forum, $discussion,$user);
    }
    function subscribeUser($forumid,$userid=0){
        global $USER;
        if($userid==0){
            $userid = $USER->id;
        }
        if(!isset($forumid)){
            return null;
        }
        forum_subscribe($userid, $forumid);
        return true;
    }
    function unsubscribeUser($forumid,$userid=0){
        global $USER;
        if($userid==0){
            $userid = $USER->id;
        }
        if(!isset($forumid)){
            return null;
        }
        forum_unsubscribe($userid, $forumid);
        return true;
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
        $ret_forum_str = parent::toJsonString(). ","
                ."\"assessed\":\"".$this->assessed."\","
                ."\"ratingtime\":\"".$this->ratingtime."\","
                ."\"assesstimestart\":\"".$this->assesstimestart."\","
                ."\"assesstimefinish\":\"".$this->assesstimefinish."\","
                ."\"courseid\":\"".$this->courseid."\","
                ."\"introFormat\":\"".$this->introFormat."\","
                ."\"scale\":\"".$this->scale."\","
                ."\"forceSubscribe\":\"".$this->forceSubscribe."\","
                ."\"discussioncoun\":\"".count($this->discussions)."\",";
        foreach($this->discussions as $discuss_obj){
            $ret_forum_str .= $discuss_obj.",";
        }
        return $ret_forum_str;

    }

    function __toString() {
        $ret_forum_str = "+++++++++++++++++++++++++\r\n"
        . "FORUM=".parent::__toString(). "\r\n"
                ."assessed=".$this->assessed."\r\n"
                ."ratingtime=".$this->ratingtime."\r\n"
                ."assesstimestart=".$this->assesstimestart."\r\n"
                ."assesstimefinish=".$this->assesstimefinish."\r\n"
                ."courseid=".$this->courseid."\r\n"
                ."introFormat=".$this->introFormat."\r\n"
                ."scale=".$this->scale."\r\n"
                ."forceSubscribe=".$this->forceSubscribe."\r\n"
                ."Discussions=".count($this->discussions)."\r\n";
        foreach($this->discussions as $discuss_obj){
            $ret_forum_str .= $discuss_obj."\r\n";
        }
        return $ret_forum_str;
     
    }

}
