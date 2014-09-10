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
        $quizid = getParam((isset($_GET['id']))?$_GET['id']:0);
        $courseid = getParam((isset($_GET['course']))?$_GET['course']:0);
        $userid = getParam((isset($_GET['user']))?$_GET['user']:0);
        $questionId = getParam((isset($_GET['question']))?$_GET['question']:'');
        $action = getParam((isset($_GET['action']))?$_GET['action']:'');
            if(!empty($userid) && $userid>0){
                $init->initUser($userid);
            }
            if(!empty($courseid) && $courseid>0){
                $init->initCourse($courseid);
            }
        if(empty($quizid)){
            ?>
        <div>No Associated quiz object<?php print $quizid;?></div>
        </div>
    </body>
</html>
            <?php
            die();
	}
        else{
            if(isset($init) && $init->checkActiveCourse() && $init->checkActiveUser()){
            $content = $init->getContent('quiz',$quizid);
            }else{
                print_error("illformed request. No valid attributes found");
            }
        }

        $cat = getParam((isset($_GET['cats']))?$_GET['cats']:'');
        $qtype = getParam((isset($_GET['qtype']))?$_GET['qtype']:'');
        $qorder = getParam((isset($_GET['qorder']))?$_GET['qorder']:-1);
        $name = getParam((isset($_GET['name']))?$_GET['name']:'');
        $text = getParam((isset($_GET['text']))?$_GET['text']:'');
        $score = getParam((isset($_GET['score']))?$_GET['score']:'');
        $penalty = getParam((isset($_GET['penalty']))?$_GET['penalty']:'');
        $generalfeedback = getParam((isset($_GET['generalfeedback']))?$_GET['generalfeedback']:'');
          if($qtype!=='random'){
            $opt_shuffleanswers = getParam((isset($_GET['shuffleanswers']))?1:0);
            $opt_correctfeedback = getParam((isset($_GET['correctfeedback']))?$_GET['correctfeedback']:'');
            $opt_incorrectfeedback = getParam((isset($_GET['incorrectfeedback']))?$_GET['incorrectfeedback']:'');
            $opt_partiallycorrectfeedback = getParam((isset($_GET['partiallycorrectfeedback']))?$_GET['partiallycorrectfeedback']:'');
            $opt_incorrectfeedback = getParam((isset($_GET['incorrectfeedback']))?$_GET['incorrectfeedback']:'');
            $opt_answernumbering = getParam((isset($_GET['answernumbering']))?$_GET['answernumbering']:'abc');
            $opt_single = getParam((isset($_GET['single']))?1:0);
        $ans_count = getParam((isset($_GET['ans_count']))?$_GET['ans_count']:0);
        $ans_params = array();
        $ansIte = 0;
        while($ansIte<=$ans_count){
          $answer= new stdClass();
          $answer->text = getParam((isset($_GET['ans_text_'.$ansIte]))?$_GET['ans_text_'.$ansIte]:'');
          $answer->fraction = getParam((isset($_GET['ans_fraction_'.$ansIte]))?$_GET['ans_fraction_'.$ansIte]:0);
          $answer->feedback = getParam((isset($_GET['ans_feedback_'.$ansIte]))?$_GET['ans_feedback_'.$ansIte]:'');
          array_push($ans_params, $answer);
          $ansIte++;
        }
}

if($action === 'delete'){
  if(!empty($questionId)){
    $content->deleteQuestion($questionId);
    print "<p>$questionId deleted</p>";
    print "</div>";
    die();
  }else{
    print "<p>No question to be delete</p>";
    print "</div>";
    die();

  }

}elseif($action==='add'){
  if($qtype==='page'){
      $content->addPage($qorder);
      $init->makeActive();
      $init->save();
  }
  elseif($cat!=='' && $qtype!=='' && $name!=='' && $text!==''){
          $qorder = $content->addQuestion($text,
           FORMAT_PLAIN, $qtype,$cat,$qorder,$name);
    print "<p>New Temp Question added at $qorder</p>";
    print "<p>Setting options for this question</p>";
    $options = new Question_Options();
    $options->shuffleanswers = $opt_shuffleanswers;
    $options->correctfeedback = $opt_correctfeedback;
    $options->incorrectfeedback = $opt_incorrectfeedback;
    $options->partiallycorrectfeedback = $opt_partiallycorrectfeedback;
    $options->answernumbering=$opt_answernumbering;
    $options->single = $opt_single;
    print "<p>Setting Answers for this question</p>";
    /*$answer_arr = array();
      $answer= new Answer_Content();
      $answer->text = "Sample Answer";
      array_push($answer_arr, $answer);
    */
    $options->answers = $ans_params;
    print "<p>Adding options to the question</p>";
    $content->setOptions($qorder,$options);
    print "<p>Saving Question</p>";
    $init->makeActive();
    $init->save();
    print "<p>Done</p>";
  }else{
    print "<p>Malformed request</p>";
  }
}

?>      
        <form id="question_form" name="question_form">  
        <input type="hidden" name="id" id="id" value="<?php print $quizid;?>" />
        <input type="hidden" name="course" id="course" value="<?php print $courseid;?>" />
        <input type="hidden" name="user" id="user" value="<?php print $userid;?>" />
        <input type="hidden" name="action" id="action" value="add" />

        <div class="form_item">
        <label>Categories</label>
        <select id="cats" name="cats">
          <?php
            $qcats = $content->getCategories();
            $randomCat = new stdClass();
            $randomCat->id='random';
            $randomCat->name='Random from the selected Category';
            array_push($qcats,$randomCat);
            foreach($qcats as $cat){
              echo '<option value="'.$cat->id.'">'.$cat->name.'</option>';
            }
          ?>
        </select>
        </div>
        <div class="form_item">
        <label>Question Type</label>
        <select id="qtype" name="qtype">
          <?php
            $qtypes = $content->getTypes();
            foreach($qtypes as $qtype){
              echo '<option value="'.$qtype.'">'.$qtype.'</option>';
            }
          ?>
        </select>
        </div>
        <div class="form_item">
        <label>Name</label>
        <input type="text" name="name" id="name" value="<?php print $name;?>" />
        </div>
        <div class="form_item">
        <label>Text</label>
        <textarea name="text" id="text"><?php print $text;?></textarea>
        </div>
        <div class="form_item">
        <label>Max Score</label>
        <input type="text" name="score" id="score" value="<?php print $score;?>" />
        </div>
        <div class="form_item">
        <label>Penalty</label>
        <input type="text" name="penalty" id="penalty" value="<?php print $penalty;?>" />
        </div>
        <div class="form_item">
        <label>General Feedback</label>
        <textarea name="generalfeedback" id="generalfeedback"><?php print $generalfeedback;?> </textarea>
        </div>
        <div class="spacer"></div>
        <div class="split"></div>
        <div id="options">

            <div class="form_item">
            <label>Shuffle Answers</label>
            <input type="checkbox" name="shuffleanswers" id="shuffleanswers" />
            </div>
            
        <div class="form_item">
        <label>Answer Numbering Style</label>
        <select id="answernumbering" name="answernumbering">
          <?php
            $numberingstyles = $content->getNumberingStyles();
            foreach($numberingstyles as $key=>$style){
              echo '<option value="'.$key.'">'.$style.'</option>';
            }
          ?>
        </select>
        </div>

            <div class="form_item">
            <label>Single Answer?</label>
            <input type="checkbox" name="single" id="single"/>
            </div>

            <div class="form_item">
            <label>Correct Feedback</label>
            <textarea name="correctfeedback" id="correctfeedback"><?php print $opt_correctfeedback; ?></textarea>
            </div>
            <div class="form_item">
            <label>Partially Correct Feedback</label>
            <textarea name="partiallycorrectfeedback" id="partiallycorrectfeedback"><?php print $opt_partiallycorrectfeedback; ?> </textarea>
            </div>
            <div class="form_item">
            <label>Incorrect Feedback</label>
            <textarea name="incorrectfeedback" id="incorrectfeedback"><?php print $opt_incorrectfeedback; ?></textarea>
            </div>


        </div>
        <div class="spacer split"></div>
        <div id="answers">
          <input type='hidden' id='ans_count' name='ans_count' value='1'/>
          <button type="button" id='add_ansfields' value='add_ansfields' onclick='addAnsFields()'>Add another answer</button>

          <div id='answer_0' name='answer_0'> 
            <label>1.</label>
            <div class="form_item">
            <label>Answer</label>
            <input type='text' name='ans_text_0' id='ans_text_0' />
            </div>
            <div class="form_item">
            <label>Fraction</label>
            <input type='text' name='ans_fraction_0' id='ans_fraction_0' />%
            </div>
            <div class="form_item">
            <label>Feedback</label>
            <textarea id='ans_feedback_0' name='ans_feedback_0'></textarea>
            </div>
          </div>
        </div>
        <div class="spacer split"></div>
        <div id="extra_fields">
        </div>
        <div class="form_item">
        <button type="button" id="submitQ" name="submitQ" onclick="submitForm('question_form','content/question.php','get')">Submit</button>
        </div>
        </form>

</div>
<?php include '../common/footer.php'?>

