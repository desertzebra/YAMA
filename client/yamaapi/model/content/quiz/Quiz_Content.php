<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Quiz_Content
 *
 * @author desertzebra
 */
ini_set('display_errors', '1');
error_reporting(E_ALL);
require_once "config.php";
global $CFG;
require_once "$CFG_YAMA->yamadir/model/Content_Model.php";
require_once "$CFG_YAMA->yamadir/model/Context_Model.php";
require_once "$CFG_YAMA->moodledir/mod/quiz/lib.php";
require_once "$CFG_YAMA->moodledir/mod/quiz/locallib.php";
require_once "$CFG_YAMA->moodledir/mod/quiz/editlib.php";
require_once "$CFG_YAMA->moodledir/question/type/questiontypebase.php";

require_once "$CFG_YAMA->yamadir/model/content/quiz/Multi_Question_Content.php";
require_once "$CFG_YAMA->yamadir/model/content/quiz/Multi_Question_Category_Content.php";

class Quiz_Format {

    public $overduehandling;
    public $graceperiod;
    public $preferredbehaviour;
    public $decimalpoints;
    public $questiondecimalpoints;
    public $reviewattempt;
    public $reviewcorrectness;
    public $reviewmarks;
    public $reviewspecificfeedback;
    public $reviewgeneralfeedback;
    public $reviewrightanswer;
    public $reviewoverallfeedback;
    public $questionsperpage;
    public $navmethod;
    public $shufflequestions;
    public $shuffleanswers;

    function populate($quiz) {
        if (isset($quiz->overduehandling)) {
            $this->overduehandling = $quiz->overduehandling;
            unset($quiz->overduehandling);
        }
        if (isset($quiz->graceperiod)) {
            $this->graceperiod = $quiz->graceperiod;
            unset($quiz->graceperiod);
        }
        if (isset($quiz->preferredbehaviour)) {
            $this->preferredbehaviour = $quiz->preferredbehaviour;
            unset($quiz->preferredbehaviour);
        }
        if (isset($quiz->decimalpoints)) {
            $this->decimalpoints = $quiz->decimalpoints;
            unset($quiz->decimalpoints);
        }
        if (isset($quiz->questiondecimalpoints)) {
            $this->questiondecimalpoints = $quiz->questiondecimalpoints;
            unset($quiz->questiondecimalpoints);
        }
        if (isset($quiz->reviewattempt)) {
            $this->reviewattempt = $quiz->reviewattempt;
            unset($quiz->reviewattempt);
        }
        if (isset($quiz->reviewcorrectness)) {
            $this->reviewcorrectness = $quiz->reviewcorrectness;
            unset($quiz->reviewcorrectness);
        }
        if (isset($quiz->reviewmarks)) {
            $this->reviewmarks = $quiz->reviewmarks;
            unset($quiz->reviewmarks);
        }
        if (isset($quiz->reviewspecificfeedback)) {
            $this->reviewspecificfeedback = $quiz->reviewspecificfeedback;
            unset($quiz->reviewspecificfeedback);
        }
        if (isset($quiz->reviewgeneralfeedback)) {
            $this->reviewgeneralfeedback = $quiz->reviewgeneralfeedback;
            unset($quiz->reviewgeneralfeedback);
        }
        if (isset($quiz->reviewoverallfeedback)) {
            $this->reviewoverallfeedback = $quiz->reviewoverallfeedback;
            unset($quiz->reviewoverallfeedback);
        }
        if (isset($quiz->reviewrightanswer)) {
            $this->reviewrightanswer = $quiz->reviewrightanswer;
            unset($quiz->reviewrightanswer);
        }
        if (isset($quiz->questionsperpage)) {
            $this->questionsperpage = $quiz->questionsperpage;
            unset($quiz->questionsperpage);
        }
        if (isset($quiz->navmethod)) {
            $this->navmethod = $quiz->navmethod;
            unset($quiz->navmethod);
        }
        if (isset($quiz->shufflequestions)) {
            $this->shufflequestions = $quiz->shufflequestions;
            unset($quiz->shufflequestions);
        } else {
            $this->shufflequestions = false;
        }
        if (isset($quiz->shuffleanswers)) {
            $this->shuffleanswers = $quiz->shuffleanswers;
            unset($quiz->shuffleanswers);
        } else {
            $this->shuffleanswers = false;
        }
    }

    function __get($name) {
        $func = 'get_' . $name;
        if (method_exists($this, $func)) {
            return $this->$func();
        } else {
            return $this->$name;
        }
    }

    function __set($name, $value) {
        $func = 'set_' . $name;
        if (method_exists($this, $func)) {
            return $this->$func($value);
        } else {
            $this->$name = $value;
        }
    }

    function __toString() {
        $ret_quiz_str = "\r\n";
        $ret_quiz_str .= "Over Due Handling = $this->overduehandling \r\n";
        $ret_quiz_str .= "Grace Period = $this->graceperiod \r\n";
        $ret_quiz_str .= "Preferred Behaviour = $this->preferredbehaviour \r\n";
        $ret_quiz_str .= "Decimal Points = $this->decimalpoints \r\n";
        $ret_quiz_str .= "Question Decimal Points = $this->questiondecimalpoints \r\n";
        $ret_quiz_str .= "Review Attempt = $this->reviewattempt \r\n";
        $ret_quiz_str .= "Review Correctness = $this->reviewcorrectness \r\n";
        $ret_quiz_str .= "Review Marks = $this->reviewmarks \r\n";
        $ret_quiz_str .= "Review Specific Feedback = $this->reviewspecificfeedback \r\n";
        $ret_quiz_str .= "Review General Feedback = $this->reviewgeneralfeedback \r\n";
        $ret_quiz_str .= "Review Overall Feedback = $this->reviewoverallfeedback \r\n";
        $ret_quiz_str .= "Review Right Answers = $this->reviewrightanswer \r\n";
        $ret_quiz_str .= "Questions Per Page = $this->questionsperpage \r\n";
        $ret_quiz_str .= "Navigation Method = $this->navmethod \r\n";
        $ret_quiz_str .= "Shuffle Questions = $this->shufflequestions \r\n";
        $ret_quiz_str .= "Shuffle Answers = $this->shuffleanswers \r\n";

        return $ret_quiz_str;
    }

}

class Quiz_Content extends Content_Model {

    protected $courseid;
    protected $cm_obj;
    protected $allowed_attempts;
    protected $userGrades;
    public $timelimit;
    public $recentAttemptAt;
    public $questions;
    protected $volMulQuestionView;
    protected $volMulQuestionObj;
    public $sumgrades;
    public $grademethod;
    protected $maxGrade;
    public $password;
    public $subnet;
    public $delay1;
    public $delay2;
    protected $categories;
    protected $quizformat;

    function __construct() {
        $this->cm_obj = null;
        $this->content_type = "quiz";
        $this->quizformat = new Quiz_Format();
        $this->volMulQuestionObj = new Multi_Question_Content();
        $this->volMulQuestionView = array();
        $this->userGrades = array();
        $this->categories = new Multi_Question_Category_Content();
        parent::__construct();
    }

    function populate($quiz) {
        //print_r($quiz);
        if (isset($quiz->course)) {
            if (is_numeric($quiz->course)) {
                $this->courseid = $quiz->course;
                unset($quiz->course);
            } else if (is_object($quiz->course)) {
                $this->courseid = $quiz->course->id;
                unset($quiz->course);
            }
        } else if (isset($quiz->courseid)) {
            $this->courseid = $quiz->courseid;
            unset($quiz->courseid);
        }

        if (isset($quiz->intro)) {
            $this->text = $quiz->intro;
            unset($quiz->intro);
        } else if (isset($quiz->text)) {
            $this->text = $quiz->text;
            unset($quiz->text);
        }
        if (isset($quiz->timeopen)) {
            $this->startTime = $quiz->timeopen;
            unset($quiz->timeopen);
        }
        if (isset($quiz->timeclose)) {
            $this->endTime = $quiz->timeclose;
            unset($quiz->timeclose);
        }

        if (isset($quiz->timelimit)) {
            $this->timelimit = $quiz->timelimit;
            unset($quiz->timelimit);
        } else {
            $this->timelimit = 0;
        }

        if (isset($quiz->attemptonlast)) {
            $this->recentAttemptAt = $quiz->attemptonlast;
            unset($quiz->attemptonlast);
        }
	if(isset($quiz->attempts)){
	    $this->allowed_attempts = $quiz->attempts;
	    unset($quiz->attempts);
	}
	if (isset($quiz->grademethod)) {
            $this->grademethod = quiz_get_grading_option_name($quiz->grademethod);
            unset($quiz->grademethod);
        }
        if (isset($quiz->sumgrades)) {
            $this->sumgrades = $quiz->sumgrades;
            unset($quiz->sumgrades);
        }
        if (isset($quiz->grade)) {
            $this->maxGrade = $quiz->grade;
            unset($quiz->grade);
        }
        if (isset($quiz->password)) {
            $this->password = $quiz->password;
            unset($quiz->password);
        }
        if (isset($quiz->subnet)) {
            $this->subnet = $quiz->subnet;
            unset($quiz->subnet);
        }
        if (isset($quiz->delay1)) {
            $this->delay1 = $quiz->delay1;
            unset($quiz->delay1);
        }
        if (isset($quiz->delay2)) {
            $this->delay2 = $quiz->delay2;
            unset($quiz->delay2);
        }

        if (isset($quiz->cm)) {
            $this->cm = $quiz->cm;
            //print_r($this->cm);
            if (!$this->cm_obj = get_coursemodule_from_id('quiz', $this->cm)) {
                print_error('invalidcoursemodule');
            }
            unset($quiz->cm);
        }
        if (isset($quiz->questions)) {
            $questionIds = explode(",", $quiz->questions);
            foreach ($questionIds as $id) {
                $this->volMulQuestionObj->add($id);
            }
            $this->questions = $quiz->questions;
            unset($quiz->questions);
        }

        $this->quizformat->populate($quiz);
        parent::populate($quiz);
    }

    /*
     * 0 for all users
     */

    function getUserGrades($userid = 0) {
        if (!isset($this->id)) {
            print_error("No quiz id($this->id) set");
            return false;
        }
        $grades_arr = quiz_get_user_grades($this, $userid);
        if (!isset($this->decimalpoints)) {
            $this->decimalpoints = 0;
        }
        //print "\r\ngrades:\r\n";
        //print_r($grades_arr);
        foreach ($grades_arr as $grade) {
            $grade_float = quiz_format_grade($this, $grade);
        }
    }

    /*
     * $type=all|finished|unfinished
     */

    function getUserAttempts($userid, $type = 'all') {
        if (empty($this->id)) {
            print_error("No quiz id found \r\n");
        }
        return quiz_get_user_attempts($this->id, $userid, $type);
    }

    function get_courseid() {
        if (!isset($this->courseid)) {
            if (!$quiz_raw = $DB->get_record("quiz", array("id" => $this->id))) {
                print_error('invalidquizid', 'quiz');
            }
            if (!isset($quiz_raw->course)) {
                print_error("\r\n in load(): quiz course not set.\r\n");
                return -1;
            }
            $this->courseid = $quiz_raw->course;
        }
        return $this->courseid;
    }

    function get_cm_obj() {
        if (!isset($this->cm_obj)) {
            if (isset($this->cm)) {
                if (!$this->cm_obj = get_coursemodule_from_id('quiz', $this->cm)) {
                    //print "$this->cm";
                    print_error('invalidcoursemodule');
                    return null;
                }
            } else {
                $courseid = $this->get_courseid();
                if (!empty($courseid)) {
                    if (!$this->cm_obj = get_coursemodule_from_instance("quiz", $this->id, $this->get_courseid())) {
                        print_error('incorrect parameter quiz id or course id');
                        return null;
                    }
                } else {
                    return null;
                }
            }
        }
        return $this->cm_obj;
    }

    function addQuestion($text, $textFormat = FORMAT_PLAIN, $qtype, $category, $qorder = -1, $name = "", $parent = 0, $penalty = false, $defaultmark = 0, $generalFeedback = "", $generalFeedbackFormat = FORMAT_PLAIN, array $otherfields = null) {
        global $CFG_YAMA;
	if(Model::loadclass('Question_Content_'.$qtype,"$CFG_YAMA->yamadir/model/content/quiz/Question_Content_$qtype.php")){
	  print "<p>file found Question_Content_".$qtype."</p>";
	  $questionClass = "Question_Content_".$qtype;
	  $question = new $questionClass();
	}else{
	  $question = new Question_Content();
	}
        $question->text = $text;
        $question->category = $category;
        $question->quizformat = $textFormat;
        $question->qtype = $qtype;
        if ($qorder !== -1) {
            $question->qorder = $qorder;
        }
        $question->name = $name;
        $question->parent = $parent;
        $question->penalty = ($penalty) ? 0 : 1;
        $question->defaultmark = $defaultmark;
        $question->generalfeedback = $generalFeedback;
        $question->generalfeedbackformat = $generalFeedbackFormat;
        if ($otherfields != null) {
            foreach ($otherfields as $key => $value) {
                $question->$key = $value;
            }
        }

        return $this->addQuestionObj($question);
    }

    function addPage($qorder = -1) {
        print "<p>Adding Page</p>";
        $question = new Question_Content();
        $question->id = 0;
        $question->type = 'new_page';
        if ($qorder !== -1) {
            $question->qorder = $qorder;
        }
        return $this->volMulQuestionObj->addQuestion($question);
    }

    function addQuestionObj($questionObj) {
	global $CFG_YAMA;
	if(!isset($questionObj->qtype)){
            return -1;
	}
	if(Model::loadclass('Question_Content_'.$questionObj->qtype,"$CFG_YAMA->yamadir/model/content/quiz/Question_Content_$questionObj->qtype.php")){
          print "<p>file found Question_Content_".$questionObj->qtpye."</p>";
          $questionClass = "Question_Content_".$questionObj->qtype;
          $question = new $questionClass();
        }else{
          $question = new Question_Content();
        }
        $question->populate($questionObj);
print "<p>Storing question</p>";
print_r($question);
        $qorder = $this->volMulQuestionObj->addQuestion($question);
        return $qorder;
    }

    function setOptions($qorder, $options) {
        if (empty($options)) {
            $options = new stdClass();
        }
        return $this->volMulQuestionObj->setOptions($qorder, $options);
    }

    function getQuestions() {
        return $this->volMulQuestionObj->getQuestions();
    }

    function saveQuestions() {
        $questionList = $this->volMulQuestionObj->getQuestions();
        foreach ($questionList as $question) {
	print_r($question);
            $questionId = $this->volMulQuestionObj->saveQuestion($question);
	        if(is_numeric($questionId)){
		    quiz_add_quiz_question($questionId, $this);
		}else{
		    return $questionId;
		}
        }
    }

    function deleteQuestions() {
        $questionList = $this->volMulQuestionObj->getQuestionsToDelete();
        foreach ($questionList as $question) {
            $questionId = $this->volMulQuestionObj->delQuestion($question);
            if (!empty($questionId)) {
                quiz_remove_question($this, $questionId);
            }
        }
    }

    function save() {
        global $CFG;
        /* if(!$this->state === STATE_SAVE){
          print_error("State does not indicate any saving. Save Mode=".$this->state);
          return false;
          }
         * 
         */
        print "<p>Converting to MOODLE Object</p>\r\n";
        $this->toMoodleObj();
        print "<p>Saving questions</p>\r\n";
        $errors = $this->saveQuestions();
	if(!empty($errors)){
	    print "<p>errors encountered while saving question</p>";
	    print_r($errors);
	    return;
	}
        print "Shuffling Questions in Quiz\r\n";
        $this->setQuestionsInQuiz();
        if ($this->maxGrade >= 0) {
            quiz_set_grade($this->maxGrade, $this);
        }
        quiz_update_sumgrades($this);
        quiz_update_sumgrades($this);
        quiz_update_all_attempt_sumgrades($this);
        quiz_update_all_final_grades($this);
        quiz_update_grades($this, 0, true);
        //print "\r\nMoodle Quiz Object\r\n";
        //print_r($this);
    }

    function setQuestionsInQuiz() {
        global $DB;
        if ($this->shufflequestions) {
            $this->shuffleQuestions();
        }
    }

    function reload() {
        if (isset($this->questions)) {
            $this->volMulQuestionObj->clear();
            $questionIds = explode(",", $this->questions);
            foreach ($questionIds as $id) {
                $this->volMulQuestionObj->add($id);
            }
            $this->volMulQuestionObj->quiz_id = $this->id;
            $this->volMulQuestionObj->load();
            $this->loadQuestionView();
        }
    }

    function shuffleQuestions() {
        $this->questions = quiz_repaginate($this->questions, $this->questionsperpage);
        $DB->set_field('quiz', 'questions', $this->questions, array('id' => $this->id));
    }

    function toMoodleObj() {
        $this->course = $this->courseid;
        $this->intro = $this->text;
        if (!empty($this->startTime)) {
            $this->timeopen = $this->startTime;
        } else {
            $this->timeopen = time();
        }
        if (!empty($this->endTime)) {
            $this->timeclose = $this->endTime;
        } else {
            $this->timeclose = time() + 3600;
        }
        if (empty($this->timelimit)) {
            $this->timelimit = $this->timeclose - $this->timeopen;
        }
	$this->attempts = $this->allowed_attempts;
        $this->grade = $this->maxGrade;
        if (!isset($this->questions)) {
            $this->questions = $this->volMulQuestionObj->getIdsInStr();
        }
        $this->questionsperpage = isset($this->format->questionsperpage) ? $this->foramt->questionsperpage : 10;
        //$this->questions = $this->questions->getIdsInStr();
        //print("questions=".$this->questions);
        $quizformat = (array) $this->quizformat;
        foreach ($quizformat as $formatKey => $formatValue) {
            $this->$formatKey = $formatValue;
        }
    }

    function load() {
        global $DB, $USER;
        //print_r($this);
        if ($this->requiresSave()) {
            $this->save();
        }
        if (!$this->requiresLoad()) {
            return;
        }
        $this->state = self::STATE_LOAD;
        if ($this->get_cm_obj() !== null) {
            if (!$quiz_raw = $DB->get_record("quiz", array("id" => $this->cm_obj->instance))) {
                print_error('invalidquizid', 'quiz');
            }
        } else {
            if (!isset($this->id)) {
                print_error("\r\n in load(): quiz id not set.\r\n");
                return;
            }

            if (!$quiz_raw = $DB->get_record("quiz", array("id" => $this->id))) {
                print_error('invalidquizid', 'quiz');
            }
            if (!isset($quiz_raw->course)) {
                print_error("\r\n in load(): quiz course not set.\r\n");
                return;
            }
            if (!$course = $DB->get_record("course", array("id" => $quiz_raw->course))) {
                print_error('coursemisconf');
                return;
            }
            if (!$this->cm_obj = get_coursemodule_from_instance("quiz", $quiz_raw->id, $course->id)) {
                print_error('incorrect parameter quiz id or course id');
                return;
            }
        }
        $this->populate($quiz_raw);

        $this->volMulQuestionObj->quiz_id = $this->id;
        //print "Quiz Id =". $this->questions->quiz_id."\r\n";
        $this->volMulQuestionObj->load();
        /*
         * Quiz Contexts
         */
        $context = new Context_Model(true);
        $context->id = $this->cm_obj->id;
        $context->load();

        $this->categories->addContext($context);

        //print "Loading all categories\r\n";
        $this->categories->load();

        /*
         * Loading question view with sample data
         */
        $this->loadQuestionsView();

        parent::load();
    }

    function loadQuestionsView() {
        
        $this->quba = question_engine::make_questions_usage_by_activity('mod_quiz', $this->cm_obj);
        $this->quba->set_preferred_behaviour($this->quizformat->preferredbehaviour);
        $this->idstoslots = array();
        
        /*
         * Converting to Moodle Quiz Object
         */
        //$this->toMoodleObj();

        /*
         * processing all questions
         */
        $questionObj_arr = $this->volMulQuestionObj->getQuestions();
        $_questionIdList = explode(',', $this->questions);
        foreach ($questionObj_arr as $questionObj) {
            if ($questionObj->id == 0) {
                //print "<p>Page break skipping</p>";
                continue;
            }
//print "<p>$questionObj->qtype,$questionObj->id</p>";
            $questionObj->toMoodleObj();
//print "<p>Converted to Moodle format</p>";
            if ($questionObj->qtype !== 'random') {
                $question = question_bank::make_question($questionObj);
            } else {
                $shuffleAns = (isset($questionObj->options->shuffleanswers) ? true : false);
                $question = question_bank::get_qtype('random')->choose_other_question($questionObj, $_questionIdList, $shuffleAns);
                if (is_null($question)) {
                    print_r($question);
                    print_error("Not enough question in the category");
                }
            }
            $this->idstoslots[$questionObj->id] = $this->quba->add_question($question, $questionObj->maxmark);
            $_questionIdList[] = $questionObj->id;

        }
    }

    function startAttempt($userid = 0) {
        global $DB, $USER;
        if ($userid === 0) {
            if (!isset($USER)) {
                print_error("No User id specified and no active user\r\n");
                return false;
            } else {
                $userid = $USER->id;
            }
        }
        
        // Create the new attempt and initialize the question sessions
        $timenow = time(); // Update time now, in case the server is running really slowly.
	$page = 0;
        $attempts = $this->getUserAttempts($userid);
        $lastattempt = end($attempts);

	if ($lastattempt && ($lastattempt->state == quiz_attempt::IN_PROGRESS ||
        $lastattempt->state == quiz_attempt::OVERDUE)) {
	    $currentattemptid = $lastattempt->id;

	    // If the attempt is now overdue, deal with that.
	    $quizobj->create_attempt_object($lastattempt)->handle_if_time_expired($timenow, true);

	    // And, if the attempt is now no longer in progress, redirect to the appropriate place.
	    if ($lastattempt->state == quiz_attempt::ABANDONED || $lastattempt->state == quiz_attempt::FINISHED) {
        	echo "<p>Attempt Finished</p>";
		return;
    	    }
        	$page = $lastattempt->currentpage;
} else {
    while ($lastattempt && $lastattempt->preview) {
        $lastattempt = array_pop($attempts);
    }

    // Get number for the next or unfinished attempt.
    if ($lastattempt) {
        $attemptnumber = $lastattempt->attempt + 1;
    } else {
        $lastattempt = false;
        $attemptnumber = 1;
    }
    $currentattemptid = null;
}

if ($currentattemptid) {
    if ($lastattempt->state == quiz_attempt::OVERDUE) {
	echo "<p>Quiz Overdue</p>";
	return;
    } else {
	$this->attempt = $lastattempt;
	return;
    }
}

	/*
	 * Converting to Moodle Obj
	 */ 
	$this->toMoodleObj();

	// Delete any previous preview attempts belonging to this user.
	quiz_delete_previews($this, $userid);

        /*
         * Creating Moodle Quiz object
         */

        $quizobj = quiz::create($this->cm_obj->instance, $userid);

        /*
         * Creating attempt
         */
        $this->attempt = quiz_create_attempt($quizobj, $attemptnumber, $lastattempt, $timenow, true);
        if ($this->attempt->preview) {
            $variantoffset = rand(1, 100);
        } else {
            $variantoffset = $attemptnumber;
        }
        $this->quba->start_all_questions(
                new question_variant_pseudorandom_no_repeats_strategy($variantoffset), $timenow);

        // Update attempt layout.
        $newlayout = array();
        foreach (explode(',', $this->attempt->layout) as $qid) {
            if ($qid != 0) {
                $newlayout[] = $this->idstoslots[$qid];
            } else {
                $newlayout[] = 0;
            }
        }
        $this->attempt->layout = implode(',', $newlayout);

        print "<p>Closing all previously open attempts</p>";
        
        //closing all open quiz attempts
        $DB->set_field('quiz_attempts', 'state', quiz_attempt::FINISHED, array('quiz' => $this->id, 'userid' => $userid));

        $transaction = $DB->start_delegated_transaction();
        question_engine::save_questions_usage_by_activity($this->quba);
        $this->attempt->uniqueid = $this->quba->get_id();
        $this->attempt->id = $DB->insert_record('quiz_attempts', $this->attempt);
        // Trigger event.
        $eventdata = new stdClass();
        $eventdata->component = 'mod_quiz';
        $eventdata->attemptid = $this->attempt->id;
        $eventdata->timestart = $this->attempt->timestart;
        $eventdata->timestamp = $this->attempt->timestart;
        $eventdata->userid = $this->attempt->userid;
        $eventdata->quizid = $this->id;
        $eventdata->cmid = $this->cm;
        $eventdata->courseid = $this->get_courseid();
        events_trigger('quiz_attempt_started', $eventdata);

        $transaction->allow_commit();
    }

    function getQuestionsView($page = 0,$attemptid=-1) {
        if(empty($attemptid)){
            if(is_object($this->attempt) && !empty($this->attempt->id)){
                $attemptid = $this->attempt->id;
            }else{
                print_error("Attempt id(".$this->attempt->id.") not specified.");
                return null;
            }
        }
        // Get the list of questions needed by this page.
        $attemptobj = quiz_attempt::create($attemptid);
        $slots = $attemptobj->get_slots($page);
        $output = array();
        foreach ($slots as $slot) {
            $output[] = $attemptobj->render_question($slot, false);
//$attemptobj->attempt_url($slot, $page));
        }
        return $output;
    }

    function getCategories() {
        if (empty($this->categories)) {
            return "No Categories associated with this quiz\r\n";
        }
        return $this->categories->getCategories();
    }

    function getNumberingStyles() {
        $numberingoptions = question_bank::get_qtype('multichoice')->get_numbering_styles();
        return $numberingoptions;
    }

    function getTypes() {
        $type_str = array();
        foreach (question_bank::get_creatable_qtypes() as $qtypename => $qtype) {
            array_push($type_str, $qtypename);
        }
        return $type_str;
    }

    function getType($qtype) {
        return question_bank::get_qtype($qtype);
    }

    function __get($name) {
        $func = 'get_' . $name;
        if (method_exists($this, $func)) {
            return $this->$func();
        } else {
            return $this->$name;
        }
    }

    function __set($name, $value) {
        $func = 'set_' . $name;
        if (method_exists($this, $func)) {
            return $this->$func($value);
        } else {
            $this->$name = $value;
        }
    }

    function __toString() {
        $ret_quiz_str = "***************************\r\n"
                . "Quiz=" . parent::__toString() . "\r\n";
        $ret_quiz_str .= "---------------------------\r\n"
                . "\tFormat=" . $this->quizformat . "\r\n";
        $ret_quiz_str .= "---------------------------\r\n";
        $ret_quiz_str .= "Available From = $this->startTime \r\n";
        $ret_quiz_str .= "Available To = $this->endTime \r\n";
        $ret_quiz_str .= "Time Limit = $this->timelimit \r\n";
        $ret_quiz_str .= "Attempts = $this->allowed_attempts \r\n";
        $ret_quiz_str .= "Most Recent Attempt Time = $this->recentAttemptAt \r\n";
        $ret_quiz_str .= "Questions: $this->volMulQuestionObj";
        $ret_quiz_str .= "Grade Method = $this->grademethod \r\n";
        $ret_quiz_str .= "Sum Grades = $this->sumgrades \r\n";
        $ret_quiz_str .= "Grade = $this->maxGrade \r\n";
        $ret_quiz_str .= "Password = $this->password \r\n";
        $ret_quiz_str .= "Subnet = $this->subnet \r\n";
        $ret_quiz_str .= "Delay 1 = $this->delay1 \r\n";
        $ret_quiz_str .= "Delay 2 = $this->delay2 \r\n";
        $ret_quiz_str .= "Categories = $this->categories \r\n";
        $ret_quiz_str = "***************************\r\n";
        return $ret_quiz_str;
    }

}
