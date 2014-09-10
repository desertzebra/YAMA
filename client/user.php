<?php include 'common/header.php'?>
        <?php
	global $init;
        ?>
<div id='main'>	
        <?php
        $username = getParam((isset($_POST['username']))?$_POST['username']:'');
        $password = getParam((isset($_POST['pass']))?$_POST['pass']:'');
	$errMsg  = "";

        if(!empty($username) && !empty($password)){
	    $init = new Init();
            $errMsg = $init->loginUser($username,$password);
            if(!empty($errMsg)){
                print_r($errMsg);
            }

        }
	if(!empty($userid)){
		$init = new Init();
		$init->initUser($userid);
	}	
        else if(!empty($errMsg) || empty($username) || empty($password)){
?>
	<div><?php print $errMsg; ?></div>
	<div>User login required</div>
</div>
    </body>
</html>

<?php
         die();
         }
$userid = $init->getUserAttr('id');


	/*
	 * User authenticated
	 */

	//print "<p>Checking active user id</p>";
            if(isset($init) && $init->checkActiveUser()){
 		//print "<p>Loading Active user</p>";
       	    
?>

<div id='menu'>
</div>
<div id='profile' class="block">
<div class="block_head">Profile</div>

<?php
printUser();
?>
<!--?php print 'JSON='.$init->getUserJsonStr(); ?-->
</div> <!-- Profile -->


<?php
$messageList = $init->getMessages();
if(!empty($messageList) && count($messageList)>0){
?>
<div id='messages' class="block">
<div class="block_head">Messages</div>
<?php
	foreach($messageList as $message){
		printMessage($message);
	}
?>
</div> <!-- Messages -->

<?php } ?>


<?php
$courseList = $init->getCoursesForActiveUser();
if(!empty($courseList) && count($courseList)>0){
?>

<div id='courses' class="block">
	<div class="block_head">Courses</div>
	<div class="table">
	<?php
	foreach($courseList as $course){
		printCourse($course,$userid,true);
	}
	?>
	</div>
</div> <!-- Courses -->
<?
}
//print "fetching active users name";
//        print $init->getUserAttr('fullname');
/*        $courseList = $this->getCoursesForActiveUser();
	foreach($courseList as $course){
            print $course->name;
	}
*/
}
?>
</div>
<?php include 'common/footer.php'?>

