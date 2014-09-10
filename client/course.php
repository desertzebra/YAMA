<?php
session_start();


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

  if($courseid<=0){
?>
<form id="course_form" action="course.php" method="get">
  <?php printCourseIdEl(0,$userid);?>
<button id="submit" name="submit" onclick="submitForm('course_form','course.php')">Submit</button>
</form>
</div>

<?php 
    include 'common/footer.php';
    exit();
  }else{
    /*
     * Safe to init the course now.
     */
    $init->initCourse($courseid);
  }
?>

<div id='menu'>
</div>
<div id='info' class="block">
<div class="block_head">Basic Info</div>

<?php
printCourse(null,$userid,false);
?>
<!--?php print 'JSON='.$init->getUserJsonStr(); ?-->
</div>
<div id='contents' class="block">
<?php
$contentList = $init->getCourseContents();
?>
<div class="block_head">Contents(<?php print count($contentList); ?>)</div>
<?php
foreach($contentList as $content){
printContent($content,$courseid,$userid);
echo '<div class="spacer"></div>';
echo '<div class="split"></div>';

}
?>
</div>
</div>
<?php

 include 'common/footer.php';
}//$userid > 0

?>

