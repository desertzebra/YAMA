<?php include '../common/header.php'?>
        <?php
        ini_set('display_errors', '1');
        error_reporting(E_ALL);
	global $init;
	if(!isset($init)){
            print "<p>resetting global init object</p>";
		$init = new Init();
	}
        ?>
	<div id='main'>	
        <?php
        $forumid = getParam((isset($_GET['id']))?$_GET['id']:0);
        $courseid = getParam((isset($_GET['course']))?$_GET['course']:0);
        $userid = getParam((isset($_GET['user']))?$_GET['user']:0);
        $action = getParam((isset($_GET['action']))?$_GET['action']:'');
	$discussionid = getParam((isset($_GET['discussion']))?$_GET['discussion']:0);
	$postid = getParam((isset($_GET['post']))?$_GET['post']:0);   
            if(!empty($userid) && $userid>0){
                $init->initUser($userid);
            }
            if(!empty($courseid) && $courseid>0){
                $init->initCourse($courseid);
            }
        if(empty($forumid)){
            ?>
        <div>No Associated Forum object:<?php print $forumid;?></div>
        </div>
    </body>
</html>
            <?php
            die();
	}
        else{
            if(isset($init) && $init->checkActiveCourse() && $init->checkActiveUser()){
            $forum = $init->getContent('forum',$forumid);
            }else{
                print_error("malformed request. No valid forum found");
            }
        }
        $text = getParam((isset($_GET['text']))?$_GET['text']:'');

$post = false;
$discussion = false;
print "<p>discussion id =$discussionid</p>";
if($discussionid>0){
    $discussion = $forum->getDiscussionById($discussionid);
    if($postid>0){
        $post = $discussion->getPostById($postid);
    }
}
if($action === 'delete'){
//make the user active before deleting.
$init->makeActive();

  if($discussion){
    if($post){
      $discussion->delPost($post);
      print "<p>Post deleted</p>";
    }else{
      $forum->deleteDiscussion(findDiscussionById($discussionid));
      print "<p>Discussion deleted</p>";
    }
  }else{
  print "<p>no discussion found for deletion</p>";
  }

    die();
}


elseif($action==='add'){
//make the user active before add
$init->makeActive();

  if($text!=''){
    if($discussion){
      $discussion->addPost($text);
    }else{
	print "Adding $text as a discussion in $forum->id";
      $forum->addDiscussion($text);
    }
  
    print "<p>Saving forum</p>";
    $forum->save();
  }
}
?>

<button type="button" onclick="javascript:newPostForm(<?php print "'".$userid."','".$courseid."','".$forumid."','".$discussionid."'"; ?>)">Add a new discussion</button>
<!--	<div id='newPost'>
	<form id="post_form" name="post_form" action="">
        <input type="hidden" name="id" id="id" value="<?php print $forumid;?>" />
        <input type="hidden" name="course" id="course" value="<?php print $courseid;?>" />
        <input type="hidden" name="user" id="user" value="<?php print $userid;?>" />
        <input type="hidden" name="action" id="action" value="add" />
	<div class="form_item">
        <label>Text</label>
        <textarea cols="100" rows="5" name="text" id="text"><?php print $text;?></textarea>
        </div>
	 <div class="form_item">
<button type="button" id="submitP" name="submitP" onclick="submitForm('post_form','content/discussion.php','get')">Submit</button>
	</div>
	</form>
	</div>
-->


<?php
  

?>
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
  }
}
?>
</div><!-- discussions block -->
</div><!--main-->
<?php


include '../common/footer.php'

?>

