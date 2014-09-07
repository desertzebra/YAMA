        <?php
	global $init;
        if(!isset($init)){
                $init = new Init();
        }
	$userid = getParam((isset($_GET['user']))?$_GET['user']:0);
	 if(!empty($userid) && $userid>0){
                        $init->initUser($userid);
                }
	$init->logoutUser();

?>
