<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Multi_Notification_Model
 *
 * @author desertzebra
 */
require_once 'config.php';
require_once "$CFG_YAMA->moodledir/message/lib.php";
require_once "$CFG_YAMA->yamadir/model/content/Notification_Content.php";
class Multi_Notification_Model extends Model {
    /*
     * array $notifications;array of messages
     */
    private $notifications;
    /*
     * 2d array $__user_message_2darray;array of users(who sent a message)
     *  with an array of messages for each user.
     */
    private $__user_message_2darray;

    function __construct() {
        $this->notifications = array();
        $this->__user_message_2darray = array();
    }
    function toJsonString(){
        $message_str = "{";
        foreach ($this->notifications as $message) {
            $message_str .= $message->toJsonString() . ",";
        }
        $message_str = rtrim($message_str, ',');
        return $message_str."}";
    }
    function __toString() {
        $message_str = "\r\nMessages=";
        foreach ($this->notifications as $message) {
            $message_str .= $message . "\r\n";
        }
        return $message_str;
    }
    /*
     * Returns the notification array with all the notifications.
     * Params:optional $target user.
     * Return:array; containing all the Notification_Content objects.
     */
    function get_notfications($target=null) {
        if(isset($target) && trim($target)!==''){
        	return $this->findNotificationsByTarget($target);
        }else{
        	return $this->notifications;
        }
        
    }
    /*
     * Converts the message object into Notification_Content object.
     * Params:
     * $message=object; contains the message object.
     * Return:
     * Object; the newly created Notification_Content object.
     */
    private function parse_message($message) {
        $new_notification = new Notification_Content();
        $new_notification->populate($message);
        return $new_notification;
    }
    /*
     * Creates and stores a Notification_Content object for the message.
     * Params:
     * $message=object; contains the message object.
     * optional $isNew=bool; flag to indicate if the message is newly created.
     * optional $isRead=bool; flag to indicate if the message has been read.
     * Return:
     * bool; False to indicate if the notification is null or not an object
     * Void; otherwise.
     */
    public function populate($message,$isNew=false,$isRead=true){
        $notification = $this->parse_message($message);
        if($isNew){
            $notification->state = MODEL::STATE_SAVE;
        }else{    
            $notification->state = MODEL::STATE_LOAD;
        }
            $notification->isRead = $isRead;
            if($notification == null || !is_object($notification)){
            return false;
            }
            $this->notifications[] = $notification;
        if (!isset($this->__user_message_2darray[$notification->owner->id]) ||
                !is_array($this->__user_message_2darray[$notification->owner->id])) {
            $this->__user_message_2darray[$notification->owner->id] = array();
        }
        array_push($this->__user_message_2darray[$notification->owner->id], $notification);
    }
    /*
     * For each message in the notification array, 
     * saves the ones that need saving and
     *  marks as read the ones that have been read.
     * Params:Void
     * Return:Void
     */
    public function save() {
        foreach ($this->notifications as $message) {
            if ($message->requiresSave()) {
                $message->save();
            }
            if ($message->isRead) {
                $message->read();
            }
        }
        parent::save();
    }
    /*
     * Adds a message object into the notification array.
     * Params:
     * $message=object; contains the message object with owner(sender),
     *  receiver(target), text and format.
     * Return: Void
     */
    public function add_notification($message) {
        $this->populate($message,true);
    }
    /*
     * Marks a single message as read, locally.
     * Params:
     * $messageIds=int|array; containing a message id or an array of message ids
     * Return:Void
     */
    function markRead($messageIds) {
        if (is_numeric($messageIds)) {
                $not_key = $this->findNotificationById($messageIds);
         if($not_key>-1){
             $this->notifications[$not_key]->isRead = true;
        }else{
                    print_error("message with key $messageIds not found");
                }
        }else if (is_array($messageIds)) {
            foreach ($messageIds as $id) {
                $not_key = $this->findNotificationById($id);
                if($not_key>-1){
                    $this->notifications[$not_key]->isRead = true;
                }else{
                    print_error("message with key $id not found");
                }
            }
        } else {
            print_error("argument to markRead Method can only be either an array of indexes or integer index.\r\n");
        }
    }
    /*
     * Overridden load method.
     * loads messages for a user. It loads all the recent messages between
     *  the provided parameter user and all the other users that have sent him
     *  a message or have received a message and then based on the
     *  deap search flag loads all the messages for each pair.
     * Params:
     * $user=Object; containing user fields, in particular the user id.
     * optional $doDeapSearch; flag to indicate if all messages for the user
     *  shouldbe loaded.
     * Return: Void.
     */
    function load($user, $doDeapSearch = true) {
        if($this->requiresSave()){
            $this->save();
        }
        if(!$this->requiresLoad()){
            return;
        }
        $this->state = self::STATE_LOAD;
        //echo "\r\nloading recent.";
        $this->loadRecent($user);
        //echo "... done.\r\n";

        if ($doDeapSearch) {
           // echo "\r\nStarting deap search of messages for".
                    count($this->__user_message_2darray).".";
            foreach ($this->__user_message_2darray as $key => $value) {
                $user2 = new stdClass();
                $user2->id = $key;
                $this->loadAll($user, $user2);
            } 
           // echo "... done.\r\n";
        }
        parent::load();
    }
    /*
     * loads recent messages for a user.
     * Params:
     * $user=Object; containing a user object for whom to fetch
     *  the recent messages for.
     * Optional $limitfrom=int; int to indicate the message start number
     * Optional $limitto =int; int to indicate the message end number
     * Return:Void
     */
    function loadRecent($user, $limitfrom = 0, $limitto = 100) {
            $messages = message_get_recent_conversations($user, $limitfrom, $limitto);
            foreach ($messages as $recent_message) {
                $this->populate($recent_message,false);
            }
    }
    /*
     * Loads all messages for a pair of users.
     * $userFrom=Object; containing a user object from which messages
     *  have been sent.
     * $userTo=Object; containing a user object to whom the messages were sent.
     * $limit=int; int indicating the number of messages to fetch
     *  between the pair.
     * Return: Void
     */
    function loadAll($userFrom, $userTo, $limit = 10) {
        $messages = message_get_history($userFrom, $userTo, $limit);
            foreach ($messages as $message) {
                $this->populate($message,false);
            }
    }
    /*
     * Marks all messages between two users as read in DB.
     * Params:
     * $userTo=int|object; contains the userid or user object to whom
     *  the messages were sent.
     * $userFrom=int|object|array; contains the userid, user object, or an 
     * array of ids or objects, from which the messages were received.
     * Return:
     * bool; indicating operation result.
     */
    function readAllFromUser($userTo,$userFrom){
        if(is_object($userTo)){
            $userTo_id = $userTo->id;
        }else if(is_numeric($userTo)){
            $userTo_id = $userTo;
        }else{
            print_error("Incorrect params for userTo, provided=$userTo");
            return false;
        }
        if(is_object($userFrom)){
            $userFrom_id = $userFrom->id;
        }else if(is_numeric($userFrom)){
            $userFrom_id = $userFrom;
        }else if(is_array($userFrom)){
            foreach ($userFrom as $user){
                readAllFromUser($userTo,$user);
            }
            return true;
        }
        if($userTo_id>0 && $userFrom_id>0){
            message_mark_messages_read($userTo_id,$userFrom_id);
        
        foreach ($this->__user_message_2darray[$userFrom_id] as $messageId){
            $not_key = $this->findNotificationById($messageId);
                if($not_key>-1){
                    $this->notifications[$not_key]->read();
                }else{
                    print_error("message with key $messageId not found");
                }
        }
        return true;
        }
        return false;
        
    }
    function getContacts($user) {
        return message_get_contacts($user);
    }

    function addContact($targetUserId) {
        global $USER;
        if (isset($USER->id)) {
            return message_add_contact($targetUserId);
        } else {
            print_error("\r\nUSER global object" . $USER . "not set\r\n");
        }
        return false;
    }

    function removeContact($targetUserId) {
        global $USER;
        if (isset($USER->id)) {
            return message_remove_contact($targetUserId);
        } else {
            print_error("\r\nUSER global object" . $USER . "not set\r\n");
        }
        return false;
    }
    /*
     * Finds the message id in the notification array.
     * Params:
     * $messageId=int; contains the id of the message to search.
     * Return:
     * $key=int; key of the message in the notification array.
     * -1; if no key was found.
     */
    public function findNotificationById($messageId) {
        foreach ($this->notifications as $key=>$message) {
            if($message->id === $messageId){
                return $key;
            }
        }
        return -1;
    }
    public function findNotificationsByTarget($target){
        $targetUsers = array();
        foreach($this->notifications as $key=>$message){
            if(in_array($message->target,$target)){
                array_push($targetUsers,$message);
            }
        }
        return $targetUsers;
    }

}
