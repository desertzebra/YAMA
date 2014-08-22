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
        $userid = filter_input($_GET['user']);
        if(empty($userid)){
            if(empty($init->active_user->id)){
            ?>
        <form id="user_form" action="user.php" method="get">
        <label>User Id</label>
        <input type="text" name="user" value="">
        <input type="submit" name="submit" value="load">
        </form>
        
            <?php
            }else{
                print "<p>Loading Active user</p>";
            }
        }else{
            print "<p>Loading user($userid)</p>";
            $init->initUser($userid);
        }
        print $init->active_user;
        print_r($this->getCoursesForActiveUser());


?>

    </body>
</html>
