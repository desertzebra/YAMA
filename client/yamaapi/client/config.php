<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$CFG_YAMAAPI = new stdClass();
$CFG_YAMAAPI->yamadir = '/media/sf_NetBeansProjects/YAMA';
function filter_input($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>
