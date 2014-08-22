<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Notification_Content
 *
 * @author desertzebra
 */
ini_set('display_errors', '1');
error_reporting(E_ALL);
require_once "config.php";
require_once "$CFG_YAMA->yamadir/model/Content_Model.php";
class Notification_Content extends Content_Model {

    //private $content;
    protected $isRead;
    public $smallmessage;
    public $contactlistid;
    public $blocked;

   public function __construct() {
        //$this->content = new Content_Model();
        $this->isRead = false;
        parent::__construct();
    }
   /*
    * Deap Copy of the Notification_Content object. Also converts the message
    * object from Moodle into Notification_Content objet by copying
    * related attributes.
    * Params:
    * $message=Object; containing the message object.
    * Return: Void
    */
  function populate($message) {
      if(is_object($message)){
        if (isset($message->mid)) {
            $this->id = $message->mid;
            unset($message->mid);
        } else if (isset($message->id)) {
            $this->id = $message->id;
            unset($message->id);
        } else{
            $this->id =0;
        }
        if(!isset($message->sender)){
        if (isset($message->useridfrom)) {
            $message->sender = $message->useridfrom;
            unset($message->useridfrom);
        }else if(isset($message->id)){
            $message->sender = $message->id;
            unset($message->id);
        }else{
            print_error("No message sender found");
        }
        }
        if(isset($message->timecreated)){
            $this->timeCreated = $message->timecreated;
            unset($message->timecreated);
            if (isset($message->lastaccess)) {
                $this->lastAccess = $message->lastaccess;
                unset($message->lastaccess);
            }
        }else if(isset($message->timeCreated)){
            $this->timeCreated = $message->timeCreated;
            unset($message->timeCreated);
            if (isset($message->lastAccess)) {
                $this->lastAccess = $message->lastAccess;
                unset($message->lastAccess);
            }
        }else{
            $this->timeCreated = time();
            $this->lastAccess = 0;
        }
        if(isset($message->smallmessage)){
            $this->smallmessage = $message->smallmessage;
            unset($message->smallmessage);
        }
        if(isset($message->blocked)){
            $this->blocked = $message->blocked;
            unset($message->blocked);
        }
        if(isset($message->contactlistid)){
            $this->contactlistid = $message->contactlistid;
            unset($message->contactlistids);
        }
        if(isset($message->fullmessage)){
            $this->text = $message->fullmessage;
            unset($message->fullmessage);
        }else if(isset($message->text)){
            $this->text = $message->text;
            unset($message->text);
        }else{
            print_error("no message found");
        }
        if (isset($message->isRead)) {
            $this->isRead = $message->isRead;
            unset($message->isRead);
        }else{
            $this->isRead = false;
        }
        $this->content_type = "notification";
        parent::populate($message);
      }else{
          print_error("Message must be a object with text, sender and receiver");
      }
    }
    /*
     * Save the Notification, using the owner of this class as sender and 
     * posting a message to each user in the target attribute.
     * Params:Void
     * Return:
     * bool; to indicate the operation result.
     */
    function save() {
        if(!$this->state === STATE_SAVE){
            print_error("State does not indicate any saving. Save Mode=".$this->state);
            return false;
        }
        if(!is_object($this->owner)){
            print_error("\r\nUnable to process user information."
            . " Owner not a object.\r\n");
            return false;
        }
        if(count($this->target)<1){
            print_error("\r\nUnable to process user information. Target array is empty.\r\n");
            return false;
        }
        foreach($this->target as $userTo){
            if(!is_object($userTo)){
                print_error($userTo." not an object\r\n");
                return false;
            }
            $message = $this->prepareMessage();
            $post_out = message_post_message($this->owner, $userTo, $message, $this->format);
            //print("post=".$post_out);
            $this->id = $post_out;
            return true;
        }
        
    }
    /*
     * Prepare the message using, name and text attributes. Additionally add a
     * line to indicate that YAMA was used to save/post the message.
     * Params:Void
     * Return:
     * text; string of the message text.
     */
    function prepareMessage(){
        return $this->name."\r\n".$this->text."\r\n Sent via YAMA\r\n";
    }
    /*
     * Marks a message as read.
     * Note:Very dangerous to call this method to mark a message as read.
     * The way MOODLE works is by storing unread messages in message table
     * while the read messages are kept in messages_read table. The problem
     * arises from the two tables using the save primary key, i.e int ids.
     * So a message in message_read will have an id similar to something
     * in message table.
     * Params:
     * Optional $message=null|Object; null to use the calling reference or the
     *  message Object.
     * Optional $readTime = int; time when the message was read. default 0 to
     *  set current time.
     * Return:
     * bool; false to indicate if the message failed.
     * Object; Message object that was marked read with the new id.
     */
    function read($message=null,$readTime=0){
        if($message==null){
            $message = $this;
        }
        if($readTime==0){
            $readTime = time();
        }
        if($message->isRead){
            //print_error("\r\nMessage already marked read locally."
            //        . " No changes required.\r\n");
            return false;
        }
        if(!$message->checkRead()){
        $message->id = message_mark_message_read($message,$readTime);
        $message->isRead = true;
        return $message;
        }
        return false;
    }
    /*
     * Check the DB through a dirty hack if the message was unread or not.
     * Params:Void;
     * Return:
     * bool; True, if the message was already read and doesn't require
     *  any change. False, if the message was not read and is indeed in the 
     *  message table.
     */
    function checkRead(){
        global $DB;
        if(!($this->id>0)){
            return true;
        }
        if(!isset($this->owner)){
            return true;
        }
        foreach($this->target as $user){
            if(!isset($user)){
                return true;
            }
            else{
                /*
                 * Dirty hack, follows:
                 */
                $sql = 'SELECT m.* FROM {message} m WHERE m.id=:mid AND m.useridto=:useridto AND m.useridfrom=:useridfrom';
                $messages = $DB->get_recordset_sql($sql, array('useridto' => $user->id,'useridfrom' => $this->owner->id,'mid' =>$this->id));
                if((count($messages)===1) && $messages->fullmessage===$this->text){
                    //print_r($messages);
                    return false;
                }
                else{
                    return true;
                }

            }
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

    function toJsonString() {
        return parent::toJsonString(). "," .
                "\"read\":\"".$this->isRead . "\"";
    }

    function __toString() {
        return parent::__toString(). "\r\n" .
                $this->isRead . "\r\n";
    }

}
