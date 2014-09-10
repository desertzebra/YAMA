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
        $forumid = getParam((isset($_GET['id']))?$_GET['id']:0);
        $action = getParam((isset($_GET['action']))?$_GET['action']:'');
	$discussionid = getParam((isset($_GET['discussion']))?$_GET['discussion']:0);
	$postid = getParam((isset($_GET['post']))?$_GET['post']:0);   
        if(empty($forumid)){
            ?>
        <div>No Associated Forum object:<?php print $forumid;?></div>
        </div>
    </body>
</html>
            <?php
            die();
	}//empty($forumid)
        else{
            if(isset($init) && $init->checkActiveCourse() && $init->checkActiveUser()){
            $forum = $init->getContent('forum',$forumid);
            }else{
                print_error("malformed request. No valid forum found");
            }
        }//!empty($forumid)
        $text = getParam((isset($_GET['text']))?$_GET['text']:'');

$post = false;
$discussion = false;
if($discussionid>0){
    $discussion = $forum->getDiscussionById($discussionid);
//var_dump($discussion);
    if($postid>0){
        $post = $discussion->getPostById($postid);
    }//$postid>0
}//$discussionid>0
if($action === 'delete'){
//make the user active before deleting.
$init->makeActive();

  if($discussion){
    if($post){
      $discussion->delPost($post);
      print "<p>Post deleted</p>";
    }else{
      $forum->deleteDiscussionById($discussionid);
      print "<p>Discussion deleted</p>";
    }
  }else{
  print "<p>no discussion found for deletion</p>";
  }//no discussion
    $forum->save();
}//$action==delete
elseif($action==='add'){
//make the user active before add
$init->makeActive();

  if($text!=''){
    if($discussion){
      $discussion->addPost($text);
      $discussion->save();
    }else{
	print "Adding $text as a discussion in $forum->id";
      $forum->addDiscussion($text);
      $forum->save();
    }//no discussion
  
  }//action===add
}

if($discussion){
?>
<div id="discussions" class="block">
<div class="block_head"><?php print $discussion->text ?></div>
<?php
    printDiscussion($discussion,$forum->id,$courseid,$userid,false);
    echo '<div class="spacer"></div>';
    echo '<div class="split"></div>';
?>
</div><!-- discussions block -->

<?
}else{

?>
<button type="button" onclick="javascript:newPostForm(<?php print "'".$userid."','".$courseid."','".$forumid."','".$discussionid."'"; ?>)">Add a new discussion</button>

<div id="discussions" class="block">
<div class="block_head">Discussions</div>
<?php
//print_r($forum->discussions);
if(count($forum->discussions)<1){
  echo '<div>No discussions found. Start by adding a new one.</div>';
}else{
  foreach($forum->discussions as $key=>$discuss){
    printDiscussion($discuss,$forum->id,$courseid,$userid);
    echo '<div class="spacer"></div>';
    echo '<div class="split"></div>';
  }//foreach discussions
}//$forum->discussions>0
?>
</div><!-- discussions block -->

<?
}//no discussion, print the forum
?>

</div><!--main-->
<?php
include '../common/footer.php';
}//else sessions set
?>

