<?php
session_start();

include_once('/var/www/yama/client/config_client.php');
ini_set('display_errors', '1');
error_reporting(E_ALL);
$path = $CFG_YAMAAPI->yamadir;
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
include_once $CFG_YAMAAPI->yamadir."/Init.php";
include_once $CFG_YAMAAPI->clientdir."/yamalib.php";

global $init;
        if(!isset($init)){
                $init = new Init();
        }
	$userid = getParam((isset($_GET['user']))?$_GET['user']:0);
	 if(!empty($userid) && $userid>0){
                        $init->initUser($userid);
                }
	$init->logoutUser();
	
/*
 * Unsetting session values, just in case.
 */

  unset($_SESSION['yamauser']);
  unset($_SESSION['yamacourse']);

/*
 * Closing session
 */
session_destroy();

header("Location: index.php");
exit();
?>
