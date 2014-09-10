<?php
session_start();
$username = (isset($_POST['username']))?$_POST['username']:'';
$password = (isset($_POST['pass']))?$_POST['pass']:'';
$errMsg  = "";
if(isset($_SESSION['yamauser'])){
header("Location: index.php?sessionhas=".$_SESSION['yamauser']);
            die();
}

if(!empty($username) && !empty($password)){
include_once('/var/www/yama/client/config_client.php');
ini_set('display_errors', '1');
error_reporting(E_ALL);
$path = $CFG_YAMAAPI->yamadir;
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
include_once $CFG_YAMAAPI->yamadir."/Init.php";
include_once $CFG_YAMAAPI->clientdir."/yamalib.php";
global $init;
        
	    $init = new Init();
            $errMsg = $init->loginUser($username,$password);
            if(!empty($errMsg)){
                print_r($errMsg);
            }
  	  
            /*
             * Save against session fixation attack
             */
	    session_regenerate_id (true);
            
	    /*
             * User logged in so storing in session
             */
            $_SESSION['yamauser'] = $init->getUserAttr('id');
            session_write_close();

//var_dump($_SESSION);
	    header("Location: login.php?sessionhas=".$_SESSION['yamauser']."&yamaid=".$init->getUserAttr('id'));
	    die();
	    
}
else if(!empty($errMsg) || empty($username) || empty($password)){
?>
<?php include 'common/header.php';?>
  <div id='main'>
    <div><?php print $errMsg; ?></div>
    
    <div class="block" id='loginDiv'>
      <div class="block_head">Login</div>
        <div>
        <form id="user_form" action="login.php" method="post">

           <div class="form_item">
                <label>Username</label>
                <input type="text" name="username" id="username" value="" />
           </div>
           <div class="form_item">
                <label>Password</label>
                <input type="password" name="pass" id="pass" value="" />
           </div>
           <div class="form_item">
                <input type="submit" name="submit" value="login" />
           </div>

        </form>
        </div>
      </div> <!-- Login block -->
    </div><!-- main div -->
<?php 
    include 'common/footer.php';

}

?>

