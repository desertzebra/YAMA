<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of File_Storage_Model
 *
 * @author desertzebra
 */
class File_Storage_Model extends Model {
    private $id;
    private $local_path;
    private $url;
    private $name;
    
    function populate($db_id,$db_local_path,$db_url,$db_name){
       $this->id = $db_id;
       $this->local_path = $db_local_path;
       $this->url = $db_url;
       $this->name = $db_name;
    }
    function load(){
        if(!requiresUpdate()){
            return;
        }
        parent::load();
    }
    public function save(){
        echo "Not yet defined";
    }
    public function getId() {
        return $this->id;
    }

    public function getLocal_path() {
        return $this->local_path;
    }

    public function getUrl() {
        return $this->url;
    }

    public function getName() {
        return $this->name;
    }


}
