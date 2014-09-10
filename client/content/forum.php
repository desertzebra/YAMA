<?php
session_start();

if(!isset($_SESSION['yamauser'])){
  header("Location: login.php");
  exit();
}
else if(!isset($_SESSION['yamacourse'])){
  header("Location: course.php");
  exit();
}
else{
  include '../common/header.php';
  global $init;

  $init = new Init();
  $init->initUser($userid);
  $init->initCourse($courseid);

?>
  <div id='main'>	
  <?php
    $forumid = getParam((isset($_GET['id']))?$_GET['id']:'');
    if(empty($forumid)){
  ?>
      <form id="forum_form" action="content/forum.php" method="form">
      <label>Forum Id</label>
      <input type="text" name="id" id="id" value="" />
      <?php printCourseIdEl();?>
      <button id="submit" name="submit" onclick="submitForm('forum_form','content/forum.php')">Submit</button>
      </form>
      </div>
  <?php
      include '../common/footer.php';
      die();
    }else{
      if(!empty($userid) && $userid>0){
        $init->initUser($userid);
      }
      $init->initCourse($courseid);
      $content = $init->getContent('forum',$forumid);
    }
?>

<div id='menu'>
</div>
<div id='info' class="block">
<div class="block_head">Basic Info</div>

<?php
printContent($content,$courseid,$userid);
printForumDetails($content);
echo '<div class="spacer"></div>';
echo '<div class="split"></div>';

?>
<!--?php print 'JSON='.$init->getUserJsonStr(); ?-->
</div>
<div id="discussions" class="block">
<div class="block_head">Discussions</div>
	<div class="block_options">
          <ul>
            <li>
            <?php
                echo "<button type='button' id='new_discuss' name='new_discuss' onclick='getContentDetails(\"content/discussion\",\"$content->id\",\"$courseid\",\"$userid\",\"&amp;action=add\")'>Add a new Discussion</button>";
              ?>

	    </li>
	</ul>
	</div>
<?php
foreach($content->discussions as $key=>$discuss){
printDiscussion($discuss,$content->id,$courseid,$userid);
echo '<div class="spacer"></div>';
echo '<div class="split"></div>';

}
?>
</div>
</div>
<?php 
include '../common/footer.php';
}

?>

