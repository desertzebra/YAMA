<?php include '../common/header.php'?>
        <?php
        require_once('../config_client.php');
        ini_set('display_errors', '1');
        error_reporting(E_ALL);
        require_once $CFG_YAMAAPI->yamadir."/Init.php";
	global $init;
	if(!isset($init)){
            print "<p>resetting global init object</p>";
		$init = new Init();
	}
        ?>
	<div id='main'>	
        <?php
        $discussionid = getParam((isset($_GET['id']))?$_GET['id']:'');
        $forumid = getParam((isset($_GET['forum']))?$_GET['forum']:'');
        $courseid = getParam((isset($_GET['course']))?$_GET['course']:'');
        $userid = getParam((isset($_GET['user'])?$_GET['user']:0));

        if(empty($forumid)){
            ?>
        <form id="discuss_form" action="content/discussion.php" method="get">
        <label>Discussion Id</label>
        <input type="text" name="id" id="id" value="" />
        <label>Forum Id</label>
        <input type="text" name="forum" id="forum" value="" />
	<?php printCourseIdEl();?>
        <?php printUserIdEl(); ?>
        <button id="submit" name="submit" onclick="submitForm('discuss_form','content/discussion.php')">Submit</button>
        </form>
</div>
    </body>
</html>

            <?php
            die();
	}
        else{
                if(!empty($userid) && $userid>0){
                        $init->initUser($userid);
                }
            $init->initCourse($courseid);
            $content = $init->getContent('forum',$forumid);
            $discussion = $content->getDiscussionById($discussionid);
        }
?>

<div id='menu'>
</div>
<div id='info' class="block">
<div class="block_head">Basic Info</div>

<?php
printContent($discussion,$courseid,$userid);
printDiscussion($discussion,$forumid,$courseid,$userid,false);
echo '<div class="spacer"></div>';
echo '<div class="split"></div>';

?>
<!--?php print 'JSON='.$init->getUserJsonStr(); ?-->
</div>
<div id="discussions" class="block">
<div class="block_head">Discussions</div>
<?php
foreach($discussion->posts as $key=>$post){
printPost($post);
echo '<div class="spacer"></div>';
echo '<div class="split"></div>';

}
?>
</div>
</div>
<?php include '../common/footer.php'?>

