<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <?php
        require_once('config.php');
        ini_set('display_errors', '1');
        error_reporting(E_ALL);
        require_once $CFG_YAMAAPI->yamadir."/Init.php";
        $init = new Init();
        ?>
        <title></title>
    </head>
    <body>
        <?php
        $courseid = filter_input($_GET['course']);
        if(empty($courseid)){
            if(empty($init->active_course->id)){
            ?>
        <form id="course_form" action="course.php"  method="get">
        <label>Course Id</label>
        <input type="text" name="course" value="">
        <input type="submit" name="submit" value="load">
        </form>
        
            <?php
            }else{
                print "<p>Loading Active Course</p>";
            }
        }else{
            print "<p>Loading Course($courseid)</p>";
            $init->initCourse($courseid);
        }
        print $init->active_course;
        


?>

    </body>
</html>
