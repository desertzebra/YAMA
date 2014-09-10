<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author fahad satti
 */

require_once('config.php');
require_once($CFG_YAMA->moodledir."/user/lib.php");
require_once($CFG_YAMA->moodledir."/login/lib.php");
require_once $CFG_YAMA->yamadir."/model/Model.php";
require_once "$CFG_YAMA->yamadir/model/File_Storage_Model.php";
require_once "$CFG_YAMA->yamadir/model/Capabilities_Model.php";
require_once "$CFG_YAMA->yamadir/model/Preferences_Model.php";
require_once "$CFG_YAMA->yamadir/model/Multi_Course_Model.php";
require_once "$CFG_YAMA->yamadir/model/Multi_Notification_Model.php";
class User_Model extends Model{
    protected $id;
    protected $fullname;
    protected $name;
    protected $username;
    protected $description;
    protected $country;
    protected $city;
    protected $email;
    protected $photolink;
    protected $enrolledCourses;
    protected $messages;
    protected $pref;
    protected $capabilities;
    protected $lastaccess;
    protected $firstaccess;
    protected $phone1;
    protected $timezone;
    protected $lang;
    protected $maildisplay;
    protected $fieldnames;
    protected $contacts;
    protected $autosubscribe;
    protected $miniLoad=false;
    // must use with $this or object ref to avoid conflict with
    // get_user_fieldnames() method.
    function get_user_fieldnames(){
        return array('username','fullname','description',
            'city','country','email','profileimageurl','enrolledcourses',
            'preferences','roles','lastaccess','firstaccess',
            'timezone','lang');
    }
    function __construct($mini=false) {
        
    $this->photolink = new File_Storage_Model();
    $this->miniLoad = $mini;
    if(!$mini){
        $this->pref = new Preferences_Model();
        $this->capabilities = new Capabilities_Model();
        $this->messages = new Multi_Notification_Model();
        $this->contacts = array();
        $this->enrolledCourses = new Multi_Course_Model();
    }
    $this->fieldnames = $this->get_user_fieldnames();
    
    }
    function makeActive(){
        global $USER;
        if(empty($this->id)){
            print_error("No id set for the user to activate");
            return false;
        }
        $USER->id = $this->id;
    }
    function toJsonString(){
        $User_String = "{";
            foreach($this->fieldnames as $fd_name){
                    if($fd_name === "preferences" ||
                    $fd_name === "roles"){
                   continue;
                }
                else if($fd_name==="profileimageurl"){
                    $User_String .= "\"$fd_name\":\"".$this->photolink->getUrl()."\",";
                }
                else if($fd_name ==="enrolledcourses"){
                $User_String .="\"miniLoad\":\"".$this->miniLoad."\",";
                    if(!$this->miniLoad){
                    $User_String .="\"courses\":{";
                    $User_String .= $this->enrolledCourses->toJsonString()."},";
                    }
                }
                else{
                $User_String .= "\"$fd_name\":\"".$this->$fd_name."\",";
                }
        }
       $User_String = rtrim($User_String, ',');
       $User_String .= "\"phone1\":\"".$this->phone1."\",";
       $User_String .= "\"maildisplay\":\"".$this->maildisplay."\",";
       $User_String .= "\"autosubscribe\":\"".$this->autosubscribe."\",";
    
        if(!$this->miniLoad){
            $User_String .= "\"messages\":".$this->messages->toJsonString().",";
       // $User_String .= $this->messages."";
            $User_String .= "\"contact\":{";
        foreach($this->contacts as $key=>$contactList){
            $User_String .="$key:{";
            foreach($contactList as $contact){
                //print_r($contact);
                $User_String .= $contact.",";
            }
            $User_String = rtrim($User_String,',');
            $User_String .="},";
        }
        $User_String = rtrim($User_String,',');
        $User_String .= "}";
        }
        return $User_String."}";
    }
    function  __toString(){
        $User_String = "\r\n++User++\r\n";
            foreach($this->fieldnames as $fd_name){
                    if($fd_name === "preferences" ||
                    $fd_name === "roles"){
                   continue;
                }
                else if($fd_name==="profileimageurl"){
                    $User_String .= "$fd_name=".$this->photolink->getUrl();
                }
                else if($fd_name ==="enrolledcourses"){
                    $User_String .="miniLoad=".$this->miniLoad;
                    if(!$this->miniLoad){
                    $User_String .="\r\nCourses=\r\n";
                    $User_String .= $this->enrolledCourses."\r\n";
                    }
                }
                else{
                $User_String .= "$fd_name=".$this->$fd_name;
                }
        }
        if(!$this->miniLoad){
       // $User_String .= $this->messages."";
        foreach($this->contacts as $contactList){
            foreach($contactList as $contact){
                //print_r($contact);
                $User_String .= $contact."\r\n";
            }
        }
        }
        return $User_String."\r\n++++\r\n";
    }
    function load(){
        if($this->requiresSave()){
	echo "saving";
            $this->save();
        }
        if(!$this->requiresLoad()){
            return;
        }
        $this->state = self::STATE_LOAD;
        //echo "\r\nLoading operation:started...";
        if(isset($this->id) && $this->id>0){
            $this->loadById();
        }
        else{
            echo "Unable to find user, set id(".$this->id
                    .">0) to search";
        }
        parent::load();
        //echo "Loading operation($this->id)... done.\r\n";
    }
    /*
     * Load a user by id.
     * Params:
     * $id = int|0|null
     * Return: object user
     */
    function loadById($userId=0){
        //$user = array('id' => "".$this->id);
        if(!isset($userId) || $userId<1){
            $userId = $this->id;
        }
        $user_arr = (user_get_users_by_id(array($userId)));//new stdClass();
        if(count($user_arr)<0 || !isset($user_arr[$userId])){
            return null;
        }
        $user = (object)$user_arr[$userId];
        //print_r($user);
        if(!isset($user)||$user==null){
            return null;
        }
        else if($userId == $this->id){
            $this->populate($user);
            //echo "\r\nLoading Details($userId) operation:started...";
            $this->loadDetails($user);
           // echo "... done.\r\n";
            return $this;
        }
        else if($userId != $this->id){
            $temp_user = new User_Model(true);
            $temp_user->id = $userId;
            $temp_user->populate($user);
            //echo "\r\nLoading Details($userId) operation:started...";
            $temp_user->loadDetails($user);
            //echo "... done.\r\n";
            return $temp_user;
        }
        else{
            echo "no user details found for ".$userId."\r\n";
            return null;
        }
    }
    protected function loadDetails($user){
        if($this->miniLoad){
            return null;
        }
        $userdetails = user_get_user_details($user,null,$this->fieldnames);
        if(!empty($userdetails)){
            $this->fullname = $userdetails['fullname'];
            if(isset($userdetails['preferences'])){
            foreach($userdetails['preferences'] as $p_name=>$p_value){
                $this->pref->add_preference($p_name, $p_value);
            }
            }
            if(isset($userdetails['enrolledcourses'])){
                //echo "\r\nLoading Courses, operation:started...";
                $this->loadCourses($userdetails);
                //echo "... done.\r\n";
            }
         }
         //echo "\r\nLoading Messages, operation:started...";
         $this->messages->load($user);
         //echo "... done.\r\n";
         //echo "\r\nLoading Contacts, operation:started...";
         $this->loadContacts($user);
         //echo "... done.\r\n";
         
    }
    protected function populate($user){
        //print_r($user);

            if(!isset($this->id) && isset($user->id) && $user->id>0){
                $this->id = $user->id;
                unset($user->id);
            }
            if(!isset($this->maildisplay) && isset($user->maildisplay)){
                $this->maildisplay = $user->maildisplay;
                // unsetting maildisplay causes problems while fetching
                // userdetails. Lots of undefined property errors.
                //unset($user->maildisplay);
            }
        foreach($this->fieldnames as $fd_name){
                if(isset($user->$fd_name)){
                    if($fd_name === "preferences" || 
                    $fd_name === "enrolledcourses" ||
                    $fd_name === "roles"){
                   continue;
                }
                else if($fd_name==="profileimageurl"){
                    $this->photolink->url = $user->$fdname;
                }
                else{
                    $this->$fd_name = $user->$fd_name;
                }
                }
            }
	$this->name = $this->fullname = fullname($user);
            
    }
    public function save(){
        if ($this->requiresSave()){
            $this->messages->save();
        }
        parent::save();
    }
    public function __get($name) {
    return $this->$name;
  }
  public function __set($name, $value) {
        $func = 'set_' . $name;
        if (method_exists($this, $func)) {
            return $this->$func($value);
        } else {
            $this->$name = $value;
        }
    }
    
  function loadCourses($userDetails){
      if($this->miniLoad){
            return null;
        }

      foreach($userDetails['enrolledcourses'] as $course_arr){
        //print_r($course_arr);
          $this->enrolledCourses->addCourse((object)$course_arr);
        }
  }
  function getCourses(){
      if($this->miniLoad){
            return null;
      }
      return $this->enrolledCourses->getAllCourses();
  }
  function loadContacts($user){
      if($this->miniLoad){
            return null;
        }
      $this->contacts = $this->messages->getContacts($user);
  }
  function getContacts(){
      if($this->miniLoad){
            return null;
        }
      return $this->contacts;
  }
  function addContact($userId){
      $this->state = self::STATE_SAVE;
      if($this->miniLoad){
            return null;
        }
      return $this->messages->addContact($userId);
  }
  function removeContact($userId){
      if($this->miniLoad){
            return null;
        }
      return $this->messages->removeContact($userId);
  }
	
    /*
     * Log the user out.
     * Params:
     * void
     * void
     */
    function logout() {
        $authsequence = get_enabled_auth_plugins(); // auths, in sequence
	foreach($authsequence as $authname) {
	    $authplugin = get_auth_plugin($authname);
	    $authplugin->logoutpage_hook();
	}

	require_logout();
	
    }
    
    /*
     * Logging the user into YAMA
     * Params:
     * optional $username = String|null
     * $password = String|null
     * Return: object $user || string error message
     */
    function login($username=null, $password=null) { 
	if($username === null){
	    $username = $this->username;
	}
	
	/*
	 * External auth plugins override the following vars
	 */
	$frm = false;
	$user = false;	
	
	/*
	 * Iterate over auth plugins, sequentially
	 * to authenticate a user, using external auth
	 * plugin.
	 */
	$authsequence = get_enabled_auth_plugins(true);
	foreach($authsequence as $auth){
	    $authplugin = get_auth_plugin($auth);
	    $authplugin->loginpage_hook();
	}

	if($frm!=false){
	 	$frm->username = $username;   
        }else{
		$frm = new stdClass();
		$frm->username = $username;
	}
	
	/*
	 * Check now the form for username and password.
	 */
	if($frm && isset($frm->username)){
	    $frm->username = trim(core_text::strtolower($frm->username));
	    if(is_enabled_auth('none')){
	         if($frm->username != clean_param($frm->username, PARAM_USERNAME)){
		     $msg = get_string('username').': '.get_string("invalidusername");
		     $user = null;
		     return $msg;
		 }
	    }
	    if(!$user){
		if($password!=null){
		    $frm->password = $password;
		}
		$user = authenticate_user_login($frm->username,$frm->password);
	    }
	    
            //complete_user_login
	      complete_user_login($user);

	}

        return $user;
    }
    
    /*
     * Fetch messages for a user.
     * Params:
     * optional $userTo = int|object|array
     * Return: list of messages.
     */
    public function getMessages($userTo=null){
        return $this->messages->get_notfications($userTo);
    }
    /*
     * Send a message to a user or multiple users
     * Params:
     * $userid = int|object|array; containing a user id, object or
     *  an array of multiple user ids or user objects
     * $message = text; containing message that is to be sent.
     * optional $format=FORMAT_PLAIN|FORMAT_HTML for the format of the message
     * Return: Void, after adding the notification.
     */
    public function sendMessage($userid,$message,$format=FORMAT_PLAIN) {
        //print "format=$format\r\n";
        $new_notification = new stdClass();
        $new_notification->text = $message;
        $new_notification->sender = $this->id;
        $new_notification->receiver = $userid;
        $new_notification->format = $format;
        $this->messages->add_notification($new_notification);
        $this->state = self::STATE_SAVE;
    }
    

}
