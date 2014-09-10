<?php


function getParam($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function shorten($content){
  if(strlen($content)>200){
    $pos=strpos($content, ' ', 200);
    $content = substr($content,0,$pos );
    $content .= "...";
  }
  return $content;
}

function xml_escape($s)
{
    $s = html_entity_decode($s, ENT_QUOTES, 'UTF-8');
    $s = htmlspecialchars($s, ENT_QUOTES, 'UTF-8', false);
    return $s;
}
function hrTime($unixTime){
  if(empty($unixTime)){
    return '';
  }
  return date('d.m.y \a\t G:ia',$unixTime);
}
function getUserAttr($user=null,$attr='fullname'){
  global $init;
  if(isset($user)){
    return xml_escape($user->$attr);
  }else{
    return xml_escape($init->getUserAttr($attr));
  }
}
function printUser($user=null,$mini=false){
  global $init;
//  if(isset($user)){
$userid = getUserAttr($user,'id');
    echo '<div class="hidden">'.$userid.' </div>';
    if($mini){
        echo "<div class='spacer'>";
      echo '<button class="" type="button" onclick="toggleDiv(\'user_details_'.$userid.'\')">'.getUserAttr($user,'fullname');
      echo '</button>';
      echo '</div>';

    }else{
    echo '<div class="item"><label>fullname</label>'.getUserAttr($user,'fullname').'</div>';
    }
      echo '<div id="user_details_'.$userid.'" class="panel '.(($mini===true)?"hidden":"").'">';
        echo '<div class="item"><label>username</label>'.getUserAttr($user,'username').'</div>';
        echo '<div class="item"><label>country</label>'.getUserAttr($user,'country').'</div>';

        echo '<div class="item"><label>city</label>'.getUserAttr($user,'city').'</div>';
        echo '<div class="item"><label>description</label>'.getUserAttr($user,'description').'</div>';
        echo '<div class="item"><label>email</label>'.getUserAttr($user,'email').'</div>';
        echo '<div class="item"><label>Timezone</label>'.getUserAttr($user,'timezone').'</div>';
        echo '<div class="item"><label>lang</label>'.getUserAttr($user,'lang').'</div>';
      echo '</div>';
/*  }else{
    $userid = $init->getUserAttr('id');
    echo '<div class="hidden">'.$userid.'</div>';
    echo '<div class="item"><label>fullname</label>'.xml_escape($init->getUserAttr('fullname')).'</div>';
    if(!$mini){
      echo '<div class="item"><label>username</label>'.xml_escape($init->getuserAttr('username')).'</div>';
      echo '<div class="item"><label>country</label>'.$init->getUserAttr('country').'</div>';
      echo '<div class="item"><label>city</label>'.$init->getUserAttr('city').'</div>';
      echo '<div class="item"><label>description</label>'.xml_escape($init->getUserAttr('description')).'</div>';
      echo '<div class="item"><label>email</label>'.$init->getUserAttr('email').'</div>';
      echo '<div class="item"><label>Timezone</label>'.$init->getUserAttr('timezone').'</div>';
      echo '<div class="item"><label>lang</label>'.$init->getUserAttr('lang').'</div>';
    }
  }
*/
}
function printUserForm(){
global $init;
$userid = $init->getUserAttr('id');
echo '<input type="hidden" name="id" value="'.(empty($userid))?0:$userid.'" />';
echo '<label>fullname</label><input type="text" name="fullname" value="'.$init->getUserAttr('fullname').'" />';
echo '<label>username</label><input type="text" name="username" value="'.$init->getUserAttr('username').'" />';
echo '<label>country</label><input type="text" name="user" value="'.$init->getUserAttr('country').'" />';
echo '<label>city</label><input type="text" name="user" value="'.$init->getUserAttr('city').'" />';
echo '<label>description</label><input type="text" name="user" value="'.$init->getUserAttr('description').'" />';
echo '<label>email</label><input type="text" name="user" value="'.$init->getUserAttr('email').'" />';
echo '<label>Timezone</label><input type="text" name="user" value="'.$init->getUserAttr('timezone').'" />';
echo '<label>lang</label><input type="text" name="user" value="'.$init->getUserAttr('lang').'" />';
}
function printCourseIdEl($courseid=0,$userid=0){
global $init;
  if($init->checkActiveUser()){
    $courseList = $init->getCoursesForActiveUser();
    if(empty($courseList) || count($courseList)<1){
      echo '<div id="form_item"><label>Course Id</label>';
      echo '<input type="text" id="course" name="course" value="'.(($courseid>0)?$courseid:'').'" /></div>';
    }else{
      echo '<div id="courses" class="block">'.
           '<div class="block_head">Select a Course</div>'.
           '<select name="course" id="course">';
           foreach($courseList as $course){
             echo '<option value="'.$course->id.'" '.(($courseid==$course->id)?'selected':'').'>'.$course->name.'</option>';
           }
      echo '</select>'.
           '</div>';
    }
  }else{
    echo '<div class="error"> Only Logged in users can view the courses.</div>';
  }
}
function printUserIdEl($userid=0){
echo '<div id="form_item"><label>User Id</label>';
echo '<input type="text" id="user" name="user" value="'.(($userid>0)?$userid:'').'" /></div>';
}

function printMessage($message){
  echo '<div id="message" class="message read_'.$message->isRead.'">';
  echo '<label>'.$message->id.'</label>';
  echo '<div class="spacer"></div>';
  echo '<div id="sender" class="subblock"><div class="subblock_head">Sender</div>';
  printUser($message->owner,true);
  echo '</div>';
  echo '<div id="target" class="multi subblock"><div class="subblock_head">Target</div>';
  foreach($message->target as $userTo){
    printUser($userTo,true);
    echo '<div class="split"></div>';
  }
  echo '</div>';
  echo '<div class="spacer"></div>';
  echo '<label>'.xml_escape($message->subject).'</label>';
  echo '<div class="text">'.xml_escape($message->text).'</div>';
  echo '';
  echo '</div>';
  echo '<div class="split"></div>';
}

function printCourse($course=null,$userid='',$mini=false){
  global $init;
  echo '<div id="Course" class="result_item">';
  if(isset($course)){
    //print_r(get_object_vars($course));
    if($mini){
      echo '<div class="name ">'.$course->name.'</div>';
      echo '<div class="desc text ">'.shorten(xml_escape($course->summary)).'</div>';

        if(isset($userid) && !empty($userid)){
          echo "<div class='result_item_options'><button class='button' type='button' id='coursedetails_$course->id' onclick='getPage(\"course.php?course=$course->id&amp;user=$userid\")'>Details</button></div>";
        }
    }
    else{
      echo '<div class="item"><label>Name</label>'.xml_escape($course->name).'</div>';
      echo '<div class="item"><label>Shortname</label>'.$course->shortname.'</div>';
      echo '<div class="item"><label>Category ID</label>'.$course->categoryid.'</div>';
      echo '<div class="item"><label>Summary</label><div id="text">'.xml_escape($course->summary).'</div></div>';
      echo '<div class="item"><label>Format</label>'.$course->format.'</div>';
      echo '<div class="item"><label>Start Time</label>'.$course->startTime.'</div>';
      echo '<div class="item"><label>End Time</label>'.$course->endTime.'</div>';
      echo '<div class="item"><label>Time Created</label>'.hrTime($course->timecreated).'</div>';
      echo '<div class="item"><label>Time Modified</label>'.hrTime($course->timemodified).'</div>';
      echo '<div class="item"><label>Language</label>'.$course->lang.'</div>';
    }
  }else{
    $courseid = $init->getCourseAttr('id');
    if($mini){
      echo '<div class="result_item_value">'.$init->getCourseAttr('name').'</div>';
      echo "<div><button class='button' type='button' id='coursedetails_$courseid' onclick='getPage(\"course.php?course=$courseid\")'>Details</button></div>";

    }else{
      echo '<div class="item"><label>Name</label>'.xml_escape($init->getCourseAttr('name')).'</div>';
      echo '<div class="item"><label>Shortname</label>'.$init->getCourseAttr('shortname').'</div>';
      echo '<div class="item"><label>Category ID</label>'.$init->getCourseAttr('categoryid').'</div>';
      echo '<div class="item"><label>Summary</label><div id="text">'.xml_escape($init->getCourseAttr('summary')).'</div></div>';
      echo '<div class="item"><label>Format</label>'.$init->getCourseAttr('format').'</div>';
      echo '<div class="item"><label>Start Time</label>'.$init->getCourseAttr('startTime').'</div>';
      echo '<div class="item"><label>End Time</label>'.$init->getCourseAttr('endTime').'</div>';
      echo '<div class="item"><label>Time Created</label>'.hrTime($init->getCourseAttr('timecreated')).'</div>';
      echo '<div class="item"><label>Time Modified</label>'.hrTime($init->getCourseAttr('timemodified')).'</div>';
      echo '<div class="item"><label>Language</label>'.$init->getCourseAttr('lang').'</div>';

    }

  }
  echo '</div>';
  echo '<div id="split"></div>';
}

function printContent($content,$courseid='',$userid=''){
  global $init;
  if(!isset($content->id)){
    echo '<div>Content id not found. Content skipped.</div>';
    echo '<div class="spacer"></div>';
    echo '<div class="split"></div>';
    return;
  }
  echo '<div id="content">';
  echo '<div class="item"><label>id</label>'.$content->id.'</div>';
  if(isset($content->module_id)){
    echo '<div class="item"><label>module_id</label>'.$content->module_id.'</div>';
  }
  if(isset($content->section_num) && isset($content->sectionid)){
    echo '<div class="item"><label>Section Num</label>'.$content->section_num.'</div>';
    echo '<div class="item"><label>Section Id</label>'.$content->sectionid.'</div>';
  }

  if(isset($content->name)){
    echo '<div class="item"><label>name</label>'.xml_escape($content->name).'</div>';
  }
  if(isset($content->subject)){
    echo '<div class="item"><label>subject</label>'.xml_escape($content->subject).'</div>';
  }
  if(isset($content->text)){
    echo '<div class="item"><label>text</label>'.xml_escape($content->text).'</div>';
  }
  if(isset($content->format)){
    echo '<div class="item"><label>format</label>'.$content->format.'</div>';
  }
echo '<div class="item"><label>timeCreated</label>'.hrTime($content->timeCreated).'</div>';
echo '<div class="item"><label>Time Limit</label>'.hrTime($content->startTime).' - '.hrTime($content->endTime).'</div>';
echo '<div class="item"><label>Last Access</label>'.hrTime($content->lastAccess).'</div>';
if(isset($content->parent)){
	echo '<div class="item"><label>Parent</label>'.$content->parent.'</div>';
}
  echo '<div class="item"><label>Type</label>';
    if(isset($content->content_type)){
      echo $content->content_type;
    }else{
      echo 'Not set';
    }
  echo '</div>';
  echo '<div class="spacer"></div>';
  echo '<div id="owner" class="subblock">';
  echo '<div class="subblock_head">owner';
  echo '</div>';
  printUser($content->owner, true);
  echo '</div>';
  if(isset($content->target) && count($content->target)>0){
    echo '<div id="target" class="multi subblock">';
    echo '<div class="subblock_head">Target</div>';
    foreach($content->target as $userTo){
      printUser($userTo,true);
      echo '<div class="split"></div>';
    }
    echo '</div>';
    echo '<div class="spacer"></div>';
  }

  if(isset($content->children)&& count($content->children)>0){
    foreach($content->children as $child){ 
        echo '<div class="item"><label>'.$child->name.'</label>'.printContent($child,$courseid,$userid).'</div>';
	echo '<div class="spacer"></div>';
	echo '<div class="split"></div>';

    }
  }
  if(isset($courseid) && !empty($courseid) && isset($userid) && !empty($userid))
  {
    echo "<div><button class='button' type='button' id='contentdetails_".$content->content_type."_".$content->id."' onclick='getContentDetails(\"content/$content->content_type\",\"$content->id\",\"$courseid\",\"$userid\")'>Details</button></div>";
  }
  echo '</div>';
}

function printForumDetails($forum){
  if(!isset($forum->id)){
    echo '<div> Forum Id not found</div>';
  }else{
    echo '<div id="forum_details">';
    if(!empty($forum->assessed)){
      echo '<div class="item"><label>Assessed</label><p>'.$forum->assessed.'</p>';
      echo '<div>'.hrTime($forum->assesstimestart).' - '.hrTime($forum->assesstimefinish).'</div>';
      echo '</div>';
    }
    echo '<div class="item"><label>Rating Time</label>'.hrTime($forum->ratingtime).'</div>';
    echo '<div class="item"><label>Scale</label>'.$forum->scale.'</div>';
    echo '</div>';
  }
  echo '<div class="spacer"></div>';
  echo '<div class="split"></div>';
}

function printPost($post){
  if(!isset($post->id)){
    echo '<div>Post('.$post->id.') not found.</div>';
  }else{
    echo '<div class="result_item">';
    printContent($post);
    echo '<div class="spacer"></div>';
    echo '<div class="tag">';
    echo 'last edited by '.getUserAttr($post->modifiedBy);
    if(isset($post->modifiedAt)){
      echo ' at '.hrTime($post->modifiedAt);
    }
    echo '</div>';
    echo '</div>';
  }
}

function printDiscussion($discuss,$forumid='',$courseid='',$userid='',$mini=true){
  global $init;
  if(!isset($discuss->id)){
    echo '<div>Discussion id not found.</div>';
    return;
  }
  echo '<div id="discuss_'.$discuss->id.'">';
  printPost($discuss);
  echo '<div class="item"><label>Number of posts</label>'.count($discuss->posts).'</div>';
  echo "<button type='button' onclick='javascript:newPostForm(\"$userid\",\"$courseid\",\"$forumid\",\"$discuss->id\")'>Add a new Post</button>";

  echo '<div class="spacer"></div>';
  if($mini){
    echo '<div class="table">';
    $firstPost = $discuss->get_first_post();
    if(!isset($firstPost)){
      echo '<p>No first post found by Moodle function. Looking at records..</p>';
      $firstPost = $discuss->posts[0];
    }
    printPost($firstPost);
    echo '</div>';
  }else{
  }
  if(empty($forumid)){
    if(isset($discuss->forumId)){
      $forumid = $discuss->forumId;
      echo '<div class="item"><label>Forum Id</label>'.$discuss->forumId.'</div>';
    }
  }
    if($mini && !empty($courseid) && !empty($forumid) && !empty($userid))
    {
      echo "<div><button class='button' type='button' id='discussiondetails".$discuss->id."' onclick='getDiscussionDetails(\"$discuss->id\",\"$forumid\",\"$courseid\",\"$userid\")'>More...</button></div>";
    }
  
  echo '</div>';
  
}
function printQuizDetails($quiz){
//print_r($quiz);
echo '<div class="spacer"></div>';
  if(!isset($quiz->id)){
    echo '<div> Quiz Id not found</div>';
  }else{
    echo '<div id="forum_details">';
    if(isset($password)){
      echo '<div class="item"><label>Password</label>'.$quiz->password.'</div>';
    }
    echo '<div class="item"><label>Grade Method</label>'.$quiz->grademethod.'</div>';
    echo '<div class="item"><label>Sum Grades</label>'.$quiz->sumgrades.'</div>';
    echo '<div class="item"><label>Most recent attempt at</label>'.hrTime($quiz->recentAttemptAt).'</div>';
    echo '<div class="item"><label>Time Limit</label>'.$quiz->timelimit.'</div>';
    echo '<div class="item"><label>Allowed Retries</label>'.(($quiz->allowed_attempts==0)?'Unlimited':$quiz->allowed_attempts).'</div>';
    
    if(isset($quiz->subnet)){
      echo '<div class="item"><label>Scale</label>'.$quiz->subnet.'</div>';
    }
    if(!empty($quiz->delay1) && !empty($quiz->delay2)){
      echo '<div class="item"><label>delay1</label>'.$quiz->delay1.'</div>';
      echo '<div class="item"><label>delay2</label>'.$quiz->delay2.'</div>';
    }
    printQuizFormat($quiz->quizformat);
    if(!empty($quiz->quizformat->overduehandling))
    echo '</div>';
  }
  echo '<div class="spacer"></div>';
  echo '<div class="split"></div>';
}
function printQuizFormat($qformat){
  echo '<div class="spacer"></div>';
  echo '<div id="quiz_details">';
    echo '<div class="item"><label>Overdue Handling</label>'.$qformat->overduehandling.'</div>';
    echo '<div class="item"><label>Grace Period</label>'.$qformat->graceperiod.'</div>';
    echo '<div class="item"><label>Preferred Behaviour</label>'.$qformat->preferredbehaviour.'</div>';
    echo '<div class="item"><label>Decimal Points</label>'.$qformat->decimalpoints.'</div>';
    echo '<div class="item"><label>Question Decimal Points</label>'.$qformat->questiondecimalpoints.'</div>';
    echo '<div class="item"><label>Review Attempt</label>'.$qformat->reviewattempt.'</div>';
    echo '<div class="item"><label>Review Correctness</label>'.$qformat->reviewcorrectness.'</div>';
    echo '<div class="item"><label>Review Marks</label>'.$qformat->reviewmarks.'</div>';
    echo '<div class="item"><label>Review Specific Feedback</label>'.$qformat->reviewspecificfeedback.'</div>';
    echo '<div class="item"><label>Review General Feedback</label>'.$qformat->reviewgeneralfeedback.'</div>';
    echo '<div class="item"><label>Review Right Answer</label>'.$qformat->reviewrightanswer.'</div>';
    echo '<div class="item"><label>Review Overall Feedback</label>'.$qformat->reviewoverallfeedback.'</div>';
    echo '<div class="item"><label>Questions per page</label>'.$qformat->questionsperpage.'</div>';
    echo '<div class="item"><label>Nav Method</label>'.$qformat->navmethod.'</div>';
    echo '<div class="item"><label>Shuffle Questions?</label>'.(($qformat->shufflequestions)?'Yes':'No').'</div>';
    echo '<div class="item"><label>Shuffle Answers?</label>'.(($qformat->shuffleanswers)?'Yes':'No').'</div>';
  echo '</div>';
}
function printQuestion($question,$contentid,$userid=0,$courseid=0,$mini=false){
  if(!isset($question->id)){
    echo '<div> Question Id not set</div>';
  }elseif(intval($question->id,10)===0){
    echo "<div class='subblock_head'>Page Break</div>";
  }else{
    echo '<div class="result_item" id="question_'.$question->id.'">';
    echo '<label>'.(isset($question->qorder)?$question->qorder:$question->id).': '.$question->name.'</label>';
   echo '<div class="spacer"></div>';

    echo '<div class="result_item_options"><ul>';
if($userid>0 && $courseid>0){
echo "<li><button class='button' type='button' id='del_question' name='del_question' onclick='getDiv(\"new\",\"content/question.php?question=$question->id&id=$contentid&user=$userid&course=$courseid&delete=1\")'>Delete</button></li>";
}
    echo '<li><button class="details" type="button" id="question_toggleDetails_'.$question->id.' name="question_toggleDetails_'.$question->id.'" onclick="toggleDiv(\'question_details_'.$question->id.'\')">Details...</button></li>';


    echo '</ul></div>';
      echo '<div class="text">('.$question->qtype.') '.$question->text.'</div>';
      echo '<div id="question_details_'.$question->id.'" name="question_details_'.$question->id.'" class="hidden">';
      printContent($question);
      echo '<div class="item"><label>Penalty</label>'.$question->penalty.'</div>';
      echo '<div class="item"><label>Default Score</label>'.$question->defaultmark.'</div>';
      echo '<div class="item"><label>General Feedback</label><div class="text">'.xml_escape($question->generalfeedback).'</div></div>';
      echo '<div class="tag">Stamp('.$question->stamp.'),Version('.$question->version.')</div>';
      printCategory($question->category,true);
      if($question->qtype!=='random'){
        printQuestionOptions($question->options);
      }
    
    echo '</div>';
    echo '</div>';
  }
    
}
function printQuestionOptions($qoptions){
    echo '<div class="item hidden"><label>Option Id</label>'.$qoptions->id.'</div>';
    echo '<div class="item"><label>Synchronize</label>'.$qoptions->synchronize.'</div>';
    echo '<div class="item"><label>Single</label>'.$qoptions->single.'</div>';
    echo '<div class="item"><label>Shuffle Answers</label>'.$qoptions->shuffleanswers.'</div>';
    echo '<div class="item"><label>Correct Feedback</label><div class="text">'.xml_escape($qoptions->correctfeedback).'</div></div>';
    echo '<div class="item"><label>Incorrect Feedback</label><div class="text">'.xml_escape($qoptions->incorrectfeedback).'</div></div>';
    echo '<div class="item"><label>Partially Correct Feedback</label><div class="text">'.xml_escape($qoptions->partiallycorrectfeedback).'</div></div>';
    echo '<div class="item"><label>Answer Numbering</label>'.xml_escape($qoptions->answernumbering).'</div>';
  if(isset($qoptions->answers) && count($qoptions->answers)>0){
  echo '<div class="spacer section"><div class="section_head">Answers</div>';
    foreach($qoptions->answers as $answer){
      printAnswer($answer);
      echo '<div class="spacer"></div>';
      echo '<div class="split"></div>';

    }
    echo '<div class="spacer"></div>';
    echo '</div>';
  }

  if($qoptions->showunits){
    echo '<div class="spacer section"><div class="section_head">Units</div>';
    echo '<div class="item"><label>Units</label>'.$qoptions->units.'</div>';
    echo '<div class="item"><label>Unit Grading Type</label>'.$qoptions->unitgradingtype.'</div>';
    echo '<div class="item"><label>Unit Penalty</label>'.$qoptions->unitpenalty.'</div>';
    echo '<div class="item"><label>Units Left</label>'.$qoptions->unitsleft.'</div>';


    echo '</div>';   
  }

}
function printAnswer($answer){
  if(!isset($answer->id)){
    echo '<div>No answer id set</div>';
  }else{
    echo '<div id="answer_'.$answer->id.'">';
    echo '<div class="item"><label>Text</label>'.$answer->text.'</div>';
    echo '<div class="item"><label>Fraction</label>'.$answer->fraction.'</div>';
    echo '<div class="item"><label>Answer</label>'.$answer->feedback.'</div>';
    echo '<div class="item"><label>Tolerance</label>'.$answer->tolerance.'</div>';
    echo '<div class="item"><label>Correct Answer Length</label>'.$answer->correctanswerlength.'</div>';
    echo '</div>';
  }
}
function printCategory($category,$mini=false){
if(!isset($category->id)){
  echo '<div>Category Id not set</div>';
  return;
}
if($mini){
  echo '<div class="tag">'.$category->name.'</div>';
  return;
}else{
  echo '<div id="category_'.$category->id.'">';
    echo '<div class="result_caption">';
      echo ''.$category->name.'';
    echo '</div>';
    echo '<div class="item">'.$category->text.'</div>';
    echo '<div class="result_container">';
    echo '<div class="table">';
      $questionList = $category->getQuestions();
      if(count($questionList)>0){
        echo '<div class="result_head">Questions('.count($questionList).')</div>';
      }
      foreach($questionList as $question){
        echo '<div class="result_item">';
          printQuestion($question,0,0,0,true);
        echo '</div>';
        echo '<div class="spacer"></div>';
        echo '<div class="split"></div>';

      }
    echo '</div>';
    echo '</div>';
  echo '</div>';
}
//print_r($category);
}
function printQuestionView($question){
  echo '<div>';
  echo $question;
  echo '</div>';
}
?>
