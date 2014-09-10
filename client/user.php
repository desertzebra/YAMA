<?php
session_start();
//var_dump($_SESSION);
if(!isset($_SESSION['yamauser'])){
  header("Location: login.php");
  exit();
}
else{
  include 'common/header.php';
  global $init;

  $init = new Init();
  $init->initUser($userid);

?>


<div id='main'>	
<?php
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
  }// check active user
}//userid>0 
?>
</div>
<?php include 'common/footer.php'?>

