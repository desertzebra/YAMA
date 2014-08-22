<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Content
 *
 * @author desertzebra
 */
require_once "config.php";
require_once "$CFG_YAMA->yamadir/model/Model.php";
require_once "$CFG_YAMA->yamadir/model/Submission_Model.php";
require_once "$CFG_YAMA->yamadir/model/User_Model.php";
require_once "$CFG_YAMA->yamadir/model/File_Storage_Model.php";

class Content_Model extends Model{
    /*
     * An id for the content. Must be provided.
     */
    public $id;
    protected $module_id;
    public $subject;
    public $name;
    public $description;
    public $text;
    public $cm;
    public $children;
    public $format;
    public $groupid;
    public $type;
    public $parent;
    public $sectionid;
    protected $target;
    protected $owner;
    protected $isVisible;
    protected $section_num;
    /*
     * The creation time for the content. Must be provided.
     */
    protected $timeCreated;
    protected $lastAccess;
    protected $isRead;
    protected $fileLinks;
    
    protected $startTime;
    protected $endTime;
    
    /*
     * The type of the content. Must be provided.
     */
    protected $content_type;
    
    public function __construct() {
        $this->fileLinks = array();
        $this->children = array();
        //$this->owner = new User_Model();
        $this->target = array();
        parent::__construct();
    }
    function toJsonString(){
        $content_str =  "\"id\":\"".$this->id . "\"," .
                "\"module_id\":\"".$this->module_id . "\",".
               "\"name\":\"".$this->name . "\",".
                "\"section\":\"".$this->section_num . "\",".
                "\"sectionid\":\"".$this->sectionid . "\",".
               "\"subject\":\"".$this->subject . "\",".
               "\"text\":\"".$this->text . "\",".
                "\"format\":\"".$this->format . "\",".
               "\"timeCreated\":\"".$this->timeCreated . "\",".
                "\"Group id\":\"".$this->groupid . "\",".
                "\"startTime\":\"".$this->startTime . "\",".
                "\"endTime\":\"".$this->endTime . "\",".
               "\"isRead\":\"".$this->isRead . "\",".
                "\"isVisible\":\"".$this->isVisible . "\",".
               "\"lastAccess\":\"".$this->lastAccess . "\",";
               "\"parent\":\"".$this->parent . "\",";
               foreach ($this->fileLinks as $file){
               $content_str .= "\"files\":\"".$file. "\",";
               }
		$content_str = rtrim($content_str,',');
               $content_str .="\"owner\":".$this->owner->toJsonString() . ",";
               foreach($this->target as $user){
               $content_str.="\"target\":".$user->toJsonString() . ",";
               }
		$content_str = rtrim($content_str,',');
               $content_str .= "\"type\":".$this->content_type . "\",";
               $content_str .="\"children\":{";
               foreach($this->children as $child){
                    $content_str .= $child.",";
               }
		$content_str = rtrim($content_str,',');
               $content_str .="}";
               return $content_str;
    }
    function __toString() {
        $content_str =  "\r\nid=".$this->id . "\r\n" .
                "module id=".$this->module_id . "\r\n".
               "name=".$this->name . "\r\n".
                "section=".$this->section_num . "\r\n".
               "sectionid=".$this->sectionid . "\r\n".
               "subject=".$this->subject . "\r\n".
               "text=".$this->text . "\r\n".
                "format=".$this->format . "\r\n".
               "timeCreated=".$this->timeCreated . "\r\n".
                "Group id=".$this->groupid . "\r\n".
                "startTime=".$this->startTime . "\r\n".
                "endTime=".$this->endTime . "\r\n".
               "isRead=".$this->isRead . "\r\n".
                "isVisible=".$this->isVisible . "\r\n".
               "lastAccess=".$this->lastAccess . "\r\n";
               "parent=".$this->parent . "\r\n";
               foreach ($this->fileLinks as $file){
               $content_str .= "files=".$file. "\r\n";
               }
               $content_str .="owner=".$this->owner . "\r\n";
               foreach($this->target as $user){
               $content_str.="target=".$user . "\r\n";
               }
               $content_str .= "type=".$this->content_type . "\r\n";
               $content_str .="Children=\r\n";
               foreach($this->children as $child){
                    $content_str .= $child."\r\n";
               }
               return $content_str;
    }
    
    function populate($content){
        
        if(!isset($this->id) && isset($content->id) && $content->id > 0 ){
            $this->id = $content->id;
            unset($content->id);
        }
        if(!isset($this->cm) && isset($content->cm)){
            $this->cm = $content->cm;
            unset($content->cm);
        }
        if(isset($this->cm_obj))
        if(!isset($this->module_id) && isset($content->module) && $content->module > 0 ){
            $this->module_id = $content->module;
            unset($content->module);
        }
        if(!isset($this->name)){
        if(isset($content->name)){
            $this->name = $content->name;
            unset($content->name);
        }else if(isset($content->subject)){
            $this->name = $content->subject;
        }
        }
        if(!isset($this->subject)){
        if(isset($content->subject)){
            $this->subject = $content->subject;
            unset($content->subject);
        }
        }
        if(!isset($this->text)){
        if(isset($content->text)){
            $this->text = $content->text;
            unset($content->text);
        }else if(isset($content->content)){
            $this->text = $content->content;
            unset($content->content);
        }
        }
        
        if(!isset($this->description) && isset($content->description)){
            $this->description = $content->description;
            unset($content->description);
        }
        if(!isset($this->timeCreated)){
        if(isset($content->timeCreated) && $content->timeCreated > 0 ){
            $this->timeCreated = $content->timeCreated;
            unset($content->timeCreated);
        }else if(isset($content->added) && $content->added > 0 ){
            $this->timeCreated = $content->added;            
            unset($content->added);
        }
        }
        
        if(!isset($this->lastAccess) && isset($content->lastAccess)){
            $this->lastAccess = $content->lastAccess;
            unset($content->lastAccess);
        }
        if(!isset($this->fileLinks) || !is_array($this->fileLinks)){
            $this->fileLinks = array();
        }
        if(isset($content->fileLinks)){
            $content->files = $content->fileLinks;
            unset($content->fileLinks);
        }else if(isset ($content->attachment)){
            $content->files = $content->attachment;
            unset($content->attachment);
        }
        if(isset ($content->files)){
            $this->add_file($content->files);
            unset($content->files);

        }
        
        if(!isset($this->isRead) && isset($content->isRead)){
            $this->isRead = $content->isRead;
            unset($content->isRead);
        }
        
        if(!isset($this->isVisible)){
            if(isset($content->visible)){
            $this->isVisible = $content->visible;
            unset($content->visible);
        }else if(isset($content->isVisible)){
            $this->isVisible = $content->isVisible;
            unset($content->isVisible);
        }
        }
        
        if(!isset($this->section_num)){
            if(isset($content->section)){
                $this->section_num = $content->section;
                unset($content->section);
            }else if(isset ($content->section_num)){
                $this->section_num = $content->section_num;
                unset($content->section_num);
            }
        }
        if(!isset($this->sectionid)){
            if(isset($content->sectionid)){
                $this->sectionid = $content->sectionid;
                unset($content->sectionid);
            }
        }

        if(!isset($this->target) || !is_array($this->target)){
            $this->target = array();
        }
        if(isset($content->target)){
            $this->add_receiver($content->target);
            unset($content->target);
        }
        if(isset($content->receiver)){
            $this->add_receiver($content->receiver);
            unset($content->receiver);
        }elseif(isset($content->useridto)){
            $this->add_receiver($content->useridto);
            unset($content->useridto);
        }

        if(isset($content->owner)){
            $content->sender = $content->owner;
            unset($content->owner);
        }

        if(!isset($this->owner) && isset($content->sender)){
            $this->set_owner($content->sender);
            unset($content->sender);
        }
        if(!isset($this->format)){
            if(isset($content->format)){
                $this->format = $content->format;
                unset($content->format);
            }
        }
            //echo $this->groupid."==".$content->groupid."\r\n";
            
        if(!isset($this->groupid)){
            if(!empty($content->groupid)){
                $this->groupid = $content->groupid;
                unset($content->groupdid);
            }else{
                $this->groupid = -1;
            }
        }
        if(!isset($this->startTime)){
            if(isset($content->timestart)){
                $this->timestart = $content->timestart;
                unset($content->timestart);
            }else if(isset($content->availablefrom)){
                $this->startTime = $content->availablefrom;
                $this->availablefrom = $content->availablefrom;
                unset($content->availablefrom);
            }else if(isset($content->startTime)){
                $this->startTime = $content->startTime;
                unset($content->startTime);
            }
        }
        if(!isset($this->endTime)){
            if(isset($content->timeend)){
                $this->timeend = $content->timeend;
                unset($content->timeend);
            }else if(isset($content->availableuntil)){
                $this->startTime = $content->availableuntil;
                $this->availableuntil = $content->availableuntil;
                unset($content->availableuntil);
            }else if(isset($content->endTime)){
                $this->endTime = $content->endTime;
                unset($content->endTime);
            }
        }
        if(!isset($this->content_type)){
            if(isset($content->content_type)){
            $this->content_type = $content->content_type;
            unset($content->content_type);
            }else if(isset($content->mod)){
            $this->content_type = $content->mod;
            unset($content->mod);
            }else if(isset($content->modname)){
            $this->content_type = $content->modname;
            unset($content->modname);
            }    
        }
        if(!isset($this->parent) && isset($content->parent)){
            $this->parent = $content->parent;
            unset($content->parent);
        }
        if(!empty($content->children)){
            $this->addChildren($content->children);
        }
            
        return true;
    }
    protected function addChildren($children){
            foreach($children as $child){
                $content_obj = new Content_Model();
                $content_obj->populate($child);
                array_push($this->children, $content_obj);
            }
    }
    function get_groupid(){
        if(empty($this->groupid)){
            return 0;
        }
        return 1;
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
    function set_owner($owner){
        if(is_numeric($owner)){
                $user = new User_Model(true);
                $user->id = $owner;
                $user->load();
                $this->owner = $user;
            }
            else if(is_object($owner)){
                $this->owner = $owner;
            }
    }
    function add_receiver($receiver){
        if(is_array($receiver)){
                foreach($receiver as $user){
                if(is_numeric($user)){
                $user_obj = new User_Model(true);
                $user_obj->id = $user;
                $user_obj->load();
                array_push($this->target,$user_obj);
                }
            else if(is_object($user)){
                 $user = true;
                array_push($this->target,$user);
            }
               }
            }
            else if(is_numeric($receiver)){
                $user = new User_Model(true);
                $user->id = $receiver;
                $user->load();
                array_push($this->target,$user);
            }
            else if(is_object($receiver)){
                array_push($this->target,$receiver);
            }
    }
    function add_file($files){
        if(is_array($files)){
                foreach($files as $file){
                    if(is_numeric($file)){
                $file_obj = new File_Storage_Model();
                $file_obj->id = $file;
                $file_obj->load();
                array_push($this->fileLinks,$file_obj);
            }
            else if(is_object($file)){
                array_push($this->fileLinks,$file);
            }
            }
            } else if(is_numeric($files)){
                $file_obj = new File_Storage_Model();
                $file_obj->id = $files;
                $file_obj->load();
                array_push($this->fileLinks,$file_obj);
            }
            else if(is_object($files)){
                array_push($this->fileLinks,$files);
            }
    }
}
