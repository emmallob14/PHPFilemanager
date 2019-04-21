<?php
#FETCH SOME GLOBAL FUNCTIONS
global $SITEURL, $config, $session, $admin_user, $directory;
#REDIREC THE USER IF NOT LOGGED IN
if(!$admin_user->logged_InControlled()) {
	require "Login.php";
	exit(-1);
}
if($admin_user->changed_password_first()) { 
	header("Location: ".$config->base_url()."Dashboard");
	exit(-1);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Change Default Password: <?php print config_item('site_name'); ?></title><meta charset="UTF-8" />
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
		<form id="loginForm" method="POST" class="form-vertical" action="<?php print $config->base_url(); ?>doAuth/doFirstChange">
			<div class="control-group normal_text"> <h3><?php print config_item('site_name'); ?></h3></div>
			<span class="alert alert-success" style="width:100%">Hello <strong><?php print $admin_user->return_fullname(); ?></strong>, you have to change the default password  to continue.</span>
			<div class="control-group">
				<div class="controls">
					<div class="main_input_box">
						<span class="add-on bg_ly"><i class="icon-lock"></i></span><input type="password" placeholder="Password"  name="password" id="password"/>
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<div class="main_input_box">
						<span class="add-on bg_ly"><i class="icon-lock"></i></span><input type="password" placeholder="Confirm Password"  name="password1" id="password1"/>
					</div>
				</div>
			</div>
			<?php  PRINT (!confirm_url_id(0, 'Login')) ? "<input name=\"href\" value=\"".current_url()."\" type=\"hidden\" readonly>" : ""; ?>
			<input type="hidden" id="change_password_first" name="change_password_first">
			<input type="hidden" id="change_password" name="change_password">
			<div class="form-actions">
				<span class="pull-right"><button id="submitButton" type="submit" class="btn btn-success" > Change Password</button></span>
			</div>
		</form>
		<div id="formResult"></div>
	</div>
	
	<script src="<?php print $config->base_url(); ?>assets/js/jquery.min.js"></script>  
	<script src="<?php print $config->base_url(); ?>assets/js/matrix.login.js"></script>
	<script src="<?php print $config->base_url(); ?>assets/js/matrix.script.js"></script>
</body>
</html>