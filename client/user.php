<?php include 'common/header.php'?>
        <?php
	global $init;
        ?>
<div id='main'>	
        <?php
        $userid = getParam((isset($_GET['user']))?$_GET['user']:'');
	print "<p>userid=$userid</p>";
        if(empty($userid)){
	print "<p>Checking active user id</p>";
            if(isset($init) && $init->checkActiveUser()){
 		print "<p>Loading Active user</p>";
            }else{
            ?>
        <form id="user_form" action="user.php" method="get">
        <label>User Id</label>
        <input type="text" name="user" value="" />
        <input type="submit" name="submit" value="load" />
        </form>
</div>
    </body>
</html>

            <?php
            die();
		}
        }else{
	    $init = new Init();
            print "<p>Loading user($userid)</p>";
            $init->initUser($userid);
        }
?>

<div id='menu'>
</div>
<div id='profile' class="block">
<div class="block_head">Profile</div>

<?php
printUser();
?>
<!--?php print 'JSON='.$init->getUserJsonStr(); ?-->
</div>
<div id='messages' class="block">
<div class="block_head">Messages</div>
<?php
$messageList = $init->getMessages();
foreach($messageList as $message){
printMessage($message);
}
?>
</div>
<div id='courses' class="block">
<div class="block_head">Courses</div>
<div class="table">
<?php
$courseList = $init->getCoursesForActiveUser();
foreach($courseList as $course){
printCourse($course,$userid,true);
}
?>
</div>
</div>
<?
//print "fetching active users name";
//        print $init->getUserAttr('fullname');
/*        $courseList = $this->getCoursesForActiveUser();
	foreach($courseList as $course){
            print $course->name;
	}
*/

?>
</div>
<?php include 'common/footer.php'?>

