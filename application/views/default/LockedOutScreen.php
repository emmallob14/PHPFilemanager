<?php
#FETCH SOME GLOBAL FUNCTIONS
global $SITEURL, $config, $session, $admin_user, $directory;
#REDIREC THE USER IF NOT LOGGED IN
if(!$admin_user->logged_InControlled()) {
	require "Login.php";
	exit(-1);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Lockscreen: <?php print config_item('site_name'); ?></title><meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/bootstrap.min.css" />
<meta name="author" content="<?php print config_item('developer'); ?>">
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="<?php print $config->base_url(); ?>assets/css/matrix-login.css" />
<link href="<?php print $config->base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
<meta name="pageurl" id="pageurl" value="<?php print $config->base_url(); ?>" content="<?php print $config->base_url(); ?>">
</head>
<body style="height:500px;">
	<div id="loginbox">            
		<form id="loginForm" method="POST" class="form-vertical" action="<?php print $config->base_url(); ?>doAuth/doUnlock">
			<div class="control-group normal_text"> <h3><?php print config_item('site_name'); ?></h3></div>
			<span class="alert alert-success" style="width:100%">Hello <strong><?php print $admin_user->return_fullname(); ?></strong>, you have been locked out; enter your password to continue.</span>
			<div class="control-group">
				<div class="controls">
					<div class="main_input_box">
						<span class="add-on bg_ly"><i class="icon-lock"></i></span><input type="password" placeholder="Password"  name="lock_password" id="password"/>
					</div>
				</div>
			</div>
			<input type="hidden" id="unlock_screen" name="unlock_screen">
			<?php  PRINT (!confirm_url_id(0, 'Login')) ? "<input name=\"href\" value=\"".current_url()."\" type=\"hidden\" readonly>" : ""; ?>
			<input type="hidden" id="change_password_first" name="change_password_first">
			<input type="hidden" id="change_password" name="change_password">
			<div class="form-actions">
				<span class="pull-left"><button class="btn btn-danger" onclick="javascript:window.location.href='<?php print $config->base_url(); ?>Login/Logout'"><i class="icon icon-key"></i> Logout</button></span>
				<span class="pull-right"><button id="submitButton" type="submit" class="btn btn-success" ><i class="icon icon-unlock"></i> Unlock Screen</button></span>
			</div>
		</form>
		<div id="formResult"></div>
	</div>
	
	<script src="<?php print $config->base_url(); ?>assets/js/jquery.min.js"></script>  
	<script src="<?php print $config->base_url(); ?>assets/js/matrix.login.js"></script>
	<script src="<?php print $config->base_url(); ?>assets/js/matrix.script.js"></script>
</body>
</html>