<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Submission_Model
 *
 * @author desertzebra
 */
require_once('Model.php');
require_once('User_Model.php');
require_once('File_Storage_Model.php');
require_once('Content_Model.php');

class Submission_Model extends Model {
    private $id;
    private $question_content;
    private $submission_content;
    private $users;
    private $grades;
}
