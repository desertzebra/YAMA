<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

unset($CFG_YAMA);
global $CFG_YAMA;
$CFG_YAMA = new stdClass();
$CFG_YAMA->yamadir = '/var/www/yama/client/yamaapi/';
$CFG_YAMA->moodledir = '/var/www/moodle';
$CFG_YAMA->updateInterval = 60;
$CFG_YAMA->forum = new stdClass();
$CFG_YAMA->forum->path = $CFG_YAMA->yamadir."/model/content/forum/Forum_Content.php";
$CFG_YAMA->forum->cn = "Forum_Content";

$CFG_YAMA->quiz = new stdClass();
$CFG_YAMA->quiz->path = $CFG_YAMA->yamadir."/model/content/quiz/Quiz_Content.php";
$CFG_YAMA->quiz->cn = "Quiz_Content";
?>
