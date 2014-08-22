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
        $quizid = getParam((isset($_GET['id']))?$_GET['id']:'');
       $courseid = getParam((isset($_GET['course']))?$_GET['course']:'');
        $userid = getParam((isset($_GET['user']))?$_GET['user']:0);
        $shuffle = getParam((isset($_GET['shuffle']))?$_GET['shuffle']:0);
        $attempt = getParam((isset($_GET['attempt']))?$_GET['attempt']:-1);
        $page = getParam((isset($_GET['page']))?$_GET['page']:0);

        if(empty($quizid)){
            ?>
        <form id="quiz_form" action="content/quiz.php" method="get">
        <label>Quiz Id</label>
        <input type="text" name="id" id="id" value="" />
	<?php printCourseIdEl($courseid);?>
        <?php printUserIdEl($userid); ?>
        <button type='button' id="submit" name="submit" onclick="submitForm('quiz_form','content/quiz.php')">Submit</button>
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
print "<p>$quizid</p>";
            $content = $init->getContent('quiz',$quizid);
        }
?>

<div id='menu'>
</div>

<?php
$content->startAttempt();
?>
<div id="main">
      <div id="questions" class="block">
        <div class="block_head">Questions</div>
         <?php
           $questionList = $content->getQuestionsView($page,$attempt);
           echo '<div class="table">';
             foreach($questionList as $question){
               printQuestionView($question);
               echo '<div class="spacer"></div>';
               echo '<div class="split"></div>';
             }
           echo '</div>';
         ?>
     </div>
</div>
<?php include '../common/footer.php'?>

