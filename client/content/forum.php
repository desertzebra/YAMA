<?php include '../common/header.php'?>
        <?php
        include_once('../config_client.php');
        ini_set('display_errors', '1');
        error_reporting(E_ALL);
        include_once $CFG_YAMAAPI->yamadir."/Init.php";
	global $init;
	if(!isset($init)){
            print "<p>resetting global init object</p>";
		$init = new Init();
	}
        ?>
	<div id='main'>	
        <?php
        $forumid = getParam((isset($_GET['id']))?$_GET['id']:'');
       $courseid = getParam((isset($_GET['course']))?$_GET['course']:'');
        $userid = getParam((isset($_GET['user']))?$_GET['user']:0);

        if(empty($forumid)){
            ?>
        <form id="forum_form" action="content/forum.php" method="get">
        <label>Forum Id</label>
        <input type="text" name="id" id="id" value="" />
	<?php printCourseIdEl();?>
        <?php printUserIdEl(); ?>
        <button id="submit" name="submit" onclick="submitForm('forum_form','content/forum.php')">Submit</button>
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
<?php include '../common/footer.php'?>

