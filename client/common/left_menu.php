<?php
        $courseid = (isset($_GET['course']))?$_GET['course']:'';
        $userid = (isset($_GET['user']))?$_GET['user']:0;
?>

<div class="block nav" id='nav_opts'>
	<div class="block_head">Navigation Options</div>
	<ul>
		<li><div>
<?php
if($userid>0){
echo '<button name="load_u" id="load_u" onclick="getPage(\''.$CFG_YAMAAPI->clientcontext.'/user.php?user='.$userid.'\')">Load Profile</button>';
}
else{
echo '<button name="load_u" id="load_u" onclick="getPage(\''.$CFG_YAMAAPI->clientcontext.'/user.php\')">Load User</button>';
}
?>
			</div>
		</li>
		<li><div>
<?php 
if($courseid!=='' &&  $userid>0){
echo '<button name="load_c" id="load_c" onclick="getPage(\''.$CFG_YAMAAPI->clientcontext.'/course.php?course='.$courseid.'&amp;user='.$userid.'\')">Load Current Course</button>';

}
else{
echo '<button name="load_c" id="load_c" onclick="getPage(\''.$CFG_YAMAAPI->clientcontext.'/course.php\')">Load Course</button>';
}
?>   
                        </div>
                </li>   
	</ul>
</div>
<?php if($userid>0){ ?>
<div class="block" id="logoutDiv">

<?php
echo '<button name="logout" id="logout" onclick="getPage(\''.$CFG_YAMAAPI->clientcontext.'/logout.php\')">Logout</button>';
?>

</div><!-- logout block -->

<?php }else{?>
<div class="block" id='loginDiv'>
	<div class="block_head">Login</div>
	<div>
        <form id="user_form" action="user.php" method="POST">
	
	   <div class="form_item">
        	<label>Username</label>
        	<input type="text" name="username" id="username" value="" />
	   </div>
	   <div class="form_item">
	        <label>Password</label>
	        <input type="password" name="pass" id="pass" value="" />
	   </div>
           <div class="form_item">
        	<input type="submit" name="submit" value="login" />
	   </div>
	
        </form>
	</div>

</div> <!-- Login block -->
<?php } ?>
<div id="olcontent" class="overlay-bg">
</div>
