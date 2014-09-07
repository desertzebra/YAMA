<?php include 'common/header.php'?>
        <?php
        require_once('config_client.php');
        ini_set('display_errors', '1');
        error_reporting(E_ALL);
        require_once $CFG_YAMAAPI->yamadir."/Init.php";
	global $init;
	if(!isset($init)){
		$init = new Init();
	}
        ?>
	<div id='main'>	
        <?php
        $courseid = getParam((isset($_GET['course']))?$_GET['course']:'');
	$userid = getParam((isset($_GET['user']))?$_GET['user']:0);
        if(empty($courseid)){
            ?>
        <form id="course_form" action="course.php" method="get">
        <?php printCourseIdEl();?>
	<?php printUserIdEl(); ?>
	<button id="submit" name="submit" onclick="submitForm('course_form','course.php')">Submit</button>
        </form>
</div>
    </body>
</html>

            <?php
            die();
	
        }else{
		if(!empty($userid) && $userid>0){
	    		$init->initUser($userid);
		}
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
<?php include 'common/footer.php'?>

