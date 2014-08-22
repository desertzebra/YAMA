<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type" />
<meta content="utf-8" http-equiv="encoding" />
<?php
        require_once('/var/www/yama/client/config_client.php');
        ini_set('display_errors', '1');
        error_reporting(E_ALL);
        $path = $CFG_YAMAAPI->yamadir;
        set_include_path(get_include_path() . PATH_SEPARATOR . $path);
        require_once $CFG_YAMAAPI->yamadir."/Init.php";
        include $CFG_YAMAAPI->clientdir."/yamalib.php";
        global $init;

?>
<link rel="stylesheet" type="text/css"
        href='<?php print $CFG_YAMAAPI->clientcontext; ?>/css/default.css' />
<link rel="stylesheet" type="text/css"
        href='<?php print $CFG_YAMAAPI->clientcontext; ?>/css/jquery-ui-themes-1.10.4/themes/smoothness/jquery-ui.css' />

<script type="text/javascript">

/* 
 * Global Variables 
 */
var isloading=false;
var CONTEXT_ROOT='<?php print $CFG_YAMAAPI->clientcontext; ?>';


</script>

<script type="text/javascript" src='<?php print $CFG_YAMAAPI->clientcontext; ?>/js/jquery-1.9.1.js'></script>
<script type="text/javascript" src='<?php print $CFG_YAMAAPI->clientcontext; ?>/js/jquery-ui-1.10.4.js'></script>
<script type="text/javascript" src='<?php print $CFG_YAMAAPI->clientcontext; ?>/js/main.js'></script>

</head>
<body>
	<div class="menu" id="menu">
		<ul>
			<li><button name="home" id="home" onclick="getPage('/')">
					<img src='<?php print $CFG_YAMAAPI->clientcontext; ?>/images/icons/home_icon_small.png'></img><br />
					HOME
				</button></li>
		</ul>
	</div>
	<div id="notice">
		<div id="notice-info" class="notice-info">Info message</div>
		<div id="notice-success" class="notice-success">Successful
			operation message</div>
		<div id="notice-warning" class="notice-warning">Warning message</div>
		<div id="notice-error" class="notice-error">
		</div>
	</div>
	<div id="overlay" class="loading"></div>
	<div class="left" id="left">
		<?php include "$CFG_YAMAAPI->clientdir/common/left_menu.php"; ?>
	</div>
