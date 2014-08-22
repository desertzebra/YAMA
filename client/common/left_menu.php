<?php
        $courseid = (isset($_GET['course']))?$_GET['course']:'';
        $userid = (isset($_GET['user']))?$_GET['user']:0;
?>

<div class="block" id='nav_opts'>
	<div class="block_head">Navigation Options</div>
	<ul>
		<li><div class="opts">
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
		<li><div class="opts">
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
<div class="block" id='login'>

</div>
