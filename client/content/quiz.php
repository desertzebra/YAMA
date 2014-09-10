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
        $quizid = getParam((isset($_GET['id']))?$_GET['id']:'');
       $courseid = getParam((isset($_GET['course']))?$_GET['course']:'');
        $userid = getParam((isset($_GET['user']))?$_GET['user']:0);
        $shuffle = getParam((isset($_GET['shuffle']))?$_GET['shuffle']:0);

        if(empty($quizid)){
            ?>
        <form id="quiz_form" action="content/quiz.php" method="get">
        <label>Quiz Id</label>
        <input type="text" name="id" id="id" value="" />
	<?php printCourseIdEl();?>
        <?php printUserIdEl(); ?>
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
print "<p>Userid=$userid</p>";
                        $init->initUser($userid);
                }
print "<p>init Course=$courseid</p>";
            $init->initCourse($courseid);
//print "<p>$quizid</p>";
            $content = $init->getContent('quiz',$quizid);
        }
?>

<div id='menu'>
</div>
  <div id='info' class="block">
    <div class="block_head">Basic Info</div>

<?php
printContent($content);
printQuizDetails($content);
echo '<div class="spacer"></div>';
echo '<div class="split"></div>';

?>
  </div>
    <div id="legacy">
      <div id="questions" class="minblock">
        <div class="block_head">Questions</div>
        <div class="block_options">
          <ul>
	    <li>
              <?php
                echo "<button type='button' id='preview' name='preview' onclick='getContentDetails(\"content/quiz_attempt\",\"$content->id\",\"$courseid\",\"$userid\",\"&amp;action=preview\")'>Preview</button>";
              ?>
            </li>
            <li>
              <?php
                echo "<button type='button' id='new_question' name='new_question' onclick='getContentDetails(\"content/question\",\"$content->id\",\"$courseid\",\"$userid\",\"&amp;qtype=page&amp;qorder=2&amp;action=add\")'>Add a new Page</button>";
              ?>
            </li>
            <!--li-->
              <?php
              //echo "<button type='button' id='shuffle' name='shuffle' onclick='getDiv(\"questions\",\"content/quiz.php?shuffle=1&id=$content->id&user=$userid&course=$courseid",\"get\")'>Shuffle</button>";
              ?>
             <!--/li-->
             <li>
               <?php
                 echo "<button type='button' id='new_question' name='new_question' onclick='getContentDetails(\"content/question\",\"$content->id\",\"$courseid\",\"$userid\")'>Add a new Question</button>";
               ?>
             </li>
           </ul>
         </div>
         <?php
           $questionList = $content->getQuestions($userid);
           echo '<div class="table">';
             foreach($questionList as $question){
               printQuestion($question,$content->id,$userid,$courseid,true);
               echo '<div class="spacer"></div>';
               echo '<div class="split"></div>';
             }
           echo '</div>';
         ?>
       </div>
       <div id="Categories" class="minblock" style="float:right;">
         <div class="block_head">Categories</div>
         <?php
           $categoryList = $content->getCategories();
           foreach($categoryList as $category){
             printCategory($category);
             echo '<div class="spacer"></div>';
             echo '<div class="split"></div>';
           }
         ?>
       </div>
     </div>
</div>
<?php include '../common/footer.php'?>

